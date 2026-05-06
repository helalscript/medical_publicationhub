<?php

namespace App\Http\Controllers\API\V1\CMS;

use App\Enums\Page;
use App\Enums\Section;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use App\Models\DynamicPage;
use App\Models\SystemSetting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomePageController extends Controller
{
    public function getSystemInfo(Request $request): JsonResponse
    {
        try {
            $data = SystemSetting::select('id', 'system_name', 'logo', 'favicon', 'copyright_text', 'address', 'company_open_hour', 'description', 'contact_number', 'address', 'email','whatsapp_number','calendly_scheduling_url')->first();
            return Helper::jsonResponse(true, 'System data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getSystemInfo" . $e->getMessage());
            return Helper::jsonErrorResponse('Failed to retrieve System', 403);
        }
    }

    public function getDynamicPages(Request $request): JsonResponse
    {
        try {
            $per_page = $request->has('per_page') ? $request->per_page : 25;
            $dynamicPages = DynamicPage::where('status', 'active')->select('id', 'page_title', 'page_slug')->paginate($per_page);
            if (!$dynamicPages) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }
            return Helper::jsonResponse(true, 'Dynamic pages retrieved successfully.', 200, $dynamicPages, true);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse('Failed to retrieve Dynamic pages data.', 403);
        }
    }

    public function showDaynamicPage($page_slug): JsonResponse
    {
        try {
            $page = DynamicPage::where('page_slug', $page_slug)->where('status', 'active')->firstOrFail();
            if (!$page) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }
            return Helper::jsonResponse(true, 'Dynamic page retrieved successfully.', 200, $page);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse('Failed to retrieve Dynamic page data.', 403);
        }
    }

    public function getSocialLinks(Request $request): JsonResponse
    {
        try {
            $per_page = $request->has('per_page') ? $request->per_page : 25;
            $data = CMS::select('id', 'title', 'image', 'link_url')->where('page', Page::HomePage)->where('section', Section::SocialLinkContainer)->where('status', 'active')->paginate($per_page);
            return Helper::jsonResponse(true, 'Social links data fetched successfully', 200, $data, true);
        } catch (Exception $e) {
            Log::error("HomePageController::getSocialLinks" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // Hero: title, sub_title, button_text
    public function getHeroSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::HomePage)
                ->where('section', Section::HeroSection)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description')
                ->first();
            if (!$data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }
            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getHeroSection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // Counter: content(title), items(count_number, sub_title)
    public function getCounterSection(): JsonResponse
    {
        try {
            // $section = CMS::where('page', Page::HomePage)
            //     ->where('section', Section::CounterSection)
            //     ->where('status', 'active')
            //     ->select('id', 'title')
            //     ->first();

            $items = CMS::where('page', Page::HomePage)
                ->where('section', Section::CounterSectionContent)
                ->where('status', 'active')
                ->select('id', 'title as count_number', 'sub_title')
                ->orderBy('id')
                ->get();

            $data = [
                // 'content' => ['title' => $section?->title ?? ''],
                'items' => $items,
            ];
            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getCounterSection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // Why Researchers Choose: content(title), items(image, title, description)
    public function getWhyResearchersChooseSection(): JsonResponse
    {
        try {
            $section = CMS::where('page', Page::HomePage)
                ->where('section', Section::WhyResearchersChooseSection)
                ->where('status', 'active')
                ->select('id', 'title')
                ->first();

            $items = CMS::where('page', Page::HomePage)
                ->where('section', Section::WhyResearchersChooseSectionContent)
                ->where('status', 'active')
                ->select('id', 'image', 'title', 'description')
                ->orderBy('id')
                ->get();

            $data = [
                'content' => ['title' => $section?->title ?? ''],
                'items' => $items,
            ];
            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getWhyResearchersChooseSection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // Featured Research Opportunities: title, description
    public function getFeaturedResearchOpportunitiesSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::HomePage)
                ->where('section', Section::FeaturedResearchOpportunitiesSection)
                ->where('status', 'active')
                ->select('id', 'title', 'description')
                ->first();

            if (!$data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getFeaturedResearchOpportunitiesSection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // Meta Academy: title, sub_title, description(editor), image, button texts, sub_description
    public function getMetaAcademySection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::HomePage)
                ->where('section', Section::MetaAcademySection)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description', 'sub_description', 'image','button_text')
                ->first();

            if (!$data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getMetaAcademySection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // Research Journey: title, sub_title, description, button_text, button_text_2, sub_description
    public function getResearchJourneySection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::HomePage)
                ->where('section', Section::ResearchJourneySection)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description', 'button_text', 'button_text_2', 'sub_description')
                ->first();

            if (!$data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getResearchJourneySection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // FAQ: title only
    public function getFaqSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', Page::HomePage)
                ->where('section', Section::FaqSection)
                ->where('status', 'active')
                ->select('id', 'title')
                ->first();

            if (!$data) {
                return Helper::jsonResponse(true, 'No data found', 200, []);
            }
            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getFaqSection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    // Review: content(title), items(image, description)
    public function getReviewSection(): JsonResponse
    {
        try {
            $section = CMS::where('page', Page::HomePage)
                ->where('section', Section::ReviewSection)
                ->where('status', 'active')
                ->select('id', 'title')
                ->first();

            $items = CMS::where('page', Page::HomePage)
                ->where('section', Section::ReviewSectionContent)
                ->where('status', 'active')
                ->select('id', 'image', 'description')
                ->orderBy('id')
                ->get();

            $data = [
                'content' => [
                    'title' => $section?->title ?? '',
                ],
                'items' => $items,
            ];
            return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
        } catch (Exception $e) {
            Log::error("HomePageController::getReviewSection" . $e->getMessage());
            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }
}
