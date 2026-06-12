@php
/** @var \App\Models\Offer|null $data */
$offer = $data ?? null;
$iconRaw = $offer?->getRawOriginal('icon');
$iconIsImage = $offer && $offer->isIconImage();
@endphp

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0">Offer details</h5>
            <div class="text-muted small">Fields marked <span class="text-danger">*</span> are required</div>
        </div>
        <hr class="mt-2 mb-0">
    </div>

    <div class="col-lg-8">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $offer?->title) }}" placeholder="e.g. Summer Package - 10% off" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $offer?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $offer?->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                  placeholder="Offer description shown on the app card">{{ old('description', $offer?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label">Promo code <span class="text-danger">*</span></label>
        <input type="text" name="promo_code" class="form-control @error('promo_code') is-invalid @enderror"
               value="{{ old('promo_code', $offer?->promo_code) }}" placeholder="SUMMER10" required>
        @error('promo_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label">Discount %</label>
        <input type="number" name="discount_percentage" min="1" max="100" class="form-control @error('discount_percentage') is-invalid @enderror"
               value="{{ old('discount_percentage', $offer?->discount_percentage) }}" placeholder="10">
        @error('discount_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label">Sort order</label>
        <input type="number" name="sort_order" min="0" class="form-control"
               value="{{ old('sort_order', $offer?->sort_order ?? 0) }}">
    </div>

    <div class="col-lg-6">
        <label class="form-label">End date <span class="text-danger">*</span></label>
        <input type="datetime-local" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
               value="{{ old('end_date', $offer?->end_date ? $offer->end_date->format('Y-m-d\TH:i') : '') }}" required>
        @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-6">
        <label class="form-label">Card background color</label>
        <input type="color" name="background_color" class="form-control form-control-color w-100"
               value="{{ old('background_color', $offer?->background_color ?? '#E3F2FD') }}">
    </div>

    <div class="col-12">
        <h5 class="mb-0">Icon <span class="text-muted fw-normal">(optional)</span></h5>
        <hr class="mt-2 mb-0">
        <p class="text-muted small mb-3">Use an emoji (e.g. 🚀) or upload an image. Leave both empty for no icon.</p>
    </div>

    <div class="col-lg-4">
        <label class="form-label">Emoji icon</label>
        <input type="text" name="icon_emoji" class="form-control @error('icon_emoji') is-invalid @enderror"
               value="{{ old('icon_emoji', (!$iconIsImage && $iconRaw) ? $iconRaw : '') }}"
               placeholder="🚀" maxlength="32">
        @error('icon_emoji')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label">Image icon</label>
        <input type="file" name="icon" class="form-control dropify @error('icon') is-invalid @enderror"
               data-height="120"
               data-default-file="{{ $iconIsImage && $iconRaw ? asset($iconRaw) : '' }}">
        @error('icon')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    @if($offer && $iconRaw)
        <div class="col-lg-4 d-flex align-items-end">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remove_icon" value="1" id="remove_icon">
                <label class="form-check-label" for="remove_icon">Remove current icon</label>
            </div>
        </div>
    @endif
</div>
