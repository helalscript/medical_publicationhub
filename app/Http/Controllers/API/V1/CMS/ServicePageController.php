<?php

namespace App\Http\Controllers\API\V1\CMS;

use App\Enums\Page;
use App\Enums\Section;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ServicePageController extends Controller
{
    public function getServiceSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::ServicePage->value)
                ->where('section', Section::ServiceSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description')
                ->first();

            if (! $data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('API\ServicePageController::getServiceSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }
}
