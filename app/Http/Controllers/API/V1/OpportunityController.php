<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\OpportunityResource;
use App\Models\Category;
use App\Models\Mentor;
use App\Models\Opportunity;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OpportunityController extends Controller
{
    /**
     * Response keys => [ JSON column on opportunities, categories.type value ]
     */
    private const CATEGORY_LOOKUP_MAP = [
        'categories' => ['ids_key' => 'category_ids', 'type' => 'course'],
        'specialities' => ['ids_key' => 'speciality_ids', 'type' => 'speciality'],
        'studydesign_types' => ['ids_key' => 'studydesign_type_ids', 'type' => 'student_design_type'],
        'contribution_types' => ['ids_key' => 'contribution_type_ids', 'type' => 'contribution_type'],
        'university_hospitals' => ['ids_key' => 'university_hospital_ids', 'type' => 'university_hospital'],
        'places' => ['ids_key' => 'place_ids', 'type' => 'place'],
        'experience_levels' => ['ids_key' => 'experience_level_ids', 'type' => 'experience_level'],
    ];

    public function index(Request $request): JsonResponse
    {
        $filterRule = 'sometimes|array';
        $idItemRule = 'integer|min:1';

        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'only_active' => 'sometimes|boolean',
            'is_future' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
            'category_ids' => $filterRule,
            'category_ids.*' => $idItemRule,
            'speciality_ids' => $filterRule,
            'speciality_ids.*' => $idItemRule,
            'studydesign_type_ids' => $filterRule,
            'studydesign_type_ids.*' => $idItemRule,
            'contribution_type_ids' => $filterRule,
            'contribution_type_ids.*' => $idItemRule,
            'university_hospital_ids' => $filterRule,
            'university_hospital_ids.*' => $idItemRule,
            'place_ids' => $filterRule,
            'place_ids.*' => $idItemRule,
            'experience_level_ids' => $filterRule,
            'experience_level_ids.*' => $idItemRule,
        ]);

        try {
            $perPage = (int) ($request->input('per_page', 15));
            $q = Opportunity::query()->with('mentor.user:id,name,avatar')
                ->where('status', 'active')
                ->latest('id');

            if ($request->boolean('only_active', true)) {
                $q->where('status', 'active');
            }
            if ($request->has('is_future')) {
                $q->where('is_future', $request->boolean('is_future'));
            }

            $this->applySearchAndFilters($q, $request);

            $data = $q->paginate($perPage);
            $this->attachCategoryLookupsToCollection($data->getCollection());

            $items = OpportunityResource::collection($data->getCollection())->resolve();

            return Helper::jsonResponse(true, 'Opportunities retrieved successfully.', 200, $items, true, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load opportunities.', 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $data = Opportunity::with(['mentor.user:id,name,avatar'])->where('status', 'active')->findOrFail($id);
            $this->attachCategoryLookups($data);

            return Helper::jsonResponse(true, 'Opportunity retrieved successfully.', 200, (new OpportunityResource($data))->resolve());
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Opportunity not found.', 404);
        }
    }

    private function applySearchAndFilters(Builder $q, Request $request): void
    {
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
            $q->where(function (Builder $w) use ($like) {
                $w->where('title', 'like', $like)
                    ->orWhere('overview', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            });
        }

        foreach (self::CATEGORY_LOOKUP_MAP as $cfg) {
            $column = $cfg['ids_key'];
            $ids = $this->normalizeIntIds((array) $request->input($column, []));
            if ($ids === []) {
                continue;
            }

            // Within one dimension: match if the opportunity includes any selected id (OR).
            // Across dimensions: AND (each group is a separate where clause).
            $q->where(function (Builder $w) use ($column, $ids) {
                foreach ($ids as $id) {
                    $w->orWhereJsonContains($column, $id);
                }
            });
        }
    }

    /**
     * @param  array<int, mixed>  $values
     * @return array<int, int>
     */
    private function normalizeIntIds(array $values): array
    {
        $out = [];
        foreach ($values as $v) {
            if (is_int($v)) {
                $out[] = $v;
            } elseif (is_string($v) && ctype_digit($v)) {
                $out[] = (int) $v;
            }
        }

        return array_values(array_unique(array_filter($out, fn ($id) => $id > 0)));
    }

    /**
     * @param  array<int, int>  $ids
     * @return list<array{id: int, name: string}>
     */
    private function orderedCategoryPayload(array $ids, string $expectedType): array
    {
        $ids = $this->normalizeIntIds($ids);
        if ($ids === []) {
            return [];
        }

        $rows = Category::query()
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->where('type', $expectedType)
            ->get(['id', 'name'])
            ->keyBy('id');

        $payload = [];
        foreach ($ids as $id) {
            $row = $rows->get($id);
            if ($row) {
                $payload[] = ['id' => (int) $row->id, 'name' => (string) $row->name];
            }
        }

        return $payload;
    }

    private function attachCategoryLookups(Opportunity $opportunity): void
    {
        foreach (self::CATEGORY_LOOKUP_MAP as $attribute => $cfg) {
            $ids = (array) ($opportunity->{$cfg['ids_key']} ?? []);
            $opportunity->setAttribute($attribute, $this->orderedCategoryPayload($ids, $cfg['type']));
        }
    }

    /**
     * @param  Collection<int, Opportunity>  $opportunities
     */
    private function attachCategoryLookupsToCollection(Collection $opportunities): void
    {
        if ($opportunities->isEmpty()) {
            return;
        }

        $neededByType = [];
        foreach (self::CATEGORY_LOOKUP_MAP as $cfg) {
            $type = $cfg['type'];
            if (! isset($neededByType[$type])) {
                $neededByType[$type] = [];
            }
        }

        foreach ($opportunities as $opp) {
            foreach (self::CATEGORY_LOOKUP_MAP as $cfg) {
                $type = $cfg['type'];
                $ids = $this->normalizeIntIds((array) ($opp->{$cfg['ids_key']} ?? []));
                foreach ($ids as $id) {
                    $neededByType[$type][$id] = true;
                }
            }
        }

        $lookup = [];
        foreach ($neededByType as $type => $idMap) {
            $idList = array_keys($idMap);
            if ($idList === []) {
                $lookup[$type] = collect();

                continue;
            }
            $lookup[$type] = Category::query()
                ->whereIn('id', $idList)
                ->where('status', 'active')
                ->where('type', $type)
                ->get(['id', 'name'])
                ->keyBy('id');
        }

        foreach ($opportunities as $opp) {
            foreach (self::CATEGORY_LOOKUP_MAP as $attribute => $cfg) {
                $type = $cfg['type'];
                $ids = $this->normalizeIntIds((array) ($opp->{$cfg['ids_key']} ?? []));
                $rows = $lookup[$type] ?? collect();
                $payload = [];
                foreach ($ids as $id) {
                    $row = $rows->get($id);
                    if ($row) {
                        $payload[] = ['id' => (int) $row->id, 'name' => (string) $row->name];
                    }
                }
                $opp->setAttribute($attribute, $payload);
            }
        }
    }

    public function mentors(int $id): JsonResponse
    {
        try {
            $mentor = Mentor::with('user:id,name,avatar')
            ->where('status', 'active')
            ->find($id);

            return Helper::jsonResponse(true, 'Mentor retrieved successfully.', 200, $mentor);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityController::mentors '.$e->getMessage());

            return Helper::jsonErrorResponse('Mentor not found.', 404);
        }
    }

    public function fillterList(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:course,speciality,student_design_type,contribution_type,university_hospital,place,experience_level',
        ]);

        try {
            $perPage = (int) ($request->input('per_page', 15));
            $list = Category::query()
            ->select('id', 'name')
                ->where('status', 'active')
                ->where('type', $request->input('type'))
                ->orderBy('name')
                ->paginate($perPage);
            return Helper::jsonResponse(true, 'Fillter list retrieved successfully.', 200, $list,true);
        } catch (Exception $e) {
            Log::error('API\\V1\\OpportunityController::fillterList '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to load fillter list.', 500);
        }
    }
}
