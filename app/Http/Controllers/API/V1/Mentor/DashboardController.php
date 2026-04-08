<?php

namespace App\Http\Controllers\API\V1\Mentor;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ChatUser;
use App\Models\Mentor;
use App\Models\Message;
use App\Models\Opportunity;
use App\Models\ResearchIdea;
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
            $mentor = Mentor::query()->where('user_id', $user->id)->first();
            if (! $mentor) {
                return Helper::jsonErrorResponse('Mentor profile not found.', 404);
            }

            $ideaBase = ResearchIdea::query()->where('mentor_id', $mentor->id);
            $opBase = Opportunity::query()->where('mentor_id', $mentor->id);

            $chatIds = ChatUser::query()
                ->where('user_id', (int) $user->id)
                ->pluck('chat_id');

            $data = [
                'mentor' => [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'user_id' => (int) $mentor->user_id,
                ],
                'research_ideas' => [
                    'total' => (int) (clone $ideaBase)->count(),
                    'pending' => (int) (clone $ideaBase)->where('status', 'pending')->count(),
                    'on_review' => (int) (clone $ideaBase)->where('status', 'on_review')->count(),
                    'accept' => (int) (clone $ideaBase)->where('status', 'accept')->count(),
                    'reject' => (int) (clone $ideaBase)->where('status', 'reject')->count(),
                ],
                'opportunities' => [
                    'total' => (int) (clone $opBase)->count(),
                    'active' => (int) (clone $opBase)->where('status', 'active')->count(),
                    'inactive' => (int) (clone $opBase)->where('status', 'inactive')->count(),
                ],
                'chats' => [
                    'total' => (int) $chatIds->count(),
                    'messages_sent' => (int) Message::query()->where('user_id', (int) $user->id)->count(),
                ],
            ];

            return Helper::jsonResponse(true, 'Mentor dashboard overview retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\Mentor\\DashboardController::overview '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load dashboard overview.', 500);
        }
    }
}

