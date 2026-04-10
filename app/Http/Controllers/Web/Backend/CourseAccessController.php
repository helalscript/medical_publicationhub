<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Services\CourseAccessService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseAccessController extends Controller
{
    public function index()
    {
        $courses = Course::query()->orderBy('title')->get(['id', 'title']);
        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('backend.layouts.course_access.index', compact('courses', 'users'));
    }

    public function grant(Request $request, CourseAccessService $courseAccessService)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'access_type' => ['required', 'in:paid,admin_grant,free'],
            'expire_at' => ['nullable', 'date'],
        ]);

        try {
            $expire = ! empty($validated['expire_at']) ? Carbon::parse($validated['expire_at']) : null;
            $courseAccessService->grantCourseAccess((int) $validated['user_id'], (int) $validated['course_id'], $validated['access_type'], $expire, 'active');

            return redirect()->back()->with('success', 'Course access granted.');
        } catch (Exception $e) {
            Log::error('CourseAccessController::grant '.$e->getMessage());
            return redirect()->back()->with('error', 'Grant failed.');
        }
    }

    public function revoke(Request $request, CourseAccessService $courseAccessService)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        try {
            $courseAccessService->grantCourseAccess((int) $validated['user_id'], (int) $validated['course_id'], 'admin_grant', null, 'inactive');
            return redirect()->back()->with('success', 'Course access revoked (inactive).');
        } catch (Exception $e) {
            Log::error('CourseAccessController::revoke '.$e->getMessage());
            return redirect()->back()->with('error', 'Revoke failed.');
        }
    }
}
