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

class AboutusPageController extends Controller
{
    /**
     * Get Aboutus page hero section
     */
    public function getHeroSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageHeroSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description')
                ->first();

            if (!$data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('AboutusPageController::getHeroSection ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Update Aboutus page hero section
     */
    public function updateHeroSection(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $validatedData['status'] = 'active';

            $data = CMS::updateOrCreate(
                ['page' => Page::AboutPage->value, 'section' => Section::AboutusPageHeroSection->value],
                $validatedData
            );

            return Helper::jsonResponse(true, 'Aboutus hero section updated successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('AboutusPageController::updateHeroSection ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Get Aboutus page our mission section
     */
    public function getOurMissionSection(): JsonResponse
    {
        try {
            $containerData = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description')
                ->first();

            if (!$containerData) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            // Get mission items
            $items = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title')
                ->latest()
                ->get();

            $containerData->items = $items;

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $containerData);
        } catch (Exception $e) {
            Log::error('AboutusPageController::getOurMissionSection ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

}
