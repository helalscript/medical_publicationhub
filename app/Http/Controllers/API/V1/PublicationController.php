<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Publication;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $q = Publication::query()->where('status', 'active');

            if ($request->filled('search')) {
                $search = (string) $request->get('search');
                $q->where('title', 'like', "%{$search}%");
            }

            if ($request->filled('category_id')) {
                $cid = (int) $request->get('category_id');
                // JSON contains match
                $q->whereJsonContains('category_ids', $cid);
            }

            $data = $q->latest()->paginate((int) ($request->get('per_page', 10)));

            return Helper::jsonResponse(true, 'Publications retrieved successfully.', 200, $data, true);
        } catch (Exception $e) {
            Log::error('PublicationController::index '.$e->getMessage());
            return Helper::jsonErrorResponse('Failed to load publications.', 500);
        }
    }

    public function show(int $id)
    {
        try {
            $data = Publication::where('status', 'active')->findOrFail($id);
            return Helper::jsonResponse(true, 'Publication retrieved successfully.', 200, $data);
        } catch (Exception $e) {
            Log::error('PublicationController::show '.$e->getMessage());
            return Helper::jsonErrorResponse('Publication not found.', 404);
        }
    }
}
