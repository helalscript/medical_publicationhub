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
        $data = collect($this->resource->toArray())
            ->except(self::HIDDEN_ID_KEYS)
            ->all();

        // Include loaded relationships/dynamically assigned properties if they are set
        if (isset($this->resource->categories_data)) $data['categories'] = $this->resource->categories_data;
        if (isset($this->resource->specialities)) $data['specialities'] = $this->resource->specialities;
        if (isset($this->resource->studydesign_types)) $data['studydesign_types'] = $this->resource->studydesign_types;
        if (isset($this->resource->contribution_types)) $data['contribution_types'] = $this->resource->contribution_types;
        if (isset($this->resource->university_hospitals)) $data['university_hospitals'] = $this->resource->university_hospitals;
        if (isset($this->resource->places)) $data['places'] = $this->resource->places;
        if (isset($this->resource->experience_levels)) $data['experience_levels'] = $this->resource->experience_levels;

        return $data;
    }
}
