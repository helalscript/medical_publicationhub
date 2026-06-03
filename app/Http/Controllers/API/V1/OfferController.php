<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\OfferResource;
use App\Services\API\V1\Offer\OfferService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    public function __construct(
        private readonly OfferService $offerService
    ) {}

    public function index(Request $request)
    {
        try {
            $data = $this->offerService->paginateActive($request);
            $data->setCollection(
                $data->getCollection()->map(fn ($offer) => new OfferResource($offer))
            );

            return Helper::jsonResponse(true, 'Offers retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('OfferController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load offers.', 500);
        }
    }

    public function show(int $id)
    {
        try {
            $data = $this->offerService->findActiveOrFail($id);

            return Helper::jsonResponse(true, 'Offer retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('OfferController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Offer not found.', 404);
        }
    }
}
