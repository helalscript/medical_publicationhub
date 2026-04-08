<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Mentor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.courses.status')) {
                        $c = $row->status === 'active' ? 'success' : 'secondary';
                        $t = $row->status === 'active' ? 'Active' : 'Inactive';

                        return '<span class="badge bg-'.$c.'">'.$t.'</span>';
                    }

                    return '<div class="form-check form-switch">'
                        .'<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>'
                        .'</div>';
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    $buttons = [];

                    if ($user && $user->can('admin.courses.show')) {
                        $buttons[] = '<a type="button" href="'.route('admin.courses.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="View"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>';
                    }
                    if ($user && $user->can('admin.courses.edit')) {
                        $buttons[] = '<a type="button" href="'.route('admin.courses.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.courses.destroy')) {
                        $buttons[] = '<button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')" title="Delete"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>';
                    }

                    if (count($buttons) === 0) {
                        return '-';
                    }

                    return '<div class="action-wrapper">'.implode('', $buttons).'</div>';
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
        $metaAcademyCategories = Category::query()
            ->where('type', 'meta_academy')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $mentors = Mentor::query()
            ->with('user')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('backend.layouts.courses.create', compact('metaAcademyCategories', 'mentors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        try {
            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = $this->storePublicFile($request->file('thumbnail'), 'courses/thumbnail');
            }

            if ($request->hasFile('intro_video')) {
                $validated['intro_video'] = $this->storePublicFile($request->file('intro_video'), 'courses/intro-video');
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

        $metaAcademyCategories = Category::query()
            ->where('type', 'meta_academy')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $mentors = Mentor::query()
            ->with('user')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('backend.layouts.courses.edit', compact('data', 'metaAcademyCategories', 'mentors'));
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
                $this->deleteCourseStoredFile($course->thumbnail);
                $validated['thumbnail'] = $this->storePublicFile($request->file('thumbnail'), 'courses/thumbnail');
            } else {
                unset($validated['thumbnail']);
            }

            if ($request->hasFile('intro_video')) {
                $this->deleteCourseStoredFile($course->intro_video);
                $validated['intro_video'] = $this->storePublicFile($request->file('intro_video'), 'courses/intro-video');
            } else {
                unset($validated['intro_video']);
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
            $this->deleteCourseStoredFile($course->thumbnail);
            $this->deleteCourseStoredFile($course->intro_video);
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
        foreach (['category_ids', 'mentor_ids'] as $key) {
            $raw = $request->input($key, []);
            if (is_array($raw)) {
                $raw = array_values(array_filter($raw, fn ($v) => $v !== '' && $v !== null));
                $request->merge([$key => $raw]);
            }
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'thumbnail' => [$id ? 'nullable' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'thumbnail_description' => ['nullable', 'string', 'max:255'],
            'thumbnail_button_text' => ['nullable', 'string', 'max:255'],
            'intro_video' => ['nullable', 'file', 'mimes:mp4,webm,mov,avi,mkv', 'max:102400'],
            'total_durations' => ['nullable', 'string', 'max:255'],
            'start_module' => ['nullable', 'integer', 'min:1'],
            'is_future' => ['nullable', 'boolean'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', Rule::exists('categories', 'id')->where('type', 'meta_academy')],
            'mentor_ids' => ['nullable', 'array'],
            'mentor_ids.*' => ['integer', 'exists:mentors,id'],
            'descriptions' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $validated['is_future'] = (bool) ($validated['is_future'] ?? false);

        $cat = $request->input('category_ids');
        if (! is_array($cat) || count($cat) === 0) {
            $validated['category_ids'] = null;
        } else {
            $validated['category_ids'] = array_values(array_unique(array_map('intval', $cat)));
        }

        $men = $request->input('mentor_ids');
        if (! is_array($men) || count($men) === 0) {
            $validated['mentor_ids'] = null;
        } else {
            $validated['mentor_ids'] = array_values(array_unique(array_map('intval', $men)));
        }

        if (! empty($validated['descriptions']) && is_string($validated['descriptions'])) {
            $decoded = json_decode($validated['descriptions'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                abort(422, 'Descriptions must be valid JSON.');
            }
            $validated['descriptions'] = $decoded;
        } else {
            $validated['descriptions'] = null;
        }

        return $validated;
    }

    private function storePublicFile($file, string $dir): string
    {
        $fileName = getFileName($file).'.'.$file->getClientOriginalExtension();

        return Storage::disk('public')->putFileAs($dir, $file, $fileName);
    }

    private function deleteCourseStoredFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        // Ignore external links
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        // Backward compatible: older uploads were stored in public/uploads/...
        if (str_starts_with($path, 'uploads/')) {
            Helper::fileDelete(public_path($path));

            return;
        }

        Storage::disk('public')->delete($path);
    }
}
