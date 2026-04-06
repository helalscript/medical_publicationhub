<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\ResearchIdea;
use App\Services\ResearchIdeaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ResearchIdeaAdminController extends Controller
{
    public function __construct(protected ResearchIdeaService $researchIdeaService)
    {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->researchIdeaService->adminQuery();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('mentor_name', function ($row) {
                    return $row->mentor?->name ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at?->format('Y-m-d H:i') ?? '-';
                })
                ->addColumn('status_label', function ($row) {
                    $map = [
                        'pending' => 'secondary',
                        'on_review' => 'warning',
                        'accept' => 'success',
                        'reject' => 'danger',
                    ];
                    $c = $map[$row->status] ?? 'secondary';

                    return '<span class="badge bg-'.$c.'">'.e($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('admin.research_ideas.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>
                        <a type="button" href="'.route('admin.research_ideas.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                    </div>';
                })
                ->rawColumns(['status_label', 'action'])
                ->make(true);
        }

        return view('backend.layouts.research_ideas.admin.index');
    }

    public function show(string $id)
    {
        $data = ResearchIdea::with(['mentor.user', 'user', 'updatedByUser'])->findOrFail($id);

        return view('backend.layouts.research_ideas.admin.show', compact('data'));
    }

    public function edit(string $id)
    {
        $data = ResearchIdea::with(['mentor.user', 'user'])->findOrFail($id);

        return view('backend.layouts.research_ideas.admin.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,on_review,accept,reject',
            'dead_line' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        try {
            $idea = ResearchIdea::findOrFail($id);
            $this->researchIdeaService->adminUpdate($idea, $request->user(), $validated);

            return redirect()->route('admin.research_ideas.index')->with('success', 'Research idea updated successfully.');
        } catch (Exception $e) {
            Log::error('ResearchIdeaAdminController::update '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Update failed.');
        }
    }
}
