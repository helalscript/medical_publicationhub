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

class ResearchPageController extends Controller
{
    public function getResearchSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::ResearchPage->value)
                ->where('section', Section::ResearchSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description')
                ->first();

            if (! $data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('API\ResearchPageController::getResearchSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }
}
