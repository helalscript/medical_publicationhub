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
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PartnershipPageController extends Controller
{
    private function p(): string
    {
        return Page::PartnershipPage->value;
    }

    // ================= Hero =================
    public function getHeroSection()
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageHeroSection->value)->first();
        return view('backend.layouts.cms.partnership_page.hero_section.create', compact('data'));
    }

    public function updateHeroSection(Request $request)
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
                ['page' => $this->p(), 'section' => Section::PartnershipPageHeroSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.hero_section.create')->with('success', 'Hero section updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateHeroSection ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.hero_section.create')->with('error', 'Update failed');
        }
    }

    // ================= Our Mission =================
    public function getOurMissionSection()
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageOurMissionSection->value)->first();
        return view('backend.layouts.cms.partnership_page.our_mission_section.create', compact('data'));
    }

    public function updateOurMissionSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPageOurMissionSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_our_mission', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageOurMissionSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.our_mission_section.create')->with('success', 'Our mission section updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateOurMissionSection ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.our_mission_section.create')->with('error', 'Update failed');
        }
    }

    // ================= About Us =================
    public function getAboutUsSection()
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageAboutUsSection->value)->first();
        return view('backend.layouts.cms.partnership_page.about_us_section.create', compact('data'));
    }

    public function updateAboutUsSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPageAboutUsSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_about_us', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageAboutUsSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.about_us_section.create')->with('success', 'About us section updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateAboutUsSection ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.about_us_section.create')->with('error', 'Update failed');
        }
    }

    // ================= Manuscript =================
    public function getManuscriptSection()
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageManuscriptSection->value)->first();
        return view('backend.layouts.cms.partnership_page.manuscript_section.create', compact('data'));
    }

    public function updateManuscriptSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageManuscriptSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.manuscript_section.create')->with('success', 'Manuscript section updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateManuscriptSection ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.manuscript_section.create')->with('error', 'Update failed');
        }
    }

    // ================= Reviewer Comment =================
    public function getReviewerCommentSection()
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageReviewerCommentSection->value)->first();
        return view('backend.layouts.cms.partnership_page.reviewer_comment_section.create', compact('data'));
    }

    public function updateReviewerCommentSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPageReviewerCommentSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_reviewer_comment', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageReviewerCommentSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.reviewer_comment_section.create')->with('success', 'Reviewer comment section updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateReviewerCommentSection ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.reviewer_comment_section.create')->with('error', 'Update failed');
        }
    }

    // ================= Journal =================
    public function getJournalSection()
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageJournalSection->value)->first();
        return view('backend.layouts.cms.partnership_page.journal_section.create', compact('data'));
    }

    public function updateJournalSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPageJournalSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_journal', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageJournalSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.journal_section.create')->with('success', 'Journal section updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateJournalSection ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.journal_section.create')->with('error', 'Update failed');
        }
    }

    // ================= Why Partner =================
    public function getWhyPartnerSection()
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageWhyPartnerSection->value)->first();
        return view('backend.layouts.cms.partnership_page.why_partner_section.create', compact('data'));
    }

    public function updateWhyPartnerSection(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPageWhyPartnerSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_why_partner', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageWhyPartnerSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.why_partner_section.create')->with('success', 'Why partner section updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateWhyPartnerSection ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.why_partner_section.create')->with('error', 'Update failed');
        }
    }

    // ================= Partnership Value (container + items) =================
    public function partnershipValueIndex(Request $request)
    {
        $containerData = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPagePartnershipValueSection)->first();
        if ($request->ajax()) {
            $data = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPagePartnershipValueSectionContent)->orderBy('id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.partnership_page.partnership_value_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('backend.layouts.cms.partnership_page.partnership_value_section.index', compact('containerData'));
    }

    public function partnershipValueCreate()
    {
        return view('backend.layouts.cms.partnership_page.partnership_value_section.create');
    }

    public function partnershipValueStore(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            $validatedData['page'] = $this->p();
            $validatedData['section'] = Section::PartnershipPagePartnershipValueSectionContent->value;
            $validatedData['status'] = 'active';
            CMS::create($validatedData);
            return redirect()->route('cms.partnership_page.partnership_value_section.index')->with('success', 'Item created successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::partnershipValueStore ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.partnership_value_section.index')->with('error', 'Create failed')->withInput();
        }
    }

    public function partnershipValueEdit(string $id)
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPagePartnershipValueSectionContent->value)->findOrFail($id);
        return view('backend.layouts.cms.partnership_page.partnership_value_section.edit', compact('data'));
    }

    public function partnershipValueUpdate(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            CMS::findOrFail($id)->update($validatedData);
            flash()->success('Item updated successfully');
            return redirect()->route('cms.partnership_page.partnership_value_section.index');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::partnershipValueUpdate ' . $e->getMessage());
            flash()->error('Update failed');
            return redirect()->route('cms.partnership_page.partnership_value_section.index');
        }
    }

    public function partnershipValueDestroy(string $id)
    {
        CMS::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    public function partnershipValueStatus(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    public function updatePartnershipValueContainer(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPagePartnershipValueSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_value', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPagePartnershipValueSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.partnership_value_section.index')->with('success', 'Container updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updatePartnershipValueContainer ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.partnership_value_section.index')->with('error', 'Container update failed');
        }
    }

    // ================= Core Research (container + items with Quill in views) =================
    public function coreResearchIndex(Request $request)
    {
        $containerData = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPageCoreResearchSection)->first();
        if ($request->ajax()) {
            $data = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPageCoreResearchSectionContent)->orderBy('id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.partnership_page.core_research_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('backend.layouts.cms.partnership_page.core_research_section.index', compact('containerData'));
    }

    public function coreResearchCreate()
    {
        return view('backend.layouts.cms.partnership_page.core_research_section.create');
    }

    public function coreResearchStore(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            $validatedData['page'] = $this->p();
            $validatedData['section'] = Section::PartnershipPageCoreResearchSectionContent->value;
            $validatedData['status'] = 'active';
            CMS::create($validatedData);
            return redirect()->route('cms.partnership_page.core_research_section.index')->with('success', 'Item created successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::coreResearchStore ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.core_research_section.index')->with('error', 'Create failed')->withInput();
        }
    }

    public function coreResearchEdit(string $id)
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageCoreResearchSectionContent->value)->findOrFail($id);
        return view('backend.layouts.cms.partnership_page.core_research_section.edit', compact('data'));
    }

    public function coreResearchUpdate(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            CMS::findOrFail($id)->update($validatedData);
            flash()->success('Item updated successfully');
            return redirect()->route('cms.partnership_page.core_research_section.index');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::coreResearchUpdate ' . $e->getMessage());
            flash()->error('Update failed');
            return redirect()->route('cms.partnership_page.core_research_section.index');
        }
    }

    public function coreResearchDestroy(string $id)
    {
        CMS::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    public function coreResearchStatus(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    public function updateCoreResearchContainer(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPageCoreResearchSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_core_research', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageCoreResearchSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.core_research_section.index')->with('success', 'Container updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateCoreResearchContainer ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.core_research_section.index')->with('error', 'Container update failed');
        }
    }

    // ================= Systematic Review (container + items title only) =================
    public function systematicReviewIndex(Request $request)
    {
        $containerData = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPageSystematicReviewSection)->first();
        if ($request->ajax()) {
            $data = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPageSystematicReviewSectionContent)->orderBy('id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.partnership_page.systematic_review_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('backend.layouts.cms.partnership_page.systematic_review_section.index', compact('containerData'));
    }

    public function systematicReviewCreate()
    {
        return view('backend.layouts.cms.partnership_page.systematic_review_section.create');
    }

    public function systematicReviewStore(Request $request)
    {
        $validatedData = $request->validate(['title' => 'nullable|string|max:255']);
        try {
            $validatedData['page'] = $this->p();
            $validatedData['section'] = Section::PartnershipPageSystematicReviewSectionContent->value;
            $validatedData['status'] = 'active';
            CMS::create($validatedData);
            return redirect()->route('cms.partnership_page.systematic_review_section.index')->with('success', 'Item created successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::systematicReviewStore ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.systematic_review_section.index')->with('error', 'Create failed')->withInput();
        }
    }

    public function systematicReviewEdit(string $id)
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageSystematicReviewSectionContent->value)->findOrFail($id);
        return view('backend.layouts.cms.partnership_page.systematic_review_section.edit', compact('data'));
    }

    public function systematicReviewUpdate(Request $request, string $id)
    {
        $validatedData = $request->validate(['title' => 'nullable|string|max:255']);
        try {
            CMS::findOrFail($id)->update($validatedData);
            flash()->success('Item updated successfully');
            return redirect()->route('cms.partnership_page.systematic_review_section.index');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::systematicReviewUpdate ' . $e->getMessage());
            flash()->error('Update failed');
            return redirect()->route('cms.partnership_page.systematic_review_section.index');
        }
    }

    public function systematicReviewDestroy(string $id)
    {
        CMS::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    public function systematicReviewStatus(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    public function updateSystematicReviewContainer(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5240',
        ]);
        try {
            $existing = CMS::where('page', $this->p())->where('section', Section::PartnershipPageSystematicReviewSection->value)->first();
            if ($request->hasFile('image')) {
                if ($existing?->image) {
                    Helper::fileDelete(public_path($existing->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_systematic_review', time() . '_' . getFileName($request->file('image')));
            }
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageSystematicReviewSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.systematic_review_section.index')->with('success', 'Container updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateSystematicReviewContainer ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.systematic_review_section.index')->with('error', 'Container update failed');
        }
    }

    // ================= Partnership Model (container + items) =================
    public function partnershipModelIndex(Request $request)
    {
        $containerData = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPagePartnershipModelSection)->first();
        if ($request->ajax()) {
            $data = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPagePartnershipModelSectionContent)->orderBy('id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.partnership_page.partnership_model_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('backend.layouts.cms.partnership_page.partnership_model_section.index', compact('containerData'));
    }

    public function partnershipModelCreate()
    {
        return view('backend.layouts.cms.partnership_page.partnership_model_section.create');
    }

    public function partnershipModelStore(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
        ]);
        try {
            $validatedData['page'] = $this->p();
            $validatedData['section'] = Section::PartnershipPagePartnershipModelSectionContent->value;
            $validatedData['status'] = 'active';
            CMS::create($validatedData);
            return redirect()->route('cms.partnership_page.partnership_model_section.index')->with('success', 'Item created successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::partnershipModelStore ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.partnership_model_section.index')->with('error', 'Create failed')->withInput();
        }
    }

    public function partnershipModelEdit(string $id)
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPagePartnershipModelSectionContent->value)->findOrFail($id);
        return view('backend.layouts.cms.partnership_page.partnership_model_section.edit', compact('data'));
    }

    public function partnershipModelUpdate(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
        ]);
        try {
            CMS::findOrFail($id)->update($validatedData);
            flash()->success('Item updated successfully');
            return redirect()->route('cms.partnership_page.partnership_model_section.index');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::partnershipModelUpdate ' . $e->getMessage());
            flash()->error('Update failed');
            return redirect()->route('cms.partnership_page.partnership_model_section.index');
        }
    }

    public function partnershipModelDestroy(string $id)
    {
        CMS::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    public function partnershipModelStatus(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    public function updatePartnershipModelContainer(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        try {
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPagePartnershipModelSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.partnership_model_section.index')->with('success', 'Container updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updatePartnershipModelContainer ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.partnership_model_section.index')->with('error', 'Container update failed');
        }
    }

    // ================= Research Opportunitie (container + items image, title) =================
    public function researchOpportunitieIndex(Request $request)
    {
        $containerData = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPageResearchOpportunitieSection)->first();
        if ($request->ajax()) {
            $data = CMS::where('page', Page::PartnershipPage)->where('section', Section::PartnershipPageResearchOpportunitieSectionContent)->orderBy('id')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if (! empty($row->image) && file_exists(public_path($row->image))) {
                        return '<img src="' . asset($row->image) . '" class="wh-40 rounded-3" alt="image">';
                    }
                    return '<span class="text-muted small">No Image</span>';
                })
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.partnership_page.research_opportunitie_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action', 'image'])
                ->make(true);
        }
        return view('backend.layouts.cms.partnership_page.research_opportunitie_section.index', compact('containerData'));
    }

    public function researchOpportunitieCreate()
    {
        return view('backend.layouts.cms.partnership_page.research_opportunitie_section.create');
    }

    public function researchOpportunitieStore(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5240',
        ]);
        try {
            $validatedData['page'] = $this->p();
            $validatedData['section'] = Section::PartnershipPageResearchOpportunitieSectionContent->value;
            $validatedData['status'] = 'active';
            if ($request->hasFile('image')) {
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_research_opp', time() . '_' . getFileName($request->file('image')));
            }
            CMS::create($validatedData);
            return redirect()->route('cms.partnership_page.research_opportunitie_section.index')->with('success', 'Item created successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::researchOpportunitieStore ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.research_opportunitie_section.index')->with('error', 'Create failed')->withInput();
        }
    }

    public function researchOpportunitieEdit(string $id)
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPageResearchOpportunitieSectionContent->value)->findOrFail($id);
        return view('backend.layouts.cms.partnership_page.research_opportunitie_section.edit', compact('data'));
    }

    public function researchOpportunitieUpdate(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5240',
        ]);
        try {
            $row = CMS::findOrFail($id);
            if ($request->hasFile('image')) {
                if ($row->image) {
                    Helper::fileDelete(public_path($row->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_research_opp', time() . '_' . getFileName($request->file('image')));
            }
            $row->update($validatedData);
            flash()->success('Item updated successfully');
            return redirect()->route('cms.partnership_page.research_opportunitie_section.index');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::researchOpportunitieUpdate ' . $e->getMessage());
            flash()->error('Update failed');
            return redirect()->route('cms.partnership_page.research_opportunitie_section.index');
        }
    }

    public function researchOpportunitieDestroy(string $id)
    {
        $data = CMS::findOrFail($id);
        if ($data->image) {
            Helper::fileDelete(public_path($data->image));
        }
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    public function researchOpportunitieStatus(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    public function updateResearchOpportunitieContainer(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
        ]);
        try {
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPageResearchOpportunitieSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.research_opportunitie_section.index')->with('success', 'Container updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updateResearchOpportunitieContainer ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.research_opportunitie_section.index')->with('error', 'Container update failed');
        }
    }

    // ================= Partner Model (container title + items title, description, image) =================
    public function partnerModelIndex(Request $request)
    {
        $containerData = CMS::where('page', Page::PartnershipPage)
            ->where('section', Section::PartnershipPagePartnerModelSection)
            ->first();

        if ($request->ajax()) {
            $data = CMS::where('page', Page::PartnershipPage)
                ->where('section', Section::PartnershipPagePartnerModelSectionContent)
                ->orderBy('id')
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('description', function ($row) {
                    $plain = strip_tags($row->description ?? '');

                    return Str::limit($plain, 100);
                })
                ->addColumn('image', function ($row) {
                    if (! empty($row->image) && file_exists(public_path($row->image))) {
                        return '<img src="' . asset($row->image) . '" class="wh-40 rounded-3" alt="image">';
                    }

                    return '<span class="text-muted small">No Image</span>';
                })
                ->addColumn('status', function ($row) {
                    return '<div class="form-check form-switch"><input onclick="changeStatus(event,' . $row->id . ')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"' . ($row->status == 'active' ? ' checked' : '') . '></div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="action-wrapper">
                        <a type="button" href="' . route('cms.partnership_page.partner_model_section.edit', $row->id) . '" class="ps-0 border-0 bg-transparent lh-1"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>
                        <button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,' . $row->id . ')"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>
                    </div>';
                })
                ->rawColumns(['status', 'action', 'image'])
                ->make(true);
        }

        return view('backend.layouts.cms.partnership_page.partner_model_section.index', compact('containerData'));
    }

    public function partnerModelCreate()
    {
        return view('backend.layouts.cms.partnership_page.partner_model_section.create');
    }

    public function partnerModelStore(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5240',
        ]);
        try {
            $validatedData['page'] = $this->p();
            $validatedData['section'] = Section::PartnershipPagePartnerModelSectionContent->value;
            $validatedData['status'] = 'active';
            if ($request->hasFile('image')) {
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_partner_model', time() . '_' . getFileName($request->file('image')));
            }
            CMS::create($validatedData);
            return redirect()->route('cms.partnership_page.partner_model_section.index')->with('success', 'Item created successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::partnerModelStore ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.partner_model_section.index')->with('error', 'Create failed')->withInput();
        }
    }

    public function partnerModelEdit(string $id)
    {
        $data = CMS::where('page', $this->p())->where('section', Section::PartnershipPagePartnerModelSectionContent->value)->findOrFail($id);
        return view('backend.layouts.cms.partnership_page.partner_model_section.edit', compact('data'));
    }

    public function partnerModelUpdate(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5240',
        ]);
        try {
            $row = CMS::findOrFail($id);
            if ($request->hasFile('image')) {
                if ($row->image) {
                    Helper::fileDelete(public_path($row->image));
                }
                $validatedData['image'] = Helper::fileUpload($request->file('image'), 'partnership_partner_model', time() . '_' . getFileName($request->file('image')));
            }
            $row->update($validatedData);
            flash()->success('Item updated successfully');
            return redirect()->route('cms.partnership_page.partner_model_section.index');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::partnerModelUpdate ' . $e->getMessage());
            flash()->error('Update failed');
            return redirect()->route('cms.partnership_page.partner_model_section.index');
        }
    }

    public function partnerModelDestroy(string $id)
    {
        $data = CMS::findOrFail($id);
        if ($data->image) {
            Helper::fileDelete(public_path($data->image));
        }
        $data->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }

    public function partnerModelStatus(Request $request, $id)
    {
        $data = CMS::findOrFail($id);
        $data->status = $data->status == 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json(['success' => true, 'message' => 'Status changed successfully.']);
    }

    public function updatePartnerModelContainer(Request $request)
    {
        $validatedData = $request->validate(['title' => 'nullable|string|max:255']);
        try {
            CMS::updateOrCreate(
                ['page' => $this->p(), 'section' => Section::PartnershipPagePartnerModelSection->value],
                $validatedData
            );
            return redirect()->route('cms.partnership_page.partner_model_section.index')->with('success', 'Container updated successfully');
        } catch (Exception $e) {
            Log::error('PartnershipPageController::updatePartnerModelContainer ' . $e->getMessage());
            return redirect()->route('cms.partnership_page.partner_model_section.index')->with('error', 'Container update failed');
        }
    }
}
