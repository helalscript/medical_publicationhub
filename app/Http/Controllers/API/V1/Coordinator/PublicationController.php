<?php

namespace App\Http\Controllers\API\V1\Coordinator;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\PublicationResource;
use App\Models\Category;
use App\Models\Publication;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PublicationController extends Controller
{
    private const CATEGORY_TYPE = 'publication';

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|integer|min:1',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'integer|min:1',
        ]);

        try {
            $q = Publication::query()->where('status', 'active');

            $search = trim((string) $request->input('search', ''));
            if ($search !== '') {
                $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
                $q->where(function (Builder $w) use ($like) {
                    $w->where('title', 'like', $like)
                        ->orWhere('overview', 'like', $like);
                });
            }

            if ($request->filled('category_id')) {
                $q->whereJsonContains('category_ids', (int) $request->input('category_id'));
            }

            $categoryIds = $this->normalizeIntIds((array) $request->input('category_ids', []));
            if ($categoryIds !== []) {
                $q->where(function (Builder $w) use ($categoryIds) {
                    foreach ($categoryIds as $cid) {
                        $w->orWhereJsonContains('category_ids', $cid);
                    }
                });
            }

            $data = $q->latest()->paginate((int) $request->input('per_page', 10));
            $this->attachCategoriesToCollection($data->getCollection());

            $items = PublicationResource::collection($data->getCollection())->resolve();

            return Helper::jsonResponse(true, 'Publications retrieved successfully.', 200, $items, true, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\PublicationController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load publications.', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $data = Publication::query()->where('status', 'active')->findOrFail($id);
            $this->attachCategories($data);

            return Helper::jsonResponse(true, 'Publication retrieved successfully.', 200, (new PublicationResource($data))->resolve());
        } catch (Exception $e) {
            Log::error('API\\V1\\Coordinator\\PublicationController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Publication not found.', 404);
        }
    }

    private function attachCategories(Publication $publication): void
    {
        $publication->setAttribute(
            'categories',
            $this->orderedCategoriesPayload((array) ($publication->category_ids ?? []))
        );
    }

    /**
     * @param  Collection<int, Publication>  $publications
     */
    private function attachCategoriesToCollection(Collection $publications): void
    {
        if ($publications->isEmpty()) {
            return;
        }

        $allIds = [];
        foreach ($publications as $pub) {
            foreach ($this->normalizeIntIds((array) ($pub->category_ids ?? [])) as $id) {
                $allIds[$id] = true;
            }
        }

        $catLookup = collect();
        if ($allIds !== []) {
            $catLookup = Category::query()
                ->whereIn('id', array_keys($allIds))
                ->where('status', 'active')
                ->where('type', self::CATEGORY_TYPE)
                ->get(['id', 'name'])
                ->keyBy('id');
        }

        foreach ($publications as $pub) {
            $ids = $this->normalizeIntIds((array) ($pub->category_ids ?? []));
            $payload = [];
            foreach ($ids as $cid) {
                $row = $catLookup->get($cid);
                if ($row) {
                    $payload[] = ['id' => (int) $row->id, 'name' => (string) $row->name];
                }
            }
            $pub->setAttribute('categories', $payload);
        }
    }

    /**
     * @param  array<int, int>  $ids
     * @return list<array{id: int, name: string}>
     */
    private function orderedCategoriesPayload(array $ids): array
    {
        $ids = $this->normalizeIntIds($ids);
        if ($ids === []) {
            return [];
        }

        $rows = Category::query()
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->where('type', self::CATEGORY_TYPE)
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
}
