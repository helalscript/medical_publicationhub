<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseAccessService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoursePurchaseController extends Controller
{
    /**
     * Create a payment row and grant access (basic flow).
     * Later we can hook real gateway callbacks to flip status to "paid".
     */
    public function buy(Request $request, int $courseId, CourseAccessService $courseAccessService)
    {
        try {
            $user = auth('api')->user();

            $validated = $request->validate([
                'payment_gateway' => ['nullable', 'string', 'max:255'],
                'payment_gateway_id' => ['nullable', 'string', 'max:255'],
                'payment_method_id' => ['nullable', 'string', 'max:255'],
                'pay_id' => ['nullable', 'string', 'max:255'],
            ]);

            $course = Course::where('status', 'active')->findOrFail($courseId);

            // MVP: mark as paid immediately (no gateway integration yet)
            $payment = $courseAccessService->createPaymentAndAccess(
                (int) $user->id,
                $course,
                array_merge($validated, [
                    'amount' => (float) $course->price,
                    'status' => 'paid',
                ]),
                'paid',
                true
            );

            return Helper::jsonResponse(true, 'Course purchased successfully.', 201, [
                'payment' => $payment,
            ]);
        } catch (Exception $e) {
            Log::error('CoursePurchaseController::buy '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to purchase course.', 500);
        }
    }
}
