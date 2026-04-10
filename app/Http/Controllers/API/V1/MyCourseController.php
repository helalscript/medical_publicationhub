<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MyCourseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();

            $courseIds = $user->courseAccesses()
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('expire_at')->orWhere('expire_at', '>', now());
                })
                ->pluck('course_id');

            $data = Course::whereIn('id', $courseIds)
                ->where('status', 'active')
                ->latest()
                ->paginate((int) ($request->get('per_page', 10)));

            return Helper::jsonResponse(true, 'My courses retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('MyCourseController::index '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to load your courses.', 500);
        }
    }
}
