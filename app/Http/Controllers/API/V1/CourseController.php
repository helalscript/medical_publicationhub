<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\CourseMentorResource;
use App\Http\Resources\API\V1\CourseResource;
use App\Models\Category;
use App\Models\Course;
use App\Models\Mentor;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    private const CATEGORY_TYPE = 'meta_academy';

    public function index(Request $request): JsonResponse
    {
        $filterRule = 'sometimes|array';
        $idItemRule = 'integer|min:1';

        $request->validate([
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'is_future' => 'sometimes|boolean',
            'category_ids' => $filterRule,
            'category_ids.*' => $idItemRule,
            'mentor_ids' => $filterRule,
            'mentor_ids.*' => $idItemRule,
            'price_min' => 'sometimes|numeric|min:0',
            'price_max' => 'sometimes|numeric|min:0',
        ]);

        try {
            $q = Course::query()->where('status', 'active');

            $this->applyCourseFilters($q, $request);

            $data = $q->latest()->paginate((int) $request->input('per_page', 10));
            $this->enrichCourses($data->getCollection());

            $items = CourseResource::collection($data->getCollection())->resolve();

            return Helper::jsonResponse(true, 'Courses retrieved successfully.', 200, $items, true, $data);
        } catch (Exception $e) {
            Log::error('CourseController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load courses.', 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'with_modules' => 'sometimes|boolean',
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'integer|min:1',
            'mentor_ids' => 'sometimes|array',
            'mentor_ids.*' => 'integer|min:1',
            'price_min' => 'sometimes|numeric|min:0',
            'price_max' => 'sometimes|numeric|min:0',
            'is_future' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
        ]);

        try {
            $withModules = $request->boolean('with_modules', true);

            $q = Course::query()->where('status', 'active');

            if ($withModules) {
                $q->with([
                    'modules' => fn ($mq) => $mq->orderBy('order_by')->orderBy('id'),
                    'modules.lessons' => fn ($lq) => $lq->orderBy('order_by')->orderBy('id'),
                ]);
            }

            $this->applyCourseFilters($q, $request);

            $course = $q->findOrFail($id);

            $this->enrichCourse($course);

            return Helper::jsonResponse(true, 'Course retrieved successfully.', 200, (new CourseResource($course))->resolve());
        } catch (Exception $e) {
            Log::error('CourseController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Course not found.', 404);
        }
    }

    private function applyCourseFilters(Builder $q, Request $request): void
    {
        if ($request->has('is_future')) {
            $q->where('is_future', $request->boolean('is_future'));
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
            $q->where(function (Builder $w) use ($like) {
                $w->where('title', 'like', $like)
                    ->orWhere('short_description', 'like', $like)
                    ->orWhere('overview', 'like', $like);
            });
        }

        $categoryIds = $this->normalizeIntIds((array) $request->input('category_ids', []));
        if ($categoryIds !== []) {
            $q->where(function (Builder $w) use ($categoryIds) {
                foreach ($categoryIds as $cid) {
                    $w->orWhereJsonContains('category_ids', $cid);
                }
            });
        }

        $mentorIds = $this->normalizeIntIds((array) $request->input('mentor_ids', []));
        if ($mentorIds !== []) {
            $q->where(function (Builder $w) use ($mentorIds) {
                foreach ($mentorIds as $mid) {
                    $w->orWhereJsonContains('mentor_ids', $mid);
                }
            });
        }

        if ($request->filled('price_min')) {
            $q->where('price', '>=', (float) $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $q->where('price', '<=', (float) $request->input('price_max'));
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

    private function enrichCourse(Course $course): void
    {
        $catIds = (array) ($course->category_ids ?? []);
        $course->setAttribute('categories', $this->orderedCategoriesPayload($catIds));

        $mentorIds = $this->normalizeIntIds((array) ($course->mentor_ids ?? []));
        $course->setAttribute('mentors', $this->resolveMentorsPayload($mentorIds));
    }

    /**
     * @param  Collection<int, Course>  $courses
     */
    private function enrichCourses(Collection $courses): void
    {
        if ($courses->isEmpty()) {
            return;
        }

        $allCatIds = [];
        $allMentorIds = [];
        foreach ($courses as $course) {
            foreach ($this->normalizeIntIds((array) ($course->category_ids ?? [])) as $id) {
                $allCatIds[$id] = true;
            }
            foreach ($this->normalizeIntIds((array) ($course->mentor_ids ?? [])) as $id) {
                $allMentorIds[$id] = true;
            }
        }

        $catLookup = collect();
        if ($allCatIds !== []) {
            $catList = array_keys($allCatIds);
            $catLookup = Category::query()
                ->whereIn('id', $catList)
                ->where('status', 'active')
                ->where('type', self::CATEGORY_TYPE)
                ->get(['id', 'name'])
                ->keyBy('id');
        }

        $mentorLookup = collect();
        if ($allMentorIds !== []) {
            $mentorLookup = Mentor::query()
                ->with(['user' => fn ($uq) => $uq->select('id', 'name', 'avatar')])
                ->whereIn('id', array_keys($allMentorIds))
                ->where('status', 'active')
                ->get()
                ->keyBy('id');
        }

        foreach ($courses as $course) {
            $catIds = $this->normalizeIntIds((array) ($course->category_ids ?? []));
            $categories = [];
            foreach ($catIds as $cid) {
                $row = $catLookup->get($cid);
                if ($row) {
                    $categories[] = ['id' => (int) $row->id, 'name' => (string) $row->name];
                }
            }
            $course->setAttribute('categories', $categories);

            $mentorIds = $this->normalizeIntIds((array) ($course->mentor_ids ?? []));
            $course->setAttribute('mentors', $this->mentorPayloadFromLookup($mentorIds, $mentorLookup));
        }
    }

    /**
     * @param  array<int, int>  $mentorIds
     * @return list<array<string, mixed>>
     */
    private function resolveMentorsPayload(array $mentorIds): array
    {
        if ($mentorIds === []) {
            return [];
        }

        $mentors = Mentor::query()
            ->with(['user' => fn ($uq) => $uq->select('id', 'name', 'avatar')])
            ->whereIn('id', $mentorIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        return $this->mentorPayloadFromLookup($mentorIds, $mentors);
    }

    /**
     * @param  array<int, int>  $mentorIds
     * @param  Collection<int, Mentor>  $byId
     * @return list<array<string, mixed>>
     */
    private function mentorPayloadFromLookup(array $mentorIds, Collection $byId): array
    {
        $payload = [];
        foreach ($mentorIds as $mid) {
            $m = $byId->get($mid);
            if ($m && $m->user) {
                $payload[] = (new CourseMentorResource($m))->resolve();
            }
        }

        return $payload;
    }
}
