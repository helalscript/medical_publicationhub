<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Http\Controllers\Controller;
use App\Enums\Page;
use App\Enums\Section;
use App\Models\CMS;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class HomePagePlatformImpactController extends Controller
{
    /**
     * Platform Impact Section uses CMS fields as:
     *  - title       => stat number  (e.g. "500+")
     *  - sub_title   => stat label   (e.g. "Research Opportunities")
     *  - button_text => category     (e.g. "opportunities" | "services" | "academy")
     */
    public function index(Request $request)
    {
        $containerData = CMS::where('page', Page::HomePage)->where('section', Section::PlatformImpactSection)->first();
        if ($request->ajax()) {
            $data = CMS::where('page', Page::HomePage)->where('section', Section::PlatformImpactSectionContent)->orderBy('id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('number', fn($row) => e($row->title ?? '-'))
                ->addColumn('label', fn($row) => e($row->sub_title ?? '-'))
                ->addColumn('category', function ($row) {
                    $map = ['opportunities' => '<span class="badge bg-info">Opportunities</span>', 'services' => '<span class="badge bg-success">Services</span>', 'academy' => '<span class="badge bg-primary">Academy</span>'];
                    $cat = $row->button_text ?? '-';
                    return $map[$cat] ?? $cat;
                })
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.home_page.platform_impact_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action', 'category'])
                ->make(true);
        }
        return view('backend.layouts.cms.home_page.platform_impact_section.index', compact('containerData'));
    }

    public function create()
    {
        return view('backend.layouts.cms.home_page.platform_impact_section.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:50',
            'sub_title'   => 'required|string|max:255',
            'button_text' => 'required|in:opportunities,services,academy',
        ]);
        try {
            CMS::create([
                'page'        => Page::HomePage->value,
                'section'     => Section::PlatformImpactSectionContent->value,
                'title'       => $request->title,
                'sub_title'   => $request->sub_title,
                'button_text' => $request->button_text,
                'status'      => 'active',
            ]);
            return redirect()->route('cms.home_page.platform_impact_section.index')->with('success', 'Platform Impact item created successfully');
        } catch (Exception $e) {
            Log::error("HomePagePlatformImpactController::store " . $e->getMessage());
            return redirect()->route('cms.home_page.platform_impact_section.index')->with('error', 'Item create failed')->withInput();
        }
    }

    public function edit(string $id)
    {
        $data = CMS::where('page', Page::HomePage->value)->where('section', Section::PlatformImpactSectionContent->value)->findOrFail($id);
        return view('backend.layouts.cms.home_page.platform_impact_section.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title'       => 'required|string|max:50',
            'sub_title'   => 'required|string|max:255',
            'button_text' => 'required|in:opportunities,services,academy',
        ]);
        try {
            $data = CMS::findOrFail($id);
            $data->update([
                'title'       => $request->title,
                'sub_title'   => $request->sub_title,
                'button_text' => $request->button_text,
            ]);
            flash()->success('Platform Impact item updated successfully');
            return redirect()->route('cms.home_page.platform_impact_section.index');
        } catch (Exception $e) {
            Log::error("HomePagePlatformImpactController::update " . $e->getMessage());
            flash()->error('Item update failed');
            return redirect()->route('cms.home_page.platform_impact_section.index');
        }
    }

    public function destroy(string $id)
    {
        CMS::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Platform Impact item deleted successfully.']);
    }

    public function status(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    public function updateContainer(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'sub_title'   => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            CMS::updateOrCreate(
                ['page' => Page::HomePage->value, 'section' => Section::PlatformImpactSection->value],
                $request->only(['title', 'sub_title', 'description'])
            );
            return redirect()->route('cms.home_page.platform_impact_section.index')->with('success', 'Container updated successfully');
        } catch (Exception $e) {
            Log::error("HomePagePlatformImpactController::updateContainer " . $e->getMessage());
            return redirect()->route('cms.home_page.platform_impact_section.index')->with('error', 'Container update failed');
        }
    }
}
