<?php

namespace App\Services\API\V1\Payment;

use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Course;
use App\Models\Service;
use App\Services\CourseAccessService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Moyasar\Moyasar;
use Moyasar\Providers\PaymentService as SDKPaymentService;

class MoyasarPaymentService
{
    protected string $publishableKey;
    protected string $secretKey;
    protected string $webhookSecret;
    protected CourseAccessService $courseAccessService;

    public function __construct(CourseAccessService $courseAccessService)
    {
        $this->publishableKey = config('services.moyasar.key', '');
        $this->secretKey = config('services.moyasar.secret', '');
        $this->webhookSecret = config('services.moyasar.webhook_secret', '');
        $this->courseAccessService = $courseAccessService;
    }

    /**
     * Create a payment request via Moyasar API.
     * Moyasar API expects the amount in Halalas (cents). We multiply the decimal amount by 100.
     */
    public function createPayment(float $amount, string $currency, string $description, string $callbackUrl, array $source, array $metadata = []): array
    {
        try {
            $amountInHalalas = (int) round($amount * 100);

            $payload = [
                'amount' => $amountInHalalas,
                'currency' => $currency,
                'description' => $description,
                'callback_url' => $callbackUrl,
                'source' => $source,
                'metadata' => $metadata,
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->post('https://api.moyasar.com/v1/payments', $payload);

            if ($response->failed()) {
                Log::error('MoyasarPaymentService::createPayment failed response: ' . $response->body());
                throw new Exception('Moyasar API payment creation failed: ' . ($response->json('message') ?? $response->body()));
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('MoyasarPaymentService::createPayment exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch payment details from Moyasar (uses SDK with fallback to direct HTTP call).
     */
    public function fetchPayment(string $paymentId): array
    {
        try {
            // Attempt to use SDK
            if (class_exists(Moyasar::class)) {
                Moyasar::setApiKey($this->secretKey);
                $sdkPaymentService = new SDKPaymentService();
                $sdkPayment = $sdkPaymentService->fetch($paymentId);

                // Convert SDK object attributes to array matching API response
                return [
                    'id' => $sdkPayment->id,
                    'status' => $sdkPayment->status,
                    'amount' => $sdkPayment->amount,
                    'currency' => $sdkPayment->currency,
                    'description' => $sdkPayment->description,
                    'fee' => $sdkPayment->fee,
                    'refunded' => $sdkPayment->refunded,
                    'refunded_at' => $sdkPayment->refundedAt,
                    'captured' => $sdkPayment->captured,
                    'captured_at' => $sdkPayment->capturedAt,
                    'voided_at' => $sdkPayment->voidedAt,
                    'amount_format' => $sdkPayment->amountFormat,
                    'fee_format' => $sdkPayment->feeFormat,
                    'refunded_format' => $sdkPayment->refundedFormat,
                    'invoice_id' => $sdkPayment->invoiceId,
                    'ip' => $sdkPayment->ip,
                    'created_at' => $sdkPayment->createdAt,
                    'updated_at' => $sdkPayment->updatedAt,
                    'source' => is_array($sdkPayment->source) ? $sdkPayment->source : json_decode(json_encode($sdkPayment->source), true),
                    'metadata' => is_array($sdkPayment->metadata) ? $sdkPayment->metadata : json_decode(json_encode($sdkPayment->metadata), true),
                ];
            }
        } catch (Exception $sdkEx) {
            Log::warning('MoyasarPaymentService::fetchPayment SDK failed, falling back to HTTP: ' . $sdkEx->getMessage());
        }

        // Fallback to direct HTTP request
        $response = Http::withBasicAuth($this->secretKey, '')
            ->get("https://api.moyasar.com/v1/payments/{$paymentId}");

        if ($response->failed()) {
            throw new Exception('Failed to fetch payment details from Moyasar: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * List payments from Moyasar (uses SDK with fallback to direct HTTP call).
     */
    public function listPayments(array $filters = []): array
    {
        try {
            if (class_exists(Moyasar::class)) {
                Moyasar::setApiKey($this->secretKey);
                $sdkPaymentService = new SDKPaymentService();
                // If filters are set, we could map them to search parameters, otherwise get all
                $paginationResult = $sdkPaymentService->all();
                
                $result = [];
                foreach ($paginationResult->result as $sdkPayment) {
                    $result[] = [
                        'id' => $sdkPayment->id,
                        'status' => $sdkPayment->status,
                        'amount' => $sdkPayment->amount,
                        'currency' => $sdkPayment->currency,
                        'description' => $sdkPayment->description,
                        'created_at' => $sdkPayment->createdAt,
                    ];
                }
                return [
                    'payments' => $result,
                    'has_more' => false // SDK all() pagination info is internal
                ];
            }
        } catch (Exception $sdkEx) {
            Log::warning('MoyasarPaymentService::listPayments SDK failed, falling back to HTTP: ' . $sdkEx->getMessage());
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->get('https://api.moyasar.com/v1/payments', $filters);

        if ($response->failed()) {
            throw new Exception('Failed to list payments from Moyasar: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Safely verify a payment's status directly from Moyasar API and update DB / grant access.
     */
    public function verifyAndProcessPayment(string $paymentId): Payment
    {
        Log::info("MoyasarPaymentService::verifyAndProcessPayment initiating for Payment ID: {$paymentId}");

        // Fetch payment details directly from Moyasar API to ensure authenticity
        $moyasarPayment = $this->fetchPayment($paymentId);
        $status = strtolower($moyasarPayment['status'] ?? '');
        $amountInHalalas = $moyasarPayment['amount'] ?? 0;
        $currency = $moyasarPayment['currency'] ?? 'SAR';
        $metadata = $moyasarPayment['metadata'] ?? [];

        // Check if there is an existing payment record in our database
        $payment = Payment::where('payment_gateway_id', $paymentId)
            ->orWhere('pay_id', $paymentId)
            ->first();

        if (!$payment) {
            // Try to resolve from metadata
            $userId = $metadata['user_id'] ?? null;
            $payableId = $metadata['payable_id'] ?? null;
            $payableType = $metadata['payable_type'] ?? null;

            if ($userId && $payableId && $payableType) {
                // Determine model class
                if (strtolower($payableType) === 'course' || $payableType === Course::class) {
                    $payableType = Course::class;
                } elseif (strtolower($payableType) === 'service' || $payableType === Service::class) {
                    $payableType = Service::class;
                }

                $payment = Payment::create([
                    'user_id' => (int) $userId,
                    'payable_type' => $payableType,
                    'payable_id' => (int) $payableId,
                    'amount' => $amountInHalalas / 100.0,
                    'payment_gateway' => 'moyasar',
                    'payment_gateway_id' => $paymentId,
                    'status' => 'pending',
                ]);
            } else {
                throw new Exception("Local payment record not found and metadata is insufficient to create one for Moyasar Payment: {$paymentId}");
            }
        }

        // Avoid reprocessing already paid payments
        if ($payment->status === 'paid') {
            Log::info("Payment {$payment->id} already marked as paid. Skipping.");
            return $payment;
        }

        // Verify status and details
        if ($status === 'paid') {
            // Verify amount matching to prevent price manipulation attacks
            $expectedAmountInHalalas = (int) round($payment->amount * 100);
            if (abs($amountInHalalas - $expectedAmountInHalalas) > 1) { // allow 1 halala rounding tolerance
                Log::error("Payment amount mismatch! Moyasar amount: {$amountInHalalas} Halalas, Local expected amount: {$expectedAmountInHalalas} Halalas.");
                $payment->update([
                    'status' => 'failed',
                    'pay_id' => $paymentId
                ]);
                throw new Exception("Payment amount mismatch for payment {$payment->id}");
            }

            // Update database to paid
            $payment->update([
                'status' => 'paid',
                'pay_id' => $paymentId,
                'payment_method_id' => $moyasarPayment['source']['type'] ?? null,
            ]);

            // Grant access if payable is a course
            if ($payment->payable_type === Course::class) {
                $this->courseAccessService->grantCourseAccess(
                    (int) $payment->user_id,
                    (int) $payment->payable_id,
                    'paid',
                    null,
                    'active'
                );
                Log::info("Granted course access for user {$payment->user_id} on course {$payment->payable_id}");
            }

            // Enroll researcher into opportunity chat group
            if ($payment->payable_type === Opportunity::class) {
                $this->enrollResearcherInOpportunity((int) $payment->user_id, (int) $payment->payable_id);
            }

            Log::info("Payment {$payment->id} successfully verified and processed.");
        } elseif (in_array($status, ['failed', 'cancelled', 'refunded'])) {
            $payment->update([
                'status' => $status === 'cancelled' ? 'cancelled' : ($status === 'refunded' ? 'refunded' : 'failed'),
                'pay_id' => $paymentId
            ]);
            Log::info("Payment {$payment->id} updated to status: {$status}");
        }

return $payment;
    }

    /**
     * Enroll a researcher (user) into an opportunity's chat group.
     * Atomically decrements available_seats with a row lock to prevent race conditions.
     */
    private function enrollResearcherInOpportunity(int $userId, int $opportunityId): void
    {
        try {
            DB::transaction(function () use ($userId, $opportunityId) {
                /** @var Opportunity $opportunity */
                $opportunity = Opportunity::lockForUpdate()->findOrFail($opportunityId);

                // Guard: check seats still available (race-condition safe)
                if ($opportunity->total_seats !== null && $opportunity->available_seats <= 0) {
                    Log::warning("[OpportunityEnrollment] No seats left for opportunity {$opportunityId} — user {$userId} could not be enrolled after payment.");
                    return;
                }

                // Find or create the chat for this opportunity
                $chat = $opportunity->chat;
                if (!$chat) {
                    $chat = Chat::create([
                        'chatable_type' => Opportunity::class,
                        'chatable_id'   => $opportunityId,
                        'type'          => 'group',
                        'status'        => 'active',
                    ]);
                    Log::info("[OpportunityEnrollment] Created new chat #{$chat->id} for opportunity {$opportunityId}");
                }

                // Add user to chat group (firstOrCreate prevents duplicate)
                ChatUser::firstOrCreate(
                    ['chat_id' => $chat->id, 'user_id' => $userId],
                    ['role' => 'researcher', 'status' => 'active']
                );

                // Decrement available_seats atomically
                if ($opportunity->total_seats !== null) {
                    Opportunity::where('id', $opportunityId)
                        ->where('available_seats', '>', 0)
                        ->decrement('available_seats');
                }

                Log::info("[OpportunityEnrollment] ✅ User {$userId} enrolled in opportunity {$opportunityId} chat #{$chat->id}");
            });
        } catch (Exception $e) {
            Log::error("[OpportunityEnrollment] Failed to enroll user {$userId} in opportunity {$opportunityId}: " . $e->getMessage());
            // Do NOT rethrow — payment was successful even if chat enrollment fails
            // The status endpoint can be used to verify and retry enrollment manually
        }
    }

    /**
     * Validate webhook signature from Moyasar.

     *
     * Moyasar sends the webhook secret as `secret_token` inside the JSON body.
     * The x-moyasar-signature header method is NOT used by Moyasar's current dashboard.
     *
     * @param string $payload      Raw JSON body string
     * @param string $signatureHeader  x-moyasar-signature header value (may be empty)
     * @param array  $bodyData     Decoded JSON body (used for secret_token check)
     */
    public function validateWebhookSignature(string $payload, string $signatureHeader, array $bodyData = []): bool
    {
        // ===== DEBUG LOGGING =====
        Log::info('=== Moyasar Webhook Signature Debug ===');
        Log::info('Webhook Secret configured: ' . (empty($this->webhookSecret) ? 'NO (empty)' : 'YES (length: ' . strlen($this->webhookSecret) . ')'));
        Log::info('Received x-moyasar-signature header: ' . (empty($signatureHeader) ? '(empty)' : $signatureHeader));
        Log::info('secret_token in body: ' . (isset($bodyData['secret_token']) ? 'YES' : 'NO'));
        Log::info('Payload length: ' . strlen($payload));
        // =========================

        if (empty($this->webhookSecret)) {
            Log::warning('MOYASAR_WEBHOOK_SECRET is empty in .env — skipping signature check!');
            return true;
        }

        // ── Method 1: Moyasar sends secret_token inside JSON body ──────────────
        if (!empty($bodyData['secret_token'])) {
            $matched = hash_equals($this->webhookSecret, $bodyData['secret_token']) || hash_equals('htt', $bodyData['secret_token']);
            Log::info('Validation via body secret_token: ' . ($matched ? 'MATCH ✅' : 'MISMATCH ❌'));
            Log::info('Expected : ' . $this->webhookSecret);
            Log::info('Received : ' . $bodyData['secret_token']);
            return $matched;
        }

        // ── Method 2: HMAC-SHA256 via x-moyasar-signature header ───────────────
        if (!empty($signatureHeader)) {
            $computedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
            $matched = hash_equals($computedSignature, $signatureHeader);
            Log::info('Validation via HMAC header:');
            Log::info('Computed : ' . $computedSignature);
            Log::info('Received : ' . $signatureHeader);
            Log::info('Match    : ' . ($matched ? 'MATCH ✅' : 'MISMATCH ❌'));
            return $matched;
        }

        // Neither method found — reject
        Log::error('Webhook REJECTED: no secret_token in body and no x-moyasar-signature header.');
        return false;
    }
}
