<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Http\Controllers\Controller;
use App\Enums\Page;
use App\Enums\Section;
use App\Helpers\Helper;
use App\Models\CMS;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomePageController extends Controller
{
    // ================= Hero Section (section_title, section_sub_title, section_description) =================
    public function getHeroSection()
    {
        $data = CMS::where('page', Page::HomePage->value)
            ->where('section', Section::HeroSection->value)
            ->first();
        return view('backend.layouts.cms.home_page.hero_section.create', compact('data'));
    }

    public function updateHeroSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
              CMS::updateOrCreate(
                ['page' => Page::HomePage->value, 'section' => Section::HeroSection->value],
                $validatedData
            );
            return redirect()->route('cms.home_page.hero_section.create')->with('success', 'Hero Section Updated Successfully');
        } catch (Exception $e) {
            Log::error("HomePageController::updateHeroSection " . $e->getMessage());
            return redirect()->route('cms.home_page.hero_section.create')->with('error', 'Hero Section Not Updated');
        }
    }

    // ================= Featured Research Opportunities (section_title, section_description) =================
    public function getFeaturedResearchOpportunities()
    {
        $data = CMS::where('page', Page::HomePage->value)
            ->where('section', Section::FeaturedResearchOpportunitiesSection->value)
            ->first();

        return view('backend.layouts.cms.home_page.featured_research_opportunities_section.create', compact('data'));
    }

    public function updateFeaturedResearchOpportunities(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            CMS::updateOrCreate(
                ['page' => Page::HomePage->value, 'section' => Section::FeaturedResearchOpportunitiesSection->value],
                $validatedData
            );

            return redirect()->route('cms.home_page.featured_research_opportunities_section.create')
                ->with('success', 'Featured research opportunities updated successfully');
        } catch (Exception $e) {
            Log::error("HomePageController::updateFeaturedResearchOpportunities " . $e->getMessage());
            return redirect()->route('cms.home_page.featured_research_opportunities_section.create')
                ->with('error', 'Featured research opportunities not updated');
        }
    }

    // ================= Meta Academy (section_title, sub_title, description(editor), image) =================
    public function getMetaAcademy()
    {
        $data = CMS::where('page', Page::HomePage->value)
            ->where('section', Section::MetaAcademySection->value)
            ->first();

        return view('backend.layouts.cms.home_page.meta_academy_section.create', compact('data'));
    }

    public function updateMetaAcademy(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);

        try {
            $data = CMS::where('page', Page::HomePage->value)
                ->where('section', Section::MetaAcademySection->value)
                ->first();

            if ($request->hasFile('image')) {
                if ($data?->image) {
                    Helper::fileDelete(public_path($data->image));
                }
                $validatedData['image'] = Helper::fileUpload(
                    $request->file('image'),
                    'meta_academy',
                    time() . '_' . getFileName($request->file('image'))
                );
            }

            CMS::updateOrCreate(
                ['page' => Page::HomePage->value, 'section' => Section::MetaAcademySection->value],
                $validatedData
            );

            return redirect()->route('cms.home_page.meta_academy_section.create')
                ->with('success', 'Meta academy updated successfully');
        } catch (Exception $e) {
            Log::error("HomePageController::updateMetaAcademy " . $e->getMessage());
            return redirect()->route('cms.home_page.meta_academy_section.create')
                ->with('error', 'Meta academy not updated');
        }
    }

    // ================= Research Journey (section_title, section_sub_title, section_description, button_text, button_text2, sub_description) =================
    public function getResearchJourney()
    {
        $data = CMS::where('page', Page::HomePage->value)
            ->where('section', Section::ResearchJourneySection->value)
            ->first();

        return view('backend.layouts.cms.home_page.research_journey_section.create', compact('data'));
    }

    public function updateResearchJourney(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_text_2' => 'nullable|string|max:255',
            'sub_description' => 'nullable|string',
        ]);

        try {
            CMS::updateOrCreate(
                ['page' => Page::HomePage->value, 'section' => Section::ResearchJourneySection->value],
                $validatedData
            );

            return redirect()->route('cms.home_page.research_journey_section.create')
                ->with('success', 'Research journey updated successfully');
        } catch (Exception $e) {
            Log::error("HomePageController::updateResearchJourney " . $e->getMessage());
            return redirect()->route('cms.home_page.research_journey_section.create')
                ->with('error', 'Research journey not updated');
        }
    }

    // ================= FAQ Section (section title only) =================
    public function getFaqSection()
    {
        $data = CMS::where('page', Page::HomePage->value)
            ->where('section', Section::FaqSection->value)
            ->first();

        return view('backend.layouts.cms.home_page.faq.create', compact('data'));
    }

    public function updateFaqSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
        ]);

        try {
            CMS::updateOrCreate(
                ['page' => Page::HomePage->value, 'section' => Section::FaqSection->value],
                $validatedData
            );

            return redirect()->route('cms.home_page.faq_section.create')->with('success', 'FAQ section updated successfully');
        } catch (Exception $e) {
            Log::error("HomePageController::updateFaqSection " . $e->getMessage());
            return redirect()->route('cms.home_page.faq_section.create')->with('error', 'FAQ section not updated');
        }
    }
}
