<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Enums\Page;
use App\Enums\Section;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class MetaAcademyPageController extends Controller
{
    // ================= Hero Section (title, sub_title, description) =================
    public function getHeroSection()
    {
        $data = CMS::where('page', Page::MetaAcademyPage->value)
            ->where('section', Section::MetaAcademyPageHeroSection->value)
            ->first();

        return view('backend.layouts.cms.meta_academy_page.hero_section.create', compact('data'));
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
                ['page' => Page::MetaAcademyPage->value, 'section' => Section::MetaAcademyPageHeroSection->value],
                $validatedData
            );

            return redirect()->route('cms.meta_academy_page.hero_section.create')->with('success', 'Hero section updated successfully');
        } catch (Exception $e) {
            Log::error("MetaAcademyPageController::updateHeroSection " . $e->getMessage());
            return redirect()->route('cms.meta_academy_page.hero_section.create')->with('error', 'Hero section not updated');
        }
    }

    // ================= Counter Section Items (items only) =================
    public function counterIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = CMS::where('page', Page::MetaAcademyPage)
                ->where('section', Section::MetaAcademyPageCounterSectionContent)
                ->orderBy('id')
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.meta_academy_page.counter_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.cms.meta_academy_page.counter_section.index');
    }

    public function counterCreate()
    {
        return view('backend.layouts.cms.meta_academy_page.counter_section.create');
    }

    public function counterStore(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);

        try {
            $validatedData['page'] = Page::MetaAcademyPage->value;
            $validatedData['section'] = Section::MetaAcademyPageCounterSectionContent->value;
            $validatedData['status'] = 'active';

            CMS::create($validatedData);

            return redirect()->route('cms.meta_academy_page.counter_section.index')->with('success', 'Counter item created successfully');
        } catch (Exception $e) {
            Log::error("MetaAcademyPageController::counterStore " . $e->getMessage());
            return redirect()->route('cms.meta_academy_page.counter_section.index')->with('error', 'Counter item create failed')->withInput();
        }
    }

    public function counterEdit(string $id)
    {
        $data = CMS::where('page', Page::MetaAcademyPage->value)
            ->where('section', Section::MetaAcademyPageCounterSectionContent->value)
            ->findOrFail($id);

        return view('backend.layouts.cms.meta_academy_page.counter_section.edit', compact('data'));
    }

    public function counterUpdate(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);

        try {
            $data = CMS::findOrFail($id);
            $data->update($validatedData);
            flash()->success('Counter item updated successfully');
            return redirect()->route('cms.meta_academy_page.counter_section.index');
        } catch (Exception $e) {
            Log::error("MetaAcademyPageController::counterUpdate " . $e->getMessage());
            flash()->error('Counter item update failed');
            return redirect()->route('cms.meta_academy_page.counter_section.index');
        }
    }

    public function counterDestroy(string $id)
    {
        $data = CMS::findOrFail($id);
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Counter item deleted successfully.']);
    }

    public function counterStatus(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }
}

