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
                // ->where('coordinator_id', $request->user()->id)
                ->latest('id')
                ->paginate($perPage);

            $this->hydrateCategoriesAndMapResource($data);

            return Helper::jsonResponse(true, 'Opportunities retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\OpportunityController::index ' . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to load opportunities.', 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $data = Opportunity::with(['mentor.user:id,name,avatar'])
                // ->where('coordinator_id', request()->user()->id)
                ->findOrFail($id);

            $resource = $this->hydrateCategoriesAndMapResource($data);

            return Helper::jsonResponse(true, 'Opportunity retrieved successfully.', 200, $resource);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\OpportunityController::show ' . $e->getMessage());
            return Helper::jsonErrorResponse('Opportunity not found.', 404);
        }
    }

    /**
     * Optimizes category loading by executing a single query (No N+1) 
     * and maps to OpportunityResource.
     */
    private function hydrateCategoriesAndMapResource($opportunities)
    {
        $isSingle = $opportunities instanceof Opportunity;
        $items = $isSingle ? collect([$opportunities]) : $opportunities->getCollection();

        $categoryIds = collect();
        foreach ($items as $item) {
            $categoryIds = $categoryIds->merge($item->category_ids ?? [])
                ->merge($item->speciality_ids ?? [])
                ->merge($item->studydesign_type_ids ?? [])
                ->merge($item->contribution_type_ids ?? [])
                ->merge($item->university_hospital_ids ?? [])
                ->merge($item->place_ids ?? [])
                ->merge($item->experience_level_ids ?? []);
        }

        // Single query for all categories (Removes N+1 problem completely)
        $categories = \App\Models\Category::whereIn('id', $categoryIds->unique()->filter())
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        $items->transform(function ($opportunity) use ($categories) {
            $opportunity->categories_data = collect($opportunity->category_ids ?? [])->map(fn($id) => $categories->get($id))->filter()->values();
            $opportunity->specialities = collect($opportunity->speciality_ids ?? [])->map(fn($id) => $categories->get($id))->filter()->values();
            $opportunity->studydesign_types = collect($opportunity->studydesign_type_ids ?? [])->map(fn($id) => $categories->get($id))->filter()->values();
            $opportunity->contribution_types = collect($opportunity->contribution_type_ids ?? [])->map(fn($id) => $categories->get($id))->filter()->values();
            $opportunity->university_hospitals = collect($opportunity->university_hospital_ids ?? [])->map(fn($id) => $categories->get($id))->filter()->values();
            $opportunity->places = collect($opportunity->place_ids ?? [])->map(fn($id) => $categories->get($id))->filter()->values();
            $opportunity->experience_levels = collect($opportunity->experience_level_ids ?? [])->map(fn($id) => $categories->get($id))->filter()->values();
            
            return new \App\Http\Resources\API\V1\OpportunityResource($opportunity);
        });

        return $isSingle ? $items->first() : $opportunities;
    }
}
