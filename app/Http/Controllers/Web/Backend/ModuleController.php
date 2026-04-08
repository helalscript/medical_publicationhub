<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Module::with('course')->when($request->filled('course_id'), function ($q) use ($request) {
                $q->where('course_id', (int) $request->get('course_id'));
            })->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('course_title', function ($row) {
                    return e($row->course?->title ?? '-');
                })
                ->addColumn('locked_badge', function ($row) {
                    $c = $row->is_locked ? 'warning' : 'secondary';
                    $t = $row->is_locked ? 'Locked' : 'Open';

                    return '<span class="badge bg-'.$c.'">'.$t.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.modules.status')) {
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

                    if ($user && $user->can('admin.lessons.index')) {
                        $buttons[] = '<a type="button" href="'.route('admin.lessons.index', ['module_id' => $row->id]).'" class="ps-0 border-0 bg-transparent lh-1" title="Lessons"><i class="material-symbols-outlined fs-16 text-primary">menu_book</i></a>';
                    }
                    if ($user && $user->can('admin.modules.edit')) {
                        $buttons[] = '<a type="button" href="'.route('admin.modules.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.modules.destroy')) {
                        $buttons[] = '<button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')" title="Delete"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>';
                    }

                    if (count($buttons) === 0) {
                        return '-';
                    }

                    return '<div class="action-wrapper">'.implode('', $buttons).'</div>';
                })
                ->rawColumns(['status', 'action', 'locked_badge'])
                ->make(true);
        }

        $courses = Course::query()->orderBy('title')->get(['id', 'title']);

        return view('backend.layouts.modules.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::query()->orderBy('title')->get(['id', 'title']);

        return view('backend.layouts.modules.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        try {
            if ($request->hasFile('video')) {
                $validated['video'] = $this->storePublicFile($request->file('video'), 'modules/video');
            }
            if ($request->hasFile('document')) {
                $validated['document'] = $this->storePublicFile($request->file('document'), 'modules/document');
            }
            if ($request->hasFile('attached_file')) {
                $validated['attached_file'] = $this->storePublicFile($request->file('attached_file'), 'modules/attached');
            }

            Module::create($validated);

            return redirect()->route('admin.modules.index', ['course_id' => $validated['course_id']])->with('success', 'Module created successfully.');
        } catch (Exception $e) {
            Log::error('ModuleController::store '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Module create failed.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Module::findOrFail($id);
        $courses = Course::query()->orderBy('title')->get(['id', 'title']);

        return view('backend.layouts.modules.edit', compact('data', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePayload($request, $id);
        try {
            $module = Module::findOrFail($id);

            if ($request->hasFile('video')) {
                $this->deleteIfPublicStoragePath($module->video);
                $validated['video'] = $this->storePublicFile($request->file('video'), 'modules/video');
            } else {
                unset($validated['video']);
            }

            if ($request->hasFile('document')) {
                $this->deleteIfPublicStoragePath($module->document);
                $validated['document'] = $this->storePublicFile($request->file('document'), 'modules/document');
            } else {
                unset($validated['document']);
            }

            if ($request->hasFile('attached_file')) {
                $this->deleteIfPublicStoragePath($module->attached_file);
                $validated['attached_file'] = $this->storePublicFile($request->file('attached_file'), 'modules/attached');
            } else {
                unset($validated['attached_file']);
            }

            $module->update($validated);

            return redirect()->route('admin.modules.index', ['course_id' => $module->course_id])->with('success', 'Module updated successfully.');
        } catch (Exception $e) {
            Log::error('ModuleController::update '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Module update failed.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $module = Module::findOrFail($id);
            $this->deleteIfPublicStoragePath($module->video);
            $this->deleteIfPublicStoragePath($module->document);
            $this->deleteIfPublicStoragePath($module->attached_file);
            $module->delete();

            return response()->json(['success' => true, 'message' => 'Module deleted successfully.']);
        } catch (Exception $e) {
            Log::error('ModuleController::destroy '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Module delete failed.'], 500);
        }
    }

    public function status(Request $request, int $id)
    {
        try {
            $module = Module::findOrFail($id);
            $module->status = $module->status === 'active' ? 'inactive' : 'active';
            $module->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (Exception $e) {
            Log::error('ModuleController::status '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Status update failed.'], 500);
        }
    }

    private function validatePayload(Request $request, ?string $id = null): array
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'mentor_id' => ['nullable', 'exists:mentors,id'],
            'type' => ['required', 'in:video,document,mixed'],
            'title' => ['required', 'string', 'max:255'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm,mov,avi,mkv', 'max:102400'],
            'attached_file' => ['nullable', 'file', 'max:51200'],
            'attached_text' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt', 'max:51200'],
            'order_by' => ['nullable', 'integer', 'min:0'],
            'durations' => ['nullable', 'string', 'max:255'],
            'is_locked' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $validated['is_locked'] = (bool) ($validated['is_locked'] ?? false);

        return $validated;
    }

    private function storePublicFile($file, string $dir): string
    {
        $name = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();

        return Storage::disk('public')->putFileAs($dir, $file, $name);
    }

    private function deleteIfPublicStoragePath(?string $path): void
    {
        if (! $path) {
            return;
        }

        // Only delete if it's a disk path we stored (not an external URL)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
