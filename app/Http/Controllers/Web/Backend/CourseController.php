<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Course::query()->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('is_future_badge', function ($row) {
                    $c = $row->is_future ? 'info' : 'secondary';
                    $t = $row->is_future ? 'Yes' : 'No';

                    return '<span class="badge bg-'.$c.'">'.$t.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $s = '<div class="form-check form-switch">';
                    $s .= '<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>';
                    $s .= '</div>';

                    return $s;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('admin.courses.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>
                        <a type="button" href="'.route('admin.courses.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action', 'is_future_badge'])
                ->make(true);
        }

        return view('backend.layouts.courses.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        try {
            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = Helper::fileUpload($request->file('thumbnail'), 'courses/thumbnail', time().'_'.getFileName($request->file('thumbnail')));
            }

            Course::create($validated);

            return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
        } catch (Exception $e) {
            Log::error('CourseController::store '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Course create failed.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Course::with(['modules.lessons'])->findOrFail($id);
        return view('backend.layouts.courses.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Course::findOrFail($id);
        return view('backend.layouts.courses.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePayload($request, $id);

        try {
            $course = Course::findOrFail($id);

            if ($request->hasFile('thumbnail')) {
                if (! empty($course->thumbnail)) {
                    Helper::fileDelete(public_path($course->thumbnail));
                }
                $validated['thumbnail'] = Helper::fileUpload($request->file('thumbnail'), 'courses/thumbnail', time().'_'.getFileName($request->file('thumbnail')));
            }

            $course->update($validated);

            return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
        } catch (Exception $e) {
            Log::error('CourseController::update '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Course update failed.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $course = Course::findOrFail($id);
            if (! empty($course->thumbnail)) {
                Helper::fileDelete(public_path($course->thumbnail));
            }
            $course->delete();

            return response()->json(['success' => true, 'message' => 'Course deleted successfully.']);
        } catch (Exception $e) {
            Log::error('CourseController::destroy '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Course delete failed.'], 500);
        }
    }

    public function status(Request $request, int $id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->status = $course->status === 'active' ? 'inactive' : 'active';
            $course->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (Exception $e) {
            Log::error('CourseController::status '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Status update failed.'], 500);
        }
    }

    private function validatePayload(Request $request, ?string $id = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'thumbnail' => [$id ? 'nullable' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'thumbnail_description' => ['nullable', 'string', 'max:255'],
            'thumbnail_button_text' => ['nullable', 'string', 'max:255'],
            'intro_video' => ['nullable', 'string', 'max:255'],
            'total_durations' => ['nullable', 'string', 'max:255'],
            'start_module' => ['nullable', 'integer', 'min:1'],
            'is_future' => ['nullable', 'boolean'],
            'category_ids' => ['nullable'],
            'mentor_ids' => ['nullable'],
            'descriptions' => ['nullable'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        // arrays/json handling
        $validated['is_future'] = (bool) ($validated['is_future'] ?? false);

        foreach (['category_ids', 'mentor_ids'] as $k) {
            if (isset($validated[$k]) && is_string($validated[$k])) {
                $decoded = json_decode($validated[$k], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $validated[$k] = $decoded;
                }
            }
        }

        if (isset($validated['descriptions']) && is_string($validated['descriptions'])) {
            $decoded = json_decode($validated['descriptions'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['descriptions'] = $decoded;
            }
        }

        return $validated;
    }
}
