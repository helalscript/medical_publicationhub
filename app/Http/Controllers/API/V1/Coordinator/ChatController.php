<?php

namespace App\Http\Controllers\API\V1\Coordinator;

use App\Events\MessageSent;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChatController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $per_page = $request->per_page ?? 10;
            
            // As a coordinator, fetch all opportunity chats
            $data = Chat::query()
                ->select('id', 'chatable_type', 'chatable_id', 'type', 'status', 'created_at')
                ->where('chatable_type', \App\Models\Opportunity::class)
                ->with([
                    'chatable' => function ($query) {
                        $query->select('id', 'title', 'thumbnail');
                    },
                    'last_message'
                ])
                ->latest('id')
                ->paginate($per_page);

            return Helper::jsonResponse(true, 'Chats retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\ChatController::index ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to load chats.', 500);
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $per_page = $request->per_page ?? 10;
            
            $chat = Chat::with([
                'chatable' => function ($query) {
                    $query->select('id', 'title', 'thumbnail');
                },
                'users.user' => function ($query) {
                    $query->select('id', 'name', 'avatar');
                }
            ])->findOrFail($id);

            // Fetch messages
            $messages = Message::with('user')->where('chat_id', $chat->id)->latest('id')->paginate($per_page);

            return Helper::jsonResponse(true, 'Chat retrieved successfully.', 200, [
                'chat' => $chat,
                'messages' => $messages->items(),
            ], true, $messages);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\ChatController::show ' . $e->getMessage());
            return Helper::jsonErrorResponse('Chat not found.', 404);
        }
    }

    public function sendMessage(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required_without:file|string|max:10000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,mp3,wav,ogg,m4a,aac,webm|max:10240',
        ]);

        try {
            $chat = Chat::findOrFail($id);
            if ($chat->status !== 'active') {
                throw new RuntimeException('Chat is inactive.');
            }

            // Ensure coordinator is in ChatUser to show up in users list
            ChatUser::firstOrCreate([
                'chat_id' => $chat->id,
                'user_id' => $request->user()->id,
            ], [
                'role' => 'coordinator',
                'status' => 'active',
            ]);

            $msg = Message::create([
                'chat_id' => $chat->id,
                'user_id' => $request->user()->id,
                'message' => $request->input('message'),
                'file' => $request->hasFile('file') ? Helper::fileUpload($request->file('file'), 'chats', 'chat-' . time()) : null,
            ]);

            event(new MessageSent($msg));

            return Helper::jsonResponse(true, 'Message sent.', 201, $msg);
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\ChatController::sendMessage ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to send message.', 500);
        }
    }
}
