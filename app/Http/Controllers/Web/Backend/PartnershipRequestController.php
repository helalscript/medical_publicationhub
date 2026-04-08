<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\PartnershipRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class PartnershipRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PartnershipRequest::query()->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    $c = $row->status === 'reviewed' ? 'success' : 'warning';
                    return '<span class="badge bg-'.$c.'">'.e($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('admin.partnership_requests.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('backend.layouts.partnership_requests.index');
    }

    public function show(int $id)
    {
        $data = PartnershipRequest::findOrFail($id);
        return view('backend.layouts.partnership_requests.show', compact('data'));
    }

    public function destroy(int $id)
    {
        try {
            PartnershipRequest::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Request deleted successfully.']);
        } catch (Exception $e) {
            Log::error('PartnershipRequestController::destroy '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Delete failed.'], 500);
        }
    }

    public function status(Request $request, int $id)
    {
        try {
            $r = PartnershipRequest::findOrFail($id);
            $r->status = $r->status === 'pending' ? 'reviewed' : 'pending';
            $r->save();

  
            return redirect()->back()->with('success', 'Status updated successfully.');
        } catch (Exception $e) {
            Log::error('PartnershipRequestController::status '.$e->getMessage());
            return redirect()->back()->with('error', 'Status update failed.');
        }
    }
}
