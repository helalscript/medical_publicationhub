<?php

namespace App\Services\API\V1\Offer;

use App\Http\Resources\API\V1\OfferResource;
use App\Models\Offer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class OfferService
{
    public function paginateActive(Request $request): LengthAwarePaginator
    {
        $perPage = max(1, min(50, (int) $request->get('per_page', 10)));

        $query = Offer::query()
            ->active()
            ->notExpired()
            ->orderBy('sort_order')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $search = (string) $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('promo_code', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function findActiveOrFail(int $id): OfferResource
    {
        $offer = Offer::query()
            ->active()
            ->notExpired()
            ->findOrFail($id);

        return new OfferResource($offer);
    }
}
