<?php

namespace App\Http\Controllers\API\V1\Researcher;

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
            $chatIds = ChatUser::query()
                ->where('user_id', (int) $request->user()->id)
                ->pluck('chat_id');

            $data = Chat::query()
                ->with(['chatable', 'users.user'])
                ->whereIn('id', $chatIds)
                ->latest('id')
                ->get();

            return Helper::jsonResponse(true, 'Chats retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\Researcher\\ChatController::index '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to load chats.', 500);
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $allowed = ChatUser::query()
                ->where('chat_id', (int) $id)
                ->where('user_id', (int) $request->user()->id)
                ->exists();
            if (! $allowed) {
                return Helper::jsonErrorResponse('Unauthorized', 403);
            }

            $chat = Chat::with(['chatable', 'users.user'])->findOrFail($id);
            $messages = Message::with('user')->where('chat_id', $chat->id)->latest('id')->paginate(30);

            return Helper::jsonResponse(true, 'Chat retrieved successfully.', 200, [
                'chat' => $chat,
                'messages' => $messages,
            ], true, $messages);
        } catch (Exception $e) {
            Log::error('API\\V1\\Researcher\\ChatController::show '.$e->getMessage());
            return Helper::jsonErrorResponse('Chat not found.', 404);
        }
    }

    public function sendMessage(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:10000',
        ]);

        try {
            $allowed = ChatUser::query()
                ->where('chat_id', (int) $id)
                ->where('user_id', (int) $request->user()->id)
                ->exists();
            if (! $allowed) {
                return Helper::jsonErrorResponse('Unauthorized', 403);
            }

            $chat = Chat::findOrFail($id);
            if ($chat->status !== 'active') {
                throw new RuntimeException('Chat is inactive.');
            }

            $msg = Message::create([
                'chat_id' => $chat->id,
                'user_id' => (int) $request->user()->id,
                'message' => $validated['message'],
            ]);

            event(new MessageSent($msg));

            return Helper::jsonResponse(true, 'Message sent.', 201, $msg);
        } catch (RuntimeException $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('API\\V1\\Researcher\\ChatController::sendMessage '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to send message.', 500);
        }
    }
}

