<?php

namespace App\Http\Controllers\API\V1\Coordinator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OpportunityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) ($request->input('per_page', 15));
            $data = Opportunity::with('mentor.user:id,name,avatar')
                ->latest('id')
                ->paginate($perPage);

            return Helper::jsonResponse(true, 'Opportunities retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\OpportunityController::index ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to load opportunities.', 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $data = Opportunity::with(['mentor.user:id,name,avatar'])->findOrFail($id);
            return Helper::jsonResponse(true, 'Opportunity retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\OpportunityController::show ' . $e->getMessage());
            return Helper::jsonErrorResponse('Opportunity not found.', 404);
        }
    }
}
