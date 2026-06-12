<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $icon = $this->icon;
        $isImage = $this->isIconImage();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $icon,
            'icon_is_image' => $isImage,
            'icon_url' => $isImage ? $icon : null,
            'icon_text' => $isImage ? null : $icon,
            'promo_code' => $this->promo_code,
            'discount_percentage' => $this->discount_percentage,
            'end_date' => $this->end_date?->toIso8601String(),
            'background_color' => $this->background_color,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'countdown' => $this->countdown(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
