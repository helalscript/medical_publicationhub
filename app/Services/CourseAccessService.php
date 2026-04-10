<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Payment;
use App\Models\UserCourseAccess;
use App\Models\UserModuleAccess;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class CourseAccessService
{
    /**
     * Create a payment row and (optionally) activate access.
     */
    public function createPaymentAndAccess(int $userId, Course $course, array $paymentData, string $accessType = 'paid', bool $activateAccess = false): Payment
    {
        try {
            $payment = Payment::create([
                'user_id' => $userId,
                'payable_type' => Course::class,
                'payable_id' => $course->id,
                'amount' => $paymentData['amount'] ?? (float) $course->price,
                'payment_gateway' => $paymentData['payment_gateway'] ?? null,
                'payment_gateway_id' => $paymentData['payment_gateway_id'] ?? null,
                'payment_method_id' => $paymentData['payment_method_id'] ?? null,
                'pay_id' => $paymentData['pay_id'] ?? null,
                'status' => $paymentData['status'] ?? 'pending',
            ]);

            if ($activateAccess) {
                $this->grantCourseAccess($userId, $course->id, $accessType, null, 'active');
            }

            return $payment;
        } catch (Exception $e) {
            Log::error('CourseAccessService::createPaymentAndAccess '.$e->getMessage());
            throw $e;
        }
    }

    public function grantCourseAccess(int $userId, int $courseId, string $accessType = 'admin_grant', ?Carbon $expireAt = null, string $status = 'active'): UserCourseAccess
    {
        try {
            return UserCourseAccess::updateOrCreate(
                ['user_id' => $userId, 'course_id' => $courseId],
                [
                    'access_type' => $accessType,
                    'start_at' => Carbon::now(),
                    'expire_at' => $expireAt,
                    'status' => $status,
                ]
            );
        } catch (Exception $e) {
            Log::error('CourseAccessService::grantCourseAccess '.$e->getMessage());
            throw $e;
        }
    }

    public function grantModuleAccess(int $userId, int $courseId, int $moduleId, ?Carbon $expireAt = null, string $status = 'active'): UserModuleAccess
    {
        try {
            return UserModuleAccess::updateOrCreate(
                ['user_id' => $userId, 'module_id' => $moduleId],
                ['course_id' => $courseId, 'expire_at' => $expireAt, 'status' => $status]
            );
        } catch (Exception $e) {
            Log::error('CourseAccessService::grantModuleAccess '.$e->getMessage());
            throw $e;
        }
    }
}