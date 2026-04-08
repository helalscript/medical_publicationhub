<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Lesson::with('module.course')
                ->when($request->filled('module_id'), function ($q) use ($request) {
                    $q->where('module_id', (int) $request->get('module_id'));
                })
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('course_title', function ($row) {
                    return e($row->module?->course?->title ?? '-');
                })
                ->addColumn('module_title', function ($row) {
                    return e($row->module?->title ?? '-');
                })
                ->addColumn('status', function ($row) {
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.lessons.status')) {
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

                    if ($user && $user->can('admin.lessons.edit')) {
                        $buttons[] = '<a type="button" href="'.route('admin.lessons.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.lessons.destroy')) {
                        $buttons[] = '<button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')" title="Delete"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>';
                    }

                    if (count($buttons) === 0) {
                        return '-';
                    }

                    return '<div class="action-wrapper">'.implode('', $buttons).'</div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $modules = Module::with('course')->orderBy('title')->get();

        return view('backend.layouts.lessons.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modules = Module::with('course')->orderBy('title')->get();

        return view('backend.layouts.lessons.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        try {
            if ($request->hasFile('video')) {
                $validated['video'] = $this->storePublicFile($request->file('video'), 'lessons/video');
            }
            if ($request->hasFile('document')) {
                $validated['document'] = $this->storePublicFile($request->file('document'), 'lessons/document');
            }
            if ($request->hasFile('attached_file')) {
                $validated['attached_file'] = $this->storePublicFile($request->file('attached_file'), 'lessons/attached');
            }
            if ($request->hasFile('file')) {
                $validated['file'] = $this->storePublicFile($request->file('file'), 'lessons/file');
            }

            Lesson::create($validated);

            return redirect()->route('admin.lessons.index', ['module_id' => $validated['module_id']])->with('success', 'Lesson created successfully.');
        } catch (Exception $e) {
            Log::error('LessonController::store '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Lesson create failed.');
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
        $data = Lesson::findOrFail($id);
        $modules = Module::with('course')->orderBy('title')->get();

        return view('backend.layouts.lessons.edit', compact('data', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePayload($request, $id);
        try {
            $lesson = Lesson::findOrFail($id);

            if ($request->hasFile('video')) {
                $this->deleteIfPublicStoragePath($lesson->video);
                $validated['video'] = $this->storePublicFile($request->file('video'), 'lessons/video');
            } else {
                unset($validated['video']);
            }

            if ($request->hasFile('document')) {
                $this->deleteIfPublicStoragePath($lesson->document);
                $validated['document'] = $this->storePublicFile($request->file('document'), 'lessons/document');
            } else {
                unset($validated['document']);
            }

            if ($request->hasFile('attached_file')) {
                $this->deleteIfPublicStoragePath($lesson->attached_file);
                $validated['attached_file'] = $this->storePublicFile($request->file('attached_file'), 'lessons/attached');
            } else {
                unset($validated['attached_file']);
            }

            if ($request->hasFile('file')) {
                $this->deleteIfPublicStoragePath($lesson->file);
                $validated['file'] = $this->storePublicFile($request->file('file'), 'lessons/file');
            } else {
                unset($validated['file']);
            }

            $lesson->update($validated);

            return redirect()->route('admin.lessons.index', ['module_id' => $lesson->module_id])->with('success', 'Lesson updated successfully.');
        } catch (Exception $e) {
            Log::error('LessonController::update '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Lesson update failed.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);
            $this->deleteIfPublicStoragePath($lesson->video);
            $this->deleteIfPublicStoragePath($lesson->document);
            $this->deleteIfPublicStoragePath($lesson->attached_file);
            $this->deleteIfPublicStoragePath($lesson->file);
            $lesson->delete();

            return response()->json(['success' => true, 'message' => 'Lesson deleted successfully.']);
        } catch (Exception $e) {
            Log::error('LessonController::destroy '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Lesson delete failed.'], 500);
        }
    }

    public function status(Request $request, int $id)
    {
        try {
            $lesson = Lesson::findOrFail($id);
            $lesson->status = $lesson->status === 'active' ? 'inactive' : 'active';
            $lesson->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (Exception $e) {
            Log::error('LessonController::status '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Status update failed.'], 500);
        }
    }

    private function validatePayload(Request $request, ?string $id = null): array
    {
        return $request->validate([
            'module_id' => ['required', 'exists:modules,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:51200'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm,mov,avi,mkv', 'max:102400'],
            'attached_text' => ['nullable', 'string'],
            'attached_file' => ['nullable', 'file', 'max:51200'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt', 'max:51200'],
            'order_by' => ['nullable', 'integer', 'min:0'],
            'durations' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
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

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
