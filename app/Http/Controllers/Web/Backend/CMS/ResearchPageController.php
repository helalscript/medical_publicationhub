<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Enums\Page;
use App\Enums\Section;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResearchPageController extends Controller
{
    // ================= Research Section (section_title, section_description) =================
    public function getResearchSection()
    {
        $data = CMS::where('page', Page::ResearchPage->value)
            ->where('section', Section::ResearchSection->value)
            ->first();

        return view('backend.layouts.cms.research_page.research_section.create', compact('data'));
    }

    public function updateResearchSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            CMS::updateOrCreate(
                ['page' => Page::ResearchPage->value, 'section' => Section::ResearchSection->value],
                $validatedData
            );

            return redirect()->route('cms.research_page.research_section.create')
                ->with('success', 'Research section updated successfully');
        } catch (Exception $e) {
            Log::error("ResearchPageController::updateResearchSection " . $e->getMessage());
            return redirect()->route('cms.research_page.research_section.create')
                ->with('error', 'Research section not updated');
        }
    }
}

