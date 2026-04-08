<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Publication;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class PublicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Publication::query()->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $s = '<div class="form-check form-switch">';
                    $s .= '<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>';
                    $s .= '</div>';

                    return $s;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('admin.publications.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>
                        <a type="button" href="'.route('admin.publications.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.publications.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::query()->where('status', 'active')->where('type', 'publication')->orderBy('name')->get();

        return view('backend.layouts.publications.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        try {
            if ($request->hasFile('attachment')) {
                $validated['attachment'] = Helper::fileUpload($request->file('attachment'), 'publications/attachment', time().'_'.getFileName($request->file('attachment')));
            }

            Publication::create($validated);

            return redirect()->route('admin.publications.index')->with('success', 'Publication created successfully.');
        } catch (Exception $e) {
            Log::error('PublicationController::store '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Publication create failed.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Publication::findOrFail($id);
        $categories = Category::query()
            ->whereIn('id', (array) ($data->category_ids ?? []))
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return view('backend.layouts.publications.show', compact('data', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Publication::findOrFail($id);
        $categories = Category::query()->where('status', 'active')->where('type', 'publication')->orderBy('name')->get();

        return view('backend.layouts.publications.edit', compact('data', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePayload($request, $id);

        try {
            $pub = Publication::findOrFail($id);

            if ($request->hasFile('attachment')) {
                if (! empty($pub->attachment)) {
                    Helper::fileDelete(public_path($pub->attachment));
                }
                $validated['attachment'] = Helper::fileUpload($request->file('attachment'), 'publications/attachment', time().'_'.getFileName($request->file('attachment')));
            }

            $pub->update($validated);

            return redirect()->route('admin.publications.index')->with('success', 'Publication updated successfully.');
        } catch (Exception $e) {
            Log::error('PublicationController::update '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Publication update failed.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pub = Publication::findOrFail($id);
            if (! empty($pub->attachment)) {
                Helper::fileDelete(public_path($pub->attachment));
            }
            $pub->delete();

            return response()->json(['success' => true, 'message' => 'Publication deleted successfully.']);
        } catch (Exception $e) {
            Log::error('PublicationController::destroy '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Publication delete failed.'], 500);
        }
    }

    public function status(Request $request, int $id)
    {
        try {
            $pub = Publication::findOrFail($id);
            $pub->status = $pub->status === 'active' ? 'inactive' : 'active';
            $pub->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (Exception $e) {
            Log::error('PublicationController::status '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Status update failed.'], 500);
        }
    }

    private function validatePayload(Request $request, ?string $id = null): array
    {
        $rawIds = $request->input('category_ids', []);
        if (is_array($rawIds)) {
            $rawIds = array_values(array_filter($rawIds, fn ($v) => $v !== '' && $v !== null));
            $request->merge(['category_ids' => $rawIds]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => [
                'integer',
                Rule::exists('categories', 'id')->where('type', 'publication'),
            ],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $ids = $request->input('category_ids');
        if (! is_array($ids) || count($ids) === 0) {
            $validated['category_ids'] = null;
        } else {
            $validated['category_ids'] = array_values(array_unique(array_map('intval', $ids)));
        }

        return $validated;
    }
}
