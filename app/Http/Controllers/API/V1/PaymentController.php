<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Service;
use App\Models\Payment;
use App\Services\API\V1\Payment\MoyasarPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected MoyasarPaymentService $moyasarPaymentService;

    public function __construct(MoyasarPaymentService $moyasarPaymentService)
    {
        $this->moyasarPaymentService = $moyasarPaymentService;
    }

    /**
     * Create a payment request.
     */
    public function create(Request $request)
    {
        $request->validate([
            'payable_type' => ['required', 'string', 'in:course,Course,App\\Models\\Course,service,Service,App\\Models\\Service'],
            'payable_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'description' => ['required', 'string', 'max:255'],
            'callback_url' => ['required', 'url'],
            'source' => ['required', 'array'],
        ]);

        try {
            $user = auth('api')->user();
            $payableId = $request->input('payable_id');
            $payableType = $request->input('payable_type');

            // Resolve proper payable class name
            if (in_array(strtolower($payableType), ['course', 'app\\models\\course'])) {
                $payableType = Course::class;
            } elseif (in_array(strtolower($payableType), ['service', 'app\\models\\service'])) {
                $payableType = Service::class;
            }

            // Confirm that the payable model exists
            $payable = $payableType::findOrFail($payableId);

            // Double check price matches to prevent client side price manipulation
            if ($payableType === Course::class) {
                if ((float) $payable->price != (float) $request->input('amount')) {
                    return Helper::jsonErrorResponse('Invalid payment amount for the selected course.', 422);
                }
            } elseif ($payableType === Service::class) {
                if ((float) $payable->starting_price != (float) $request->input('amount')) {
                    return Helper::jsonErrorResponse('Invalid payment amount for the selected service.', 422);
                }
            }

            // Add user metadata for webhook / async processing
            $metadata = [
                'user_id' => $user->id,
                'payable_id' => $payableId,
                'payable_type' => $payableType,
            ];

            // Create payment session on Moyasar
            $moyasarPayment = $this->moyasarPaymentService->createPayment(
                (float) $request->input('amount'),
                $request->input('currency'),
                $request->input('description'),
                $request->input('callback_url'),
                $request->input('source'),
                $metadata
            );

            // Create local pending payment entry
            $payment = Payment::create([
                'user_id' => $user->id,
                'payable_type' => $payableType,
                'payable_id' => $payableId,
                'amount' => (float) $request->input('amount'),
                'payment_gateway' => 'moyasar',
                'payment_gateway_id' => $moyasarPayment['id'],
                'status' => 'pending',
            ]);

            return Helper::jsonResponse(true, 'Payment request created successfully.', 201, [
                'payment' => $payment,
                'moyasar_payment' => $moyasarPayment,
            ]);
        } catch (Exception $e) {
            Log::error('PaymentController::create error: ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to create payment request: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Fetch payment details.
     */
    public function fetch(string $paymentId)
    {
        try {
            $moyasarPayment = $this->moyasarPaymentService->fetchPayment($paymentId);
            $localPayment = Payment::where('payment_gateway_id', $paymentId)
                ->orWhere('pay_id', $paymentId)
                ->first();

            return Helper::jsonResponse(true, 'Payment fetched successfully.', 200, [
                'local_payment' => $localPayment,
                'moyasar_payment' => $moyasarPayment,
            ]);
        } catch (Exception $e) {
            Log::error('PaymentController::fetch error: ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to fetch payment details: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Pay an existing payment record.
     */
    public function pay(Request $request)
    {
        $request->validate([
            'callback_url' => ['required', 'url'],
            'source'       => ['required', 'array'],
            'payment_id'   => ['required','string',''],
        ]);

        try {
            $user = auth('api')->user();

            // Find the local payment record ensuring it belongs to the user
            $payment = Payment::where('payment_gateway_id', $request->input('payment_id'))
                ->where('user_id', $user->id)
                ->firstOrFail();

            if ($payment->status === 'paid') {
                return Helper::jsonErrorResponse('This payment is already paid.', 400);
            }

            // Retrieve the payable details (course/service)
            $payable = $payment->payable;
            if (!$payable) {
                return Helper::jsonErrorResponse('The associated course or service no longer exists.', 404);
            }

            // Double-check the price hasn't changed since the payment was created
            if ($payment->payable_type === Course::class) {
                if ((float) $payable->price != (float) $payment->amount) {
                    return Helper::jsonErrorResponse('The price of the course has changed. Please create a new payment.', 422);
                }
            } elseif ($payment->payable_type === Service::class) {
                if ((float) $payable->starting_price != (float) $payment->amount) {
                    return Helper::jsonErrorResponse('The price of the service has changed. Please create a new payment.', 422);
                }
            }

            // Metadata for Moyasar
            $metadata = [
                'user_id'      => $user->id,
                'payable_id'   => $payment->payable_id,
                'payable_type' => $payment->payable_type,
            ];

            // Create payment session on Moyasar
            $description = "Payment Retry for " . class_basename($payment->payable_type) . " #{$payment->payable_id}";
            $moyasarPayment = $this->moyasarPaymentService->createPayment(
                (float) $payment->amount,
                'SAR',
                $description,
                $request->input('callback_url'),
                $request->input('source'),
                $metadata
            );

            // Update the existing local payment entry with the new gateway ID
            $payment->update([
                'payment_gateway'    => 'moyasar',
                'payment_gateway_id' => $moyasarPayment['id'],
                'status'             => 'pending',
            ]);

            return Helper::jsonResponse(true, 'Payment processed successfully.', 200, [
                'payment'         => $payment,
                'moyasar_payment' => $moyasarPayment,
            ]);
        } catch (Exception $e) {
            Log::error('PaymentController::pay error: ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to process payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * List user's payments.
     */
    public function list(Request $request)
    {
        try {
            $user = auth('api')->user();
            $payments = Payment::where('user_id', $user->id)
                ->with('payable')
                ->latest()
                ->paginate($request->input('per_page', 15));

            return Helper::jsonResponse(true, 'Payments retrieved successfully.', 200, $payments, true);
        } catch (Exception $e) {
            Log::error('PaymentController::list error: ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to retrieve payments: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Verify payment status manually.
     */
    public function verify(string $paymentId)
    {
        try {
            $payment = $this->moyasarPaymentService->verifyAndProcessPayment($paymentId);
            return Helper::jsonResponse(true, 'Payment verified successfully.', 200, [
                'payment' => $payment,
            ]);
        } catch (Exception $e) {
            Log::error('PaymentController::verify error: ' . $e->getMessage());
            return Helper::jsonErrorResponse('Payment verification failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Webhook receiver.
     */
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('x-moyasar-signature') ?? '';
        $bodyData  = json_decode($payload, true) ?? [];

        Log::info('Moyasar webhook received. Type: ' . ($bodyData['type'] ?? 'unknown'));

        // Validate webhook signature (supports both secret_token body method and HMAC header method)
        if (!$this->moyasarPaymentService->validateWebhookSignature($payload, $signature, $bodyData)) {
            Log::error('Moyasar webhook signature verification failed.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        try {
            $eventData = json_decode($payload, true);
            $eventType = $eventData['type'] ?? '';
            $paymentData = $eventData['data'] ?? [];
            $paymentId = $paymentData['id'] ?? $eventData['id'] ?? null;

            if (!$paymentId) {
                Log::error('Moyasar webhook payload missing payment ID.');
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            Log::info("Moyasar Webhook event [{$eventType}] received for payment: {$paymentId}");

            // Process the payment state change
            $this->moyasarPaymentService->verifyAndProcessPayment($paymentId);

            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            Log::error('Moyasar webhook handling exception: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
