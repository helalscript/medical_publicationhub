<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    /** Raw ID-only JSON columns (resolved lists are exposed instead). */
    public const HIDDEN_ID_KEYS = [
        'category_ids',
        'speciality_ids',
        'studydesign_type_ids',
        'contribution_type_ids',
        'university_hospital_ids',
        'place_ids',
        'experience_level_ids',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return collect($this->resource->toArray())
            ->except(self::HIDDEN_ID_KEYS)
            ->all();
    }
}
