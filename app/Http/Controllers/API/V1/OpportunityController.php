<?php

namespace App\Http\Controllers\API\V1;

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
        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'only_active' => 'sometimes|boolean',
            'is_future' => 'sometimes|boolean',
        ]);

        try {
            $perPage = (int) ($request->input('per_page', 15));
            $q = Opportunity::query()->with('mentor.user')->latest('id');

            if ($request->boolean('only_active', true)) {
                $q->where('status', 'active');
            }
            if ($request->has('is_future')) {
                $q->where('is_future', $request->boolean('is_future'));
            }

            $data = $q->paginate($perPage);

            return Helper::jsonResponse(true, 'Opportunities retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load opportunities.', 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $data = Opportunity::with('mentor.user')->where('status', 'active')->findOrFail($id);

            return Helper::jsonResponse(true, 'Opportunity retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Opportunity not found.', 404);
        }
    }
}

