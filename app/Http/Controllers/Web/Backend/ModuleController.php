<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                    $s = '<div class="form-check form-switch">';
                    $s .= '<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>';
                    $s .= '</div>';
                    return $s;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('admin.lessons.index', ['module_id' => $row->id]).'" class="ps-0 border-0 bg-transparent lh-1" title="Lessons"><i class="material-symbols-outlined fs-16 text-primary">menu_book</i></a>
                        <a type="button" href="'.route('admin.modules.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
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
            Module::findOrFail($id)->delete();
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
            'video' => ['nullable', 'string', 'max:255'],
            'attached_file' => ['nullable', 'string', 'max:255'],
            'attached_text' => ['nullable', 'string'],
            'document' => ['nullable', 'string', 'max:255'],
            'order_by' => ['nullable', 'integer', 'min:0'],
            'durations' => ['nullable', 'string', 'max:255'],
            'is_locked' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $validated['is_locked'] = (bool) ($validated['is_locked'] ?? false);
        return $validated;
    }
}
