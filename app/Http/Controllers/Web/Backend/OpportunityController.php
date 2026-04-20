<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Mentor;
use App\Models\Opportunity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Opportunity::with('mentor.user')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('mentor_name', function ($row) {
                    return e($row->mentor?->user?->name ?? '-');
                })
                ->addColumn('is_future_badge', function ($row) {
                    $c = $row->is_future ? 'info' : 'secondary';
                    $t = $row->is_future ? 'Yes' : 'No';

                    return '<span class="badge bg-'.$c.'">'.$t.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.opportunities.status')) {
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

                    if ($user && $user->can('admin.opportunities.show')) {
                        $buttons[] = '<a type="button" href="'.route('admin.opportunities.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="View"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>';
                    }
                    if ($user && $user->can('admin.opportunities.edit')) {
                        $buttons[] = '<a type="button" href="'.route('admin.opportunities.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.opportunities.destroy')) {
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

        return view('backend.layouts.opportunities.index');
    }

    public function create()
    {
        $mentors = Mentor::query()
            ->with('user')
            ->join('users', 'users.id', '=', 'mentors.user_id')
            ->orderBy('users.name')
            ->where('mentors.status', 'active')
            ->select('mentors.*')
            ->get();
        $categories = $this->categoryLists();

        return view('backend.layouts.opportunities.create', compact('mentors', 'categories'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $this->validatePayload($request);

        try {
            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = Helper::fileUpload($request->file('thumbnail'), 'opportunities/thumbnail', time().'_'.getFileName($request->file('thumbnail')));
            }

            Opportunity::create($validated);

            return redirect()->route('admin.opportunities.index')->with('success', 'Opportunity created successfully.');
        } catch (Exception $e) {
            Log::error('OpportunityController::store '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Opportunity create failed.');
        }
    }

    public function show(string $id)
    {
        $data = Opportunity::with('mentor.user', 'applications.user')->findOrFail($id);

        $categoryIds = (array) ($data->category_ids ?? []);
        $specialityIds = (array) ($data->speciality_ids ?? []);

        $courseTags = Category::query()
            ->whereIn('id', $categoryIds)
            ->orderBy('name')
            ->pluck('name')
            ->values();

        $specialityTags = Category::query()
            ->whereIn('id', $specialityIds)
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return view('backend.layouts.opportunities.show', compact('data', 'courseTags', 'specialityTags'));
    }

    public function edit(string $id)
    {
        $data = Opportunity::findOrFail($id);

        $mentors = Mentor::query()
            ->with('user')
            ->join('users', 'users.id', '=', 'mentors.user_id')
            ->orderBy('users.name')
            ->where('mentors.status', 'active')
            ->select('mentors.*')
            ->get();
        $categories = $this->categoryLists();

        return view('backend.layouts.opportunities.edit', compact('data', 'mentors', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $this->validatePayload($request, $id);

        try {
            $opportunity = Opportunity::findOrFail($id);

            if ($request->hasFile('thumbnail')) {
                if (! empty($opportunity->thumbnail) && file_exists(public_path($opportunity->thumbnail))) {
                    Helper::fileDelete(public_path($opportunity->thumbnail));
                }
                $validated['thumbnail'] = Helper::fileUpload($request->file('thumbnail'), 'opportunities/thumbnail', time().'_'.getFileName($request->file('thumbnail')));
            } else {
                unset($validated['thumbnail']);
            }

            $opportunity->update($validated);

            return redirect()->route('admin.opportunities.index')->with('success', 'Opportunity updated successfully.');
        } catch (Exception $e) {
            Log::error('OpportunityController::update '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Opportunity update failed.');
        }
    }

    public function destroy(string $id)
    {
        $opportunity = Opportunity::findOrFail($id);
        if (! empty($opportunity->thumbnail) && file_exists(public_path($opportunity->thumbnail))) {
            Helper::fileDelete(public_path($opportunity->thumbnail));
        }
        $opportunity->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }

    public function status(string $id)
    {
        $opportunity = Opportunity::findOrFail($id);
        $opportunity->status = $opportunity->status === 'active' ? 'inactive' : 'active';
        $opportunity->save();

        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    protected function validatePayload(Request $request, ?string $id = null): array
    {
        $validated = $request->validate([
            'mentor_id' => 'required|exists:mentors,id',
            'title' => 'required|string|max:255',
            'overview' => 'required|string|max:10000',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'dead_line' => 'required|date',
            'durations' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
            'is_future' => 'nullable|boolean',

            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'desciptions' => 'nullable|string',
            'speciality_ids' => 'nullable|array',
            'speciality_ids.*' => 'integer|exists:categories,id',
            'studydesign_type_ids' => 'nullable|array',
            'studydesign_type_ids.*' => 'integer|exists:categories,id',
            'contribution_type_ids' => 'nullable|array',
            'contribution_type_ids.*' => 'integer|exists:categories,id',
            'university_hospital_ids' => 'nullable|array',
            'university_hospital_ids.*' => 'integer|exists:categories,id',
            'place_ids' => 'nullable|array',
            'place_ids.*' => 'integer|exists:categories,id',
            'experience_level_ids' => 'nullable|array',
            'experience_level_ids.*' => 'integer|exists:categories,id',
        ]);

        if (! empty($validated['desciptions'])) {
            $decoded = json_decode($validated['desciptions'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                abort(422, 'desciptions must be valid JSON.');
            }
            $validated['desciptions'] = $decoded;
        } else {
            $validated['desciptions'] = null;
        }

        $validated['is_future'] = (bool) ($request->input('is_future', false));
        $validated['status'] = $validated['status'] ?? 'active';

        return $validated;
    }

    protected function categoryLists(): array
    {
        $types = [
            'course' => 'course',
            'speciality' => 'speciality',
            'studydesign_type' => 'student_design_type',
            'contribution_type' => 'contribution_type',
            'university_hospital' => 'university_hospital',
            'place' => 'place',
            'experience_level' => 'experience_level',
        ];

        $out = [];
        foreach ($types as $key => $type) {
            $out[$key] = Category::query()
                ->where('type', $type)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        return $out;
    }
}

