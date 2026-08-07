<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\OpportunityApplication;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class OpportunityApplicationAdminController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = OpportunityApplication::with(['user', 'opportunity'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('opportunity_title', fn ($row) => e($row->opportunity?->title ?? '-'))
                ->addColumn('applicant', fn ($row) => e(trim(($row->first_name ?? '').' '.($row->last_name ?? ''))))
                ->addColumn('status_badge', function ($row) {
                    $map = ['pending' => 'secondary', 'accept' => 'success', 'reject' => 'danger'];
                    $c = $map[$row->status] ?? 'secondary';

                    return '<span class="badge bg-'.$c.'">'.e($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    $buttons = [];

                    if ($user && $user->can('admin.opportunity_applications.show')) {
                        $buttons[] = '<a type="button" href="'.route('admin.opportunity_applications.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="View"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>';
                    }
                    if ($user && $user->can('admin.opportunity_applications.edit')) {
                        $buttons[] = '<a type="button" href="'.route('admin.opportunity_applications.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.opportunity_applications.destroy')) {
                        $buttons[] = '<button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')" title="Delete"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>';
                    }

                    if (count($buttons) === 0) {
                        return '-';
                    }

                    return '<div class="action-wrapper">'.implode('', $buttons).'</div>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('backend.layouts.opportunity_applications.index');
    }

    public function show(string $id)
    {
        $data = OpportunityApplication::with(['user', 'opportunity'])->findOrFail($id);

        return view('backend.layouts.opportunity_applications.show', compact('data'));
    }

    public function edit(string $id)
    {
        $data = OpportunityApplication::with(['user', 'opportunity'])->findOrFail($id);

        return view('backend.layouts.opportunity_applications.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accept,reject',
        ]);

        try {
            $app = OpportunityApplication::with(['opportunity.mentor', 'user'])->findOrFail($id);
            $app->update($validated);

            // If accepted: add applicant into opportunity group chat
            if ($app->status === 'accept') {
                $op = $app->opportunity;
                if ($op) {
                    $chat = Chat::firstOrCreate([
                        'chatable_id' => $op->id,
                        'chatable_type' => \App\Models\Opportunity::class,
                    ], [
                        'type' => 'group',
                        'status' => 'active',
                    ]);

                    // ensure mentor is in chat
                    $mentorUserId = $op->mentor?->user_id;
                    if (! empty($mentorUserId)) {
                        ChatUser::firstOrCreate(
                            ['chat_id' => $chat->id, 'user_id' => (int) $mentorUserId],
                            ['role' => 'mentor', 'status' => 'active']
                        );
                    }

                    // add researcher (applicant)
                    ChatUser::updateOrCreate(
                        ['chat_id' => $chat->id, 'user_id' => (int) $app->user_id],
                        ['role' => 'researcher', 'status' => 'active']
                    );

                    // Decrement available_seats atomically when application is accepted
                    if ($op->total_seats !== null && $op->available_seats > 0) {
                        $op->decrement('available_seats');
                    }
                }
            }

            return redirect()->route('admin.opportunity_applications.index')->with('success', 'Application updated successfully.');
        } catch (Exception $e) {
            Log::error('OpportunityApplicationAdminController::update '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Update failed.');
        }
    }

    public function destroy(string $id)
    {
        $app = OpportunityApplication::findOrFail($id);
        $app->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully.']);
    }
}

