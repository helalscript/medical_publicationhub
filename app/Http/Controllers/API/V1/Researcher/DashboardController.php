<?php

namespace App\Http\Controllers\API\V1\Researcher;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ChatUser;
use App\Models\Favourite;
use App\Models\Message;
use App\Models\OpportunityApplication;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $appBase = OpportunityApplication::query()->where('user_id', (int) $user->id);

            $chatIds = ChatUser::query()
                ->where('user_id', (int) $user->id)
                ->pluck('chat_id');

            $data = [
                'researcher' => [
                    'user_id' => (int) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'applications' => [
                    'total' => (int) (clone $appBase)->count(),
                    'pending' => (int) (clone $appBase)->where('status', 'pending')->count(),
                    'accept' => (int) (clone $appBase)->where('status', 'accept')->count(),
                    'reject' => (int) (clone $appBase)->where('status', 'reject')->count(),
                ],
                'favourites' => [
                    'total' => (int) Favourite::query()->where('user_id', (int) $user->id)->count(),
                ],
                'chats' => [
                    'total' => (int) $chatIds->count(),
                    'messages_sent' => (int) Message::query()->where('user_id', (int) $user->id)->count(),
                ],
            ];

            return Helper::jsonResponse(true, 'Researcher dashboard overview retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\Researcher\\DashboardController::overview '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load dashboard overview.', 500);
        }
    }
}

