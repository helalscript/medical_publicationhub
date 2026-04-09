<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public const HIDDEN_ID_KEYS = [
        'category_ids',
        'mentor_ids',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $course = $this->resource;

        $data = collect($course->toArray())
            ->except(array_merge(self::HIDDEN_ID_KEYS, ['modules']))
            ->all();

        if ($course->relationLoaded('modules')) {
            $data['modules'] = CourseModuleResource::collection($course->modules)->resolve();
        }

        return $data;
    }
}
