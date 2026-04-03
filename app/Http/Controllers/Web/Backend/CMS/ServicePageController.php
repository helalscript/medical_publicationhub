<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Enums\Page;
use App\Enums\Section;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServicePageController extends Controller
{
    // ================= Service Section (title, sub_title, description) =================
    public function getServiceSection()
    {
        $data = CMS::where('page', Page::ServicePage->value)
            ->where('section', Section::ServiceSection->value)
            ->first();

        return view('backend.layouts.cms.service_page.service_section.create', compact('data'));
    }

    public function updateServiceSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            CMS::updateOrCreate(
                ['page' => Page::ServicePage->value, 'section' => Section::ServiceSection->value],
                $validatedData
            );

            return redirect()->route('cms.service_page.service_section.create')
                ->with('success', 'Service section updated successfully');
        } catch (Exception $e) {
            Log::error("ServicePageController::updateServiceSection " . $e->getMessage());
            return redirect()->route('cms.service_page.service_section.create')
                ->with('error', 'Service section not updated');
        }
    }
}

