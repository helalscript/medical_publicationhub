<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        try {
            $q = Course::query()->where('status', 'active');

            if ($request->filled('is_future')) {
                $q->where('is_future', (bool) $request->boolean('is_future'));
            }

            if ($request->filled('search')) {
                $search = (string) $request->get('search');
                $q->where('title', 'like', "%{$search}%");
            }

            $data = $q->latest()->paginate((int) ($request->get('per_page', 10)));

            return Helper::jsonResponse(true, 'Courses retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('CourseController::index '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to load courses.', 500);
        }
    }

    public function show(int $id)
    {
        try {
            $course = Course::with(['modules.lessons'])->where('status', 'active')->findOrFail($id);

            return Helper::jsonResponse(true, 'Course retrieved successfully.', 200, $course);
        } catch (Exception $e) {
            Log::error('CourseController::show '.$e->getMessage());
            return Helper::jsonErrorResponse('Course not found.', 404);
        }
    }
}
