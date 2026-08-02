<?php

namespace App\Http\Controllers\API\V1\Researcher;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Services\API\V1\Payment\MoyasarPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpportunityEnrollmentController extends Controller
{
    protected MoyasarPaymentService $moyasarPaymentService;

    public function __construct(MoyasarPaymentService $moyasarPaymentService)
    {
        $this->moyasarPaymentService = $moyasarPaymentService;
    }

    /**
     * POST /api/researcher/opportunities/{id}/enroll
     * Researcher pays directly to enroll in an opportunity.
     * If price = 0, enroll immediately without payment.
     */
    public function enroll(Request $request, string $id)
    {
        $request->validate([
            'callback_url' => ['required_unless:free,true', 'url', 'nullable'],
            'source'       => ['required_unless:free,true', 'array', 'nullable'],
        ]);

        $user = auth('api')->user();
        $opportunity = Opportunity::findOrFail($id);

        // Check opportunity is active
        if ($opportunity->status !== 'active') {
            return Helper::jsonErrorResponse('This opportunity is not currently accepting enrollments.', 422);
        }

        // Check seat availability
        if ($opportunity->total_seats !== null && $opportunity->available_seats <= 0) {
            return Helper::jsonErrorResponse('No seats available for this opportunity.', 422);
        }

        // Check if already enrolled (has a paid payment OR already in chat)
        $alreadyPaid = Payment::where('user_id', $user->id)
            ->where('payable_type', Opportunity::class)
            ->where('payable_id', $opportunity->id)
            ->where('status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return Helper::jsonErrorResponse('You are already enrolled in this opportunity.', 422);
        }

        // Check pending payment (already initiated)
        $pendingPayment = Payment::where('user_id', $user->id)
            ->where('payable_type', Opportunity::class)
            ->where('payable_id', $opportunity->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingPayment && !$request->has('source')) {
            return Helper::jsonResponse(true, 'You have a pending enrollment payment.', 200, [
                'status'  => 'pending',
                'payment' => $pendingPayment,
                'message' => 'Payment is being processed. Please wait or complete the payment.',
            ]);
        }

        // --- FREE OPPORTUNITY: direct enroll without payment ---
        if ((float) $opportunity->price === 0.0 || $opportunity->price === null) {
            return $this->enrollFreeOpportunity($user, $opportunity);
        }

        // --- PAID OPPORTUNITY: create Moyasar payment ---
        $request->validate([
            'callback_url' => ['required', 'url'],
            'source'       => ['required', 'array'],
        ]);

        try {
            $metadata = [
                'user_id'      => $user->id,
                'payable_id'   => $opportunity->id,
                'payable_type' => Opportunity::class,
            ];

            $description = "Enrollment fee for: {$opportunity->title}";

            $moyasarPayment = $this->moyasarPaymentService->createPayment(
                (float) $opportunity->price,
                'SAR',
                $description,
                $request->input('callback_url'),
                $request->input('source'),
                $metadata
            );

            $payment = Payment::create([
                'user_id'            => $user->id,
                'payable_type'       => Opportunity::class,
                'payable_id'         => $opportunity->id,
                'amount'             => (float) $opportunity->price,
                'payment_gateway'    => 'moyasar',
                'payment_gateway_id' => $moyasarPayment['id'],
                'status'             => 'pending',
            ]);

            Log::info("[OpportunityEnrollment] Payment created for user {$user->id} on opportunity {$opportunity->id}");

            return Helper::jsonResponse(true, 'Payment initiated. Awaiting confirmation.', 201, [
                'status'          => 'pending',
                'payment'         => $payment,
                'moyasar_payment' => $moyasarPayment,
            ]);
        } catch (Exception $e) {
            Log::error("[OpportunityEnrollment] Payment creation failed: " . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to initiate enrollment payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/researcher/opportunities/{id}/enrollment-status
     * Check enrollment status for a researcher on an opportunity.
     */
    public function status(string $id)
    {
        $user = auth('api')->user();
        $opportunity = Opportunity::findOrFail($id);

        // Check if in chat group (enrolled)
        $chat = $opportunity->chat;
        if ($chat) {
            $inChat = ChatUser::where('chat_id', $chat->id)
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if ($inChat) {
                return Helper::jsonResponse(true, 'You are enrolled in this opportunity.', 200, [
                    'status'  => 'enrolled',
                    'chat_id' => $chat->id,
                ]);
            }
        }

        // Check payment status
        $payment = Payment::where('user_id', $user->id)
            ->where('payable_type', Opportunity::class)
            ->where('payable_id', $opportunity->id)
            ->latest()
            ->first();

        if (!$payment) {
            return Helper::jsonResponse(true, 'Not enrolled.', 200, [
                'status'  => 'not_enrolled',
                'chat_id' => null,
            ]);
        }

        if ($payment->status === 'pending') {
            return Helper::jsonResponse(true, 'Enrollment payment is pending.', 200, [
                'status'   => 'pending',
                'payment'  => $payment,
                'chat_id'  => null,
                'message'  => 'Your payment is being processed. You will be added to the group once confirmed.',
            ]);
        }

        if (in_array($payment->status, ['failed', 'cancelled', 'refunded'])) {
            return Helper::jsonResponse(true, 'Enrollment payment failed or was cancelled.', 200, [
                'status'   => $payment->status,
                'payment'  => $payment,
                'chat_id'  => null,
            ]);
        }

        return Helper::jsonResponse(true, 'Enrollment status checked.', 200, [
            'status'  => 'not_enrolled',
            'chat_id' => null,
        ]);
    }

    /**
     * Directly enroll researcher in a free opportunity (no payment needed).
     */
    protected function enrollFreeOpportunity($user, Opportunity $opportunity)
    {
        try {
            DB::transaction(function () use ($user, $opportunity) {
                // Find or create chat for this opportunity
                $chat = $opportunity->chat;
                if (!$chat) {
                    $chat = Chat::create([
                        'chatable_type' => Opportunity::class,
                        'chatable_id'   => $opportunity->id,
                        'type'          => 'group',
                        'status'        => 'active',
                    ]);
                }

                // Add to chat_users if not already there
                ChatUser::firstOrCreate(
                    ['chat_id' => $chat->id, 'user_id' => $user->id],
                    ['role' => 'researcher', 'status' => 'active']
                );

                // Decrement available_seats atomically
                if ($opportunity->total_seats !== null) {
                    Opportunity::where('id', $opportunity->id)
                        ->where('available_seats', '>', 0)
                        ->decrement('available_seats');
                }
            });

            $chat = $opportunity->fresh()->chat;

            Log::info("[OpportunityEnrollment] Free enrollment: user {$user->id} added to opportunity {$opportunity->id} chat");

            return Helper::jsonResponse(true, 'Successfully enrolled in this opportunity.', 200, [
                'status'  => 'enrolled',
                'chat_id' => $chat?->id,
            ]);
        } catch (Exception $e) {
            Log::error("[OpportunityEnrollment] Free enrollment failed: " . $e->getMessage());
            return Helper::jsonErrorResponse('Enrollment failed: ' . $e->getMessage(), 500);
        }
    }
}
