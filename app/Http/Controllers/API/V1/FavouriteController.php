<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Opportunity;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavouriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = Favourite::with('favouriteable')
                ->where('user_id', $request->user()->id)
                ->latest('id')
                ->get();

            return Helper::jsonResponse(true, 'Favourites retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\FavouriteController::index '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to load favourites.', 500);
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $data = Favourite::with('favouriteable')
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);

            return Helper::jsonResponse(true, 'Favourite retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('API\\V1\\FavouriteController::show '.$e->getMessage());

            return Helper::jsonErrorResponse('Favourite not found.', 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opportunity_id' => 'required|exists:opportunities,id',
        ]);

        try {
            $opportunity = Opportunity::where('status', 'active')->findOrFail($validated['opportunity_id']);

            $fav = Favourite::firstOrCreate([
                'user_id' => $request->user()->id,
                'favouriteable_id' => $opportunity->id,
                'favouriteable_type' => Opportunity::class,
            ]);

            return Helper::jsonResponse(true, 'Added to favourites.', 201, $fav);
        } catch (Exception $e) {
            Log::error('API\\V1\\FavouriteController::store '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to add favourite.', 500);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $fav = Favourite::where('user_id', $request->user()->id)->findOrFail($id);
            $fav->delete();

            return Helper::jsonResponse(true, 'Removed from favourites.', 200);
        } catch (Exception $e) {
            Log::error('API\\V1\\FavouriteController::destroy '.$e->getMessage());

            return Helper::jsonErrorResponse('Failed to remove favourite.', 500);
        }
    }
}

