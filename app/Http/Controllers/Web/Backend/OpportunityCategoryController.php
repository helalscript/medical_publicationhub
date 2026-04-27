<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class OpportunityCategoryController extends Controller
{
    public static $types = [
        'course' => 'Course',
        'speciality' => 'Speciality',
        'student_design_type' => 'Study Design Type',
        'contribution_type' => 'Contribution Type',
        'university_hospital' => 'University/Hospital',
        'place' => 'Place',
        'experience_level' => 'Experience Level',
    ];

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::query()
                ->whereIn('type', array_keys(self::$types))
                ->when($request->filled('type_filter'), fn ($q) => $q->where('type', $request->type_filter))
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('type_label', function ($row) {
                    return self::$types[$row->type] ?? $row->type;
                })
                ->addColumn('status', function ($row) {
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.opportunity_categories.status')) {
                        $c = $row->status === 'active' ? 'success' : 'secondary';
                        return '<span class="badge bg-'.$c.'">'.ucfirst($row->status).'</span>';
                    }

                    return '<div class="form-check form-switch">'
                        .'<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>'
                        .'</div>';
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    $buttons = [];

                    if ($user && $user->can('admin.opportunity_categories.edit')) {
                        $buttons[] = '<a type="button" href="'.route('admin.opportunity_categories.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.opportunity_categories.destroy')) {
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

        return view('backend.layouts.opportunity_categories.index');
    }

    public function create()
    {
        $types = self::$types;
        return view('backend.layouts.opportunity_categories.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', array_keys(self::$types)),
            'status' => 'nullable|in:active,inactive',
        ]);

        try {
            Category::create($validated);
            return redirect()->route('admin.opportunity_categories.index')->with('success', 'Created successfully.');
        } catch (Exception $e) {
            Log::error('OpportunityCategoryController::store '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Creation failed.');
        }
    }

    public function edit(string $id)
    {
        $data = Category::findOrFail($id);
        $types = self::$types;
        return view('backend.layouts.opportunity_categories.edit', compact('data', 'types'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', array_keys(self::$types)),
            'status' => 'nullable|in:active,inactive',
        ]);

        try {
            $category = Category::findOrFail($id);
            $category->update($validated);
            return redirect()->route('admin.opportunity_categories.index')->with('success', 'Updated successfully.');
        } catch (Exception $e) {
            Log::error('OpportunityCategoryController::update '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Update failed.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
        } catch (Exception $e) {
            Log::error('OpportunityCategoryController::destroy '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }

    public function status(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->status = $category->status === 'active' ? 'inactive' : 'active';
            $category->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (Exception $e) {
            Log::error('OpportunityCategoryController::status '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Status update failed.'], 500);
        }
    }
}
