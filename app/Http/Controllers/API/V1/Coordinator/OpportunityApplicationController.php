<?php

namespace App\Http\Controllers\API\V1\Coordinator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\OpportunityApplication;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OpportunityApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) ($request->input('per_page', 15));
            $query = OpportunityApplication::with(['opportunity:id,title', 'user:id,name,email,avatar'])->latest('id');
            
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }
            if ($request->has('opportunity_id')) {
                $query->where('opportunity_id', $request->input('opportunity_id'));
            }

            $data = $query->paginate($perPage);

            return Helper::jsonResponse(true, 'Applications retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\OpportunityApplicationController::index ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to load applications.', 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $data = OpportunityApplication::with(['opportunity', 'user:id,name,email,avatar'])->findOrFail($id);
            return Helper::jsonResponse(true, 'Application retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\OpportunityApplicationController::show ' . $e->getMessage());
            return Helper::jsonErrorResponse('Application not found.', 404);
        }
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'under_review', 'accepted', 'rejected'])],
        ]);

        try {
            $app = OpportunityApplication::findOrFail($id);
            $app->status = $validated['status'];
            $app->save();

            // If accepted, add applicant to the opportunity group chat
            if ($validated['status'] === 'accepted') {
                $op = $app->opportunity;
                if ($op && $op->chat) {
                    \App\Models\ChatUser::firstOrCreate([
                        'chat_id' => $op->chat->id,
                        'user_id' => $app->user_id,
                    ], [
                        'role' => 'researcher',
                        'status' => 'active',
                    ]);
                }
            }

            return Helper::jsonResponse(true, 'Application status updated successfully.', 200, $app);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\OpportunityApplicationController::updateStatus ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to update application status.', 500);
        }
    }
}
