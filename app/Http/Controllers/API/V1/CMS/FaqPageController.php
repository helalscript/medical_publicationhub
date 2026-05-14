<?php

namespace App\Http\Controllers\API\V1\CMS;

use App\Enums\Page;
use App\Enums\Section;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaqPageController extends Controller
{
    public function getHeroSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::FaqPage->value)
                ->where('section', Section::FaqPageHeroSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description')
                ->first();

            if (! $data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('FaqPageController::getHeroSection ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getQuestionSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::FaqPage->value)
                ->where('section', Section::FaqPageQuestionSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description', 'button_text')
                ->first();

            if (! $data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('FaqPageController::getQuestionSection ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

}

