<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::query()->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('featured_badge', function ($row) {
                    $c = $row->is_features ? 'info' : 'secondary';
                    $t = $row->is_features ? 'Yes' : 'No';
                    return '<span class="badge bg-'.$c.'">'.$t.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.services.status')) {
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

                    if ($user && $user->can('admin.services.show')) {
                        $buttons[] = '<a type="button" href="'.route('admin.services.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="View"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>';
                    }
                    if ($user && $user->can('admin.services.edit')) {
                        $buttons[] = '<a type="button" href="'.route('admin.services.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.services.destroy')) {
                        $buttons[] = '<button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')" title="Delete"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>';
                    }

                    if (count($buttons) === 0) {
                        return '-';
                    }

                    return '<div class="action-wrapper">'.implode('', $buttons).'</div>';
                })
                ->rawColumns(['status', 'action', 'featured_badge'])
                ->make(true);
        }

        return view('backend.layouts.services.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);
        try {
            $validated['created_by'] = auth()->id();

            if ($request->hasFile('icon')) {
                $validated['icon'] = Helper::fileUpload($request->file('icon'), 'services/icon', time().'_'.getFileName($request->file('icon')));
            }

            Service::create($validated);
            return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
        } catch (Exception $e) {
            Log::error('ServiceController::store '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Service create failed.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Service::findOrFail($id);
        return view('backend.layouts.services.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Service::findOrFail($id);
        return view('backend.layouts.services.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $this->validatePayload($request, $id);
        try {
            $s = Service::findOrFail($id);

            if ($request->hasFile('icon')) {
                if (! empty($s->icon)) {
                    Helper::fileDelete(public_path($s->icon));
                }
                $validated['icon'] = Helper::fileUpload($request->file('icon'), 'services/icon', time().'_'.getFileName($request->file('icon')));
            }

            $s->update($validated);
            return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
        } catch (Exception $e) {
            Log::error('ServiceController::update '.$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Service update failed.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $s = Service::findOrFail($id);
            if (! empty($s->icon)) {
                Helper::fileDelete(public_path($s->icon));
            }
            $s->delete();
            return response()->json(['success' => true, 'message' => 'Service deleted successfully.']);
        } catch (Exception $e) {
            Log::error('ServiceController::destroy '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Service delete failed.'], 500);
        }
    }

    public function status(Request $request, int $id)
    {
        try {
            $s = Service::findOrFail($id);
            $s->status = $s->status === 'active' ? 'inactive' : 'active';
            $s->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (Exception $e) {
            Log::error('ServiceController::status '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Status update failed.'], 500);
        }
    }

    public function featured(Request $request, int $id)
    {
        try {
            $s = Service::findOrFail($id);
            $s->is_features = ! $s->is_features;
            $s->save();
            return response()->json(['success' => true, 'message' => 'Featured updated successfully.']);
        } catch (Exception $e) {
            Log::error('ServiceController::featured '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Featured update failed.'], 500);
        }
    }

    private function validatePayload(Request $request, ?string $id = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'overview' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'starting_price' => ['nullable', 'numeric', 'min:0'],
            'whats_app_num' => ['nullable', 'string', 'max:50'],
            'is_features' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $validated['is_features'] = (bool) ($validated['is_features'] ?? false);
        return $validated;
    }
}
