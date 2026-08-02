<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Rubric;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

class RubricController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Rubric::where('is_global', true)->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('criteria_count', function ($data) {
                    return is_array($data->criteria) ? count($data->criteria) : 0;
                })
                ->addColumn('action', function ($data) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('admin.rubrics.show', $data->id) . '"
                                class="ps-0 border-0 bg-transparent lh-1 position-relative top-2"
                                 ><i class="material-symbols-outlined fs-16 text-primary">visibility</i>
                            </a>
                        <a type="button" href="' . route('admin.rubrics.edit', $data->id) . '"
                                class="ps-0 border-0 bg-transparent lh-1 position-relative top-2"
                                 ><i class="material-symbols-outlined fs-16 text-body">edit</i>
                            </a>
                        <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete" onclick="deleteRecord(event,' . $data->id . ')">
                        <i class="material-symbols-outlined fs-16 text-danger">delete</i>
                        </button>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("backend.layouts.rubrics.index");
    }

    public function create()
    {
        return view("backend.layouts.rubrics.create");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.points' => 'required|numeric|min:0',
        ]);

        try {
            Rubric::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'is_global' => true,
                'mentor_id' => null,
                'criteria' => array_values($validated['criteria']),
            ]);

            return redirect()->route('admin.rubrics.index')->with('t-success', 'Rubric created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $rubric = Rubric::where('is_global', true)->findOrFail($id);
        return view("backend.layouts.rubrics.show", compact('rubric'));
    }

    public function edit($id)
    {
        $rubric = Rubric::where('is_global', true)->findOrFail($id);
        return view("backend.layouts.rubrics.edit", compact('rubric'));
    }

    public function update(Request $request, $id)
    {
        $rubric = Rubric::where('is_global', true)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.points' => 'required|numeric|min:0',
        ]);

        try {
            $rubric->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'criteria' => array_values($validated['criteria']),
            ]);

            return redirect()->route('admin.rubrics.index')->with('t-success', 'Rubric updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $rubric = Rubric::where('is_global', true)->findOrFail($id);
            $rubric->delete();
            return response()->json([
                'success' => true,
                'message' => 'Rubric deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}
