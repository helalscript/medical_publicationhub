<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                    $s = '<div class="form-check form-switch">';
                    $s .= '<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>';
                    $s .= '</div>';
                    return $s;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('admin.lessons.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
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
            Lesson::findOrFail($id)->delete();
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
            'file' => ['nullable', 'string', 'max:255'],
            'video' => ['nullable', 'string', 'max:255'],
            'attached_text' => ['nullable', 'string'],
            'attached_file' => ['nullable', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:255'],
            'order_by' => ['nullable', 'integer', 'min:0'],
            'durations' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
    }
}
