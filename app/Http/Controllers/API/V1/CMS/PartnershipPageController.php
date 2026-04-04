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

class PartnershipPageController extends Controller
{
    private function page(): string
    {
        return Page::PartnershipPage->value;
    }

    public function getHeroSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageHeroSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description', 'button_text', 'button_text_2', 'sub_description')
                ->first();

            return $this->singleOrEmpty($data);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getHeroSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getOurMissionSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageOurMissionSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description', 'image')
                ->first();

            return $this->singleOrEmpty($data);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getOurMissionSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getAboutUsSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageAboutUsSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description', 'image')
                ->first();

            return $this->singleOrEmpty($data);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getAboutUsSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getPartnershipValueSection(): JsonResponse
    {
        try {
            $content = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPagePartnershipValueSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'image')
                ->first();

            $items = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPagePartnershipValueSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description')
                ->orderBy('id')
                ->get();

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, [
                'content' => $content,
                'items' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getPartnershipValueSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getCoreResearchSection(): JsonResponse
    {
        try {
            $content = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageCoreResearchSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description', 'image')
                ->first();

            $items = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageCoreResearchSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description')
                ->orderBy('id')
                ->get();

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, [
                'content' => $content,
                'items' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getCoreResearchSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getSystematicReviewSection(): JsonResponse
    {
        try {
            $content = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageSystematicReviewSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description', 'image')
                ->first();

            $items = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageSystematicReviewSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'title')
                ->orderBy('id')
                ->get();

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, [
                'content' => $content,
                'items' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getSystematicReviewSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getManuscriptSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageManuscriptSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description')
                ->first();

            return $this->singleOrEmpty($data);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getManuscriptSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getPartnershipModelSection(): JsonResponse
    {
        try {
            $content = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPagePartnershipModelSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description')
                ->first();

            $items = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPagePartnershipModelSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description', 'button_text')
                ->orderBy('id')
                ->get();

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, [
                'content' => $content,
                'items' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getPartnershipModelSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getReviewerCommentSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageReviewerCommentSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description', 'image')
                ->first();

            return $this->singleOrEmpty($data);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getReviewerCommentSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getJournalSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageJournalSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title', 'description', 'image')
                ->first();

            return $this->singleOrEmpty($data);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getJournalSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getResearchOpportunitieSection(): JsonResponse
    {
        try {
            $content = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageResearchOpportunitieSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'sub_title')
                ->first();

            $items = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageResearchOpportunitieSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'title', 'image')
                ->orderBy('id')
                ->get();

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, [
                'content' => $content,
                'items' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getResearchOpportunitieSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getWhyPartnerSection(): JsonResponse
    {
        try {
            $data = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPageWhyPartnerSection->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description', 'image')
                ->first();

            return $this->singleOrEmpty($data);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getWhyPartnerSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    public function getPartnerModelSection(): JsonResponse
    {
        try {
            $content = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPagePartnerModelSection->value)
                ->where('status', 'active')
                ->select('id', 'title')
                ->first();

            $items = CMS::where('page', $this->page())
                ->where('section', Section::PartnershipPagePartnerModelSectionContent->value)
                ->where('status', 'active')
                ->select('id', 'title', 'description', 'image')
                ->orderBy('id')
                ->get();

            return Helper::jsonResponse(true, 'Data fetched successfully', 200, [
                'content' => $content,
                'items' => $items,
            ]);
        } catch (Exception $e) {
            Log::error('API\PartnershipPageController::getPartnerModelSection '.$e->getMessage());

            return Helper::jsonErrorResponse('Something went wrong', 500);
        }
    }

    private function singleOrEmpty(?CMS $data): JsonResponse
    {
        if (! $data) {
            return Helper::jsonResponse(true, 'No data found', 200, []);
        }

        return Helper::jsonResponse(true, 'Data fetched successfully', 200, $data);
    }
}
