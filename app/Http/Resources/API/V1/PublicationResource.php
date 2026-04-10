<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationResource extends JsonResource
{
    /** Raw ID list replaced by resolved `categories`. */
    public const HIDDEN_ID_KEYS = [
        'category_ids',
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
