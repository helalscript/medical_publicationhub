<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Enums\Page;
use App\Enums\Section;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\CMS;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class AboutusPageController extends Controller
{
    /**
     * ================= Hero Section (title, sub_title, description) =================
     */
    public function getHeroSection()
    {
        $data = CMS::where('page', Page::AboutPage->value)
            ->where('section', Section::AboutusPageHeroSection->value)
            ->first();

        return view('backend.layouts.cms.aboutus_page.hero_section.create', compact('data'));
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
                ['page' => Page::AboutPage->value, 'section' => Section::AboutusPageHeroSection->value],
                array_merge($validatedData, ['status' => 'active'])
            );

            return redirect()->route('cms.aboutus_page.hero_section.create')->with('success', 'Hero Section Updated Successfully');
        } catch (Exception $e) {
            Log::error('AboutusPageController::updateHeroSection ' . $e->getMessage());
            return redirect()->route('cms.aboutus_page.hero_section.create')->with('error', 'Hero Section Not Updated');
        }
    }

    /**
     * ================= Our Mission Section (title, subtitle, description) =================
     */
    public function getOurMissionSection()
    {
        $data = CMS::where('page', Page::AboutPage->value)
            ->where('section', Section::AboutusPageOurMissionSection->value)
            ->first();

        return view('backend.layouts.cms.aboutus_page.our_mission_section.create', compact('data'));
    }

    public function updateOurMissionSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            CMS::updateOrCreate(
                ['page' => Page::AboutPage->value, 'section' => Section::AboutusPageOurMissionSection->value],
                array_merge($validatedData, ['status' => 'active'])
            );

            return redirect()->route('cms.aboutus_page.our_mission_section.create')->with('success', 'Our Mission Section Updated Successfully');
        } catch (Exception $e) {
            Log::error('AboutusPageController::updateOurMissionSection ' . $e->getMessage());
            return redirect()->route('cms.aboutus_page.our_mission_section.create')->with('error', 'Our Mission Section Not Updated');
        }
    }

    /**
     * ================= Our Mission Items (count_number, sub_title) =================
     */
    public function indexOurMissionItems(Request $request)
    {
        $containerData = CMS::where('page', Page::AboutPage->value)
            ->where('section', Section::AboutusPageOurMissionSection->value)
            ->first();

        if ($request->ajax()) {
            $data = CMS::where('page', Page::AboutPage->value)
                ->where('section', Section::AboutusPageOurMissionSectionContent->value)
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    $status = '<div class="form-check form-switch">';
                    $status .= '<input onclick="changeStatus(event,' . $data->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"';

                    if ($data->status == 'active') {
                        $status .= ' checked';
                    }

                    $status .= '>';
                    $status .= '</div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.aboutus_page.our_mission_items.edit', $data->id) . '"
                                class="ps-0 border-0 bg-transparent lh-1 position-relative top-2"
                                 ><i class="material-symbols-outlined fs-16 text-body">edit</i>
                            </a>
                        <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete" onclick="deleteRecord(event,' . $data->id . ')">
                        <i class="material-symbols-outlined fs-16 text-danger">delete</i>
                        </button>
             
                </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.cms.aboutus_page.our_mission_items.index', compact('containerData'));
    }

    public function createOurMissionItem()
    {
        return view('backend.layouts.cms.aboutus_page.our_mission_items.create');
    }

    public function storeOurMissionItem(Request $request)
    {
        $validatedData = $request->validate([
            'count_number' => 'required|integer|min:1',
            'sub_title' => 'required|string|max:255',
        ]);

        try {
            $validatedData['page'] = Page::AboutPage->value;
            $validatedData['section'] = Section::AboutusPageOurMissionSectionContent->value;
            $validatedData['status'] = 'active';

            CMS::create($validatedData);

            return redirect()->route('cms.aboutus_page.our_mission_items.index')
                ->with('success', 'Mission Item Created Successfully');
        } catch (Exception $e) {
            Log::error('AboutusPageController::storeOurMissionItem ' . $e->getMessage());
            return redirect()->route('cms.aboutus_page.our_mission_items.index')
                ->with('error', 'Mission Item Creation Failed')->withInput();
        }
    }

    public function editOurMissionItem(string $id)
    {
        $data = CMS::where('page', Page::AboutPage->value)
            ->where('section', Section::AboutusPageOurMissionSectionContent->value)
            ->findOrFail($id);

        return view('backend.layouts.cms.aboutus_page.our_mission_items.edit', compact('data'));
    }

    public function updateOurMissionItem(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'count_number' => 'required|integer|min:1',
            'sub_title' => 'required|string|max:255',
        ]);

        try {
            $data = CMS::findOrFail($id);
            $data->update($validatedData);

            flash()->success('Mission Item Updated Successfully');
            return redirect()->route('cms.aboutus_page.our_mission_items.index');
        } catch (Exception $e) {
            Log::error('AboutusPageController::updateOurMissionItem ' . $e->getMessage());
            flash()->error('Mission Item Update Failed');
            return redirect()->route('cms.aboutus_page.our_mission_items.index');
        }
    }

    public function destroyOurMissionItem(string $id)
    {
        $data = CMS::findOrFail($id);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.',
            ], 404);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mission Item Deleted Successfully',
        ]);
    }

    public function statusOurMissionItem(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'Status Changed Successfully',
        ]);
    }
}
