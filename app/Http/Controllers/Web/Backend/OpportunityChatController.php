<?php

namespace App\Http\Controllers\Web\Backend;

use App\Events\MessageSent;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class OpportunityChatController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Chat::query()
                ->with(['chatable.mentor.user', 'users.user'])
                ->where('chatable_type', \App\Models\Opportunity::class)
                ->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('opportunity', function ($row) {
                    return e($row->chatable?->title ?? '-');
                })
                ->addColumn('participants', function ($row) {
                    return $row->users->count() . ' Participants';
                })
                ->addColumn('status_badge', function ($row) {
                    $c = $row->status === 'active' ? 'success' : 'secondary';
                    return '<span class="badge bg-'.$c.'">'.e($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="'.route('admin.opportunity_chats.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>
                    </div>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('backend.layouts.opportunity_chats.index');
    }

    public function show(Request $request, string $id)
    {
        $chat = Chat::with(['chatable.mentor.user', 'users.user'])->findOrFail($id);
        $messagesQuery = Message::with('user')
            ->where('chat_id', $chat->id)
            ->latest('id');

        if ($request->ajax()) {
            return response()->json($messagesQuery->paginate(10));
        }

        $messages = $messagesQuery->paginate(10);

        return view('backend.layouts.opportunity_chats.show', compact('chat', 'messages'));
    }

    public function sendMessage(Request $request, string $id)
    {
        $request->validate([
            'message' => 'required_without:file|nullable|string|max:10000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240',
        ]);
        try {
            $chat = Chat::findOrFail($id);
            // ensure admin is participant (mapped to super_admin role)
            ChatUser::firstOrCreate(
                ['chat_id' => $chat->id, 'user_id' => (int) $request->user()->id],
                ['role' => 'super_admin', 'status' => 'active']
            );
            $msg = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $request->user()->id,
                'message' => $request->input('message'),
                'file' => $request->hasFile('file') ? Helper::fileUpload($request->file('file'), 'chats', 'chat-' . time()) : null,
            ]);

            event(new MessageSent($msg->load('user')));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }

            return redirect()->back()->with('success', 'Message sent.');
        } catch (Exception $e) {
            Log::error('OpportunityChatController::sendMessage ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Failed to send message.');
        }
    }
}

