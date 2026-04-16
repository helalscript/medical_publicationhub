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
                ->select('id', 'title', 'subtitle', 'description')
                ->first();

            if (!$containerData) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            // Get mission items
            $items = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'count_number', 'sub_title')
                ->orderBy('count_number')
                ->get();

            $containerData->items = $items;

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $containerData);
        } catch (Exception $e) {
            Log::error('AboutusPageController::getOurMissionSection ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Update Aboutus page our mission section
     */
    public function updateOurMissionSection(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $validatedData['status'] = 'active';

            $data = CMS::updateOrCreate(
                ['page' => Page::AboutPage->value, 'section' => Section::AboutusPageOurMissionSection->value],
                $validatedData
            );

            return Helper::jsonResponse(true, 'Aboutus our mission section updated successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('AboutusPageController::updateOurMissionSection ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Get all mission items
     */
    public function getMissionItems(): JsonResponse
    {
        try {
            $items = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'count_number', 'sub_title')
                ->orderBy('count_number')
                ->get();

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $items);
        } catch (Exception $e) {
            Log::error('AboutusPageController::getMissionItems ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Create mission item
     */
    public function storeMissionItem(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'count_number' => 'required|integer|min:1',
            'sub_title' => 'required|string|max:255',
        ]);

        try {
            $validatedData['page'] = Page::AboutPage->value;
            $validatedData['section'] = Section::AboutusPageOurMissionSectionContent->value;
            $validatedData['status'] = 'active';

            $item = CMS::create($validatedData);

            return Helper::jsonResponse(true, 'Mission item created successfully', 201, $item);
        } catch (Exception $e) {
            Log::error('AboutusPageController::storeMissionItem ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Update mission item
     */
    public function updateMissionItem(Request $request, string $id): JsonResponse
    {
        $validatedData = $request->validate([
            'count_number' => 'required|integer|min:1',
            'sub_title' => 'required|string|max:255',
        ]);

        try {
            $item = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSectionContent->value)
                ->findOrFail($id);

            $item->update($validatedData);

            return Helper::jsonResponse(true, 'Mission item updated successfully', 200, $item);
        } catch (Exception $e) {
            Log::error('AboutusPageController::updateMissionItem ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Delete mission item
     */
    public function deleteMissionItem(string $id): JsonResponse
    {
        try {
            $item = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSectionContent->value)
                ->findOrFail($id);

            $item->delete();

            return Helper::jsonResponse(true, 'Mission item deleted successfully', 200, []);
        } catch (Exception $e) {
            Log::error('AboutusPageController::deleteMissionItem ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    /**
     * Get full aboutus page data
     */
    public function getAboutUsPage(): JsonResponse
    {
        try {
            $heroSection = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageHeroSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description')
                ->first();

            $missionSection = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'subtitle', 'description')
                ->first();

            $missionItems = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'count_number', 'sub_title')
                ->orderBy('count_number')
                ->get();

            if ($missionSection) {
                $missionSection->items = $missionItems;
            }

            $data = [
                'hero_section' => $heroSection ?? [],
                'our_mission_section' => $missionSection ?? [],
            ];

            return Helper::jsonResponse(true, 'Aboutus page data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error('AboutusPageController::getAboutUsPage ' . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }
}
