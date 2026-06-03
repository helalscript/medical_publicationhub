<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Offer::query()->orderBy('sort_order')->orderByDesc('id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('icon_display', function ($row) {
                    if (empty($row->getRawOriginal('icon'))) {
                        return '<span class="text-muted">—</span>';
                    }

                    if ($row->isIconImage()) {
                        return '<img src="'.e(asset($row->getRawOriginal('icon'))).'" alt="" style="width:36px;height:36px;object-fit:contain" class="rounded">';
                    }

                    return '<span class="fs-4">'.e($row->getRawOriginal('icon')).'</span>';
                })
                ->addColumn('promo_code_badge', fn ($row) => '<span class="badge bg-primary-subtle text-primary">'.e($row->promo_code).'</span>')
                ->addColumn('end_date_fmt', fn ($row) => $row->end_date?->format('Y-m-d H:i') ?? '-')
                ->addColumn('status', function ($row) {
                    $user = auth()->user();
                    if (! $user || ! $user->can('admin.offers.status')) {
                        $c = $row->status === 'active' ? 'success' : 'secondary';
                        $t = $row->status === 'active' ? 'Active' : 'Inactive';

                        return '<span class="badge bg-'.$c.'">'.$t.'</span>';
                    }

                    return '<div class="form-check form-switch">'
                        .'<input onclick="changeStatus(event,'.$row->id.')" type="checkbox" class="form-check-input" style="border-radius: 25rem;width:40px" name="status"'.($row->status == 'active' ? ' checked' : '').'>'
                        .'</div>';
                })
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    $buttons = [];

                    if ($user && $user->can('admin.offers.show')) {
                        $buttons[] = '<a href="'.route('admin.offers.show', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="View"><i class="material-symbols-outlined fs-16 text-primary">visibility</i></a>';
                    }
                    if ($user && $user->can('admin.offers.edit')) {
                        $buttons[] = '<a href="'.route('admin.offers.edit', $row->id).'" class="ps-0 border-0 bg-transparent lh-1" title="Edit"><i class="material-symbols-outlined fs-16 text-body">edit</i></a>';
                    }
                    if ($user && $user->can('admin.offers.destroy')) {
                        $buttons[] = '<button class="ps-0 border-0 bg-transparent lh-1" onclick="deleteRecord(event,'.$row->id.')" title="Delete"><i class="material-symbols-outlined fs-16 text-danger">delete</i></button>';
                    }

                    return count($buttons) === 0 ? '-' : '<div class="action-wrapper">'.implode('', $buttons).'</div>';
                })
                ->rawColumns(['icon_display', 'promo_code_badge', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.offers.index');
    }

    public function create()
    {
        return view('backend.layouts.offers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        try {
            $validated['icon'] = $this->resolveIcon($request, null);
            Offer::create($validated);

            return redirect()->route('admin.offers.index')->with('success', 'Offer created successfully.');
        } catch (Exception $e) {
            Log::error('OfferController::store '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Offer create failed.');
        }
    }

    public function show(string $offer)
    {
        $data = Offer::findOrFail($offer);

        return view('backend.layouts.offers.show', compact('data'));
    }

    public function edit(string $offer)
    {
        $data = Offer::findOrFail($offer);

        return view('backend.layouts.offers.edit', compact('data'));
    }

    public function update(Request $request, string $offer)
    {
        $validated = $this->validatePayload($request, $offer);

        try {
            $offer = Offer::findOrFail($offer);
            $validated['icon'] = $this->resolveIcon($request, $offer);
            $offer->update($validated);

            return redirect()->route('admin.offers.index')->with('success', 'Offer updated successfully.');
        } catch (Exception $e) {
            Log::error('OfferController::update '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Offer update failed.');
        }
    }

    public function destroy(string $offer)
    {
        try {
            $offer = Offer::findOrFail($offer);
            $this->deleteIconFile($offer);
            $offer->delete();

            return response()->json(['success' => true, 'message' => 'Offer deleted successfully.']);
        } catch (Exception $e) {
            Log::error('OfferController::destroy '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Offer delete failed.'], 500);
        }
    }

    public function status(Request $request, int $id)
    {
        try {
            $offer = Offer::findOrFail($id);
            $offer->status = $offer->status === 'active' ? 'inactive' : 'active';
            $offer->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (Exception $e) {
            Log::error('OfferController::status '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Status update failed.'], 500);
        }
    }

    private function validatePayload(Request $request, ?string $id = null): array
    {
        $endDateRules = ['required', 'date'];
        if (! $id) {
            $endDateRules[] = 'after:now';
        }

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon_emoji' => ['nullable', 'string', 'max:32'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'promo_code' => ['required', 'string', 'max:64'],
            'discount_percentage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'end_date' => $endDateRules,
            'background_color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
    }

    private function resolveIcon(Request $request, ?Offer $offer): ?string
    {
        if ($request->hasFile('icon')) {
            if ($offer && $offer->isIconImage()) {
                $this->deleteIconFile($offer);
            }

            return Helper::fileUpload(
                $request->file('icon'),
                'offers/icon',
                time().'_'.getFileName($request->file('icon'))
            );
        }

        if ($request->boolean('remove_icon')) {
            if ($offer) {
                $this->deleteIconFile($offer);
            }

            return null;
        }

        $emoji = trim((string) $request->input('icon_emoji', ''));
        if ($emoji !== '') {
            if ($offer && $offer->isIconImage()) {
                $this->deleteIconFile($offer);
            }

            return $emoji;
        }

        if ($offer) {
            return $offer->getRawOriginal('icon');
        }

        return null;
    }

    private function deleteIconFile(Offer $offer): void
    {
        if (! $offer->isIconImage()) {
            return;
        }

        $path = $offer->getRawOriginal('icon');
        if (! empty($path)) {
            Helper::fileDelete(public_path($path));
        }
    }
}
