@php
/** @var \App\Models\Service|null $data */
$s = $data ?? null;
@endphp

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0">Basic info</h5>
            <div class="text-muted small">Fields marked <span class="text-danger">*</span> are required</div>
        </div>
        <hr class="mt-2 mb-0">
    </div>

    <div class="col-lg-9">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="{{ old('title', $s?->title) }}" placeholder="Service title" required>
    </div>
    <div class="col-lg-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $s?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $s?->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
    </div>

    <div class="col-12">
        <h5 class="mb-0">Pricing & contact</h5>
        <hr class="mt-2 mb-0">
    </div>

    <div class="col-lg-4">
        <label class="form-label">Icon (image)</label>
        <input type="file" name="icon" class="form-control dropify"
               data-height="180"
               data-default-file="{{ $s?->icon ? asset($s->icon) : '' }}">
        <small class="text-muted d-block mt-1">Recommended: square PNG/WebP.</small>
    </div>
    <div class="col-lg-4">
        <label class="form-label">Starting price</label>
        <input type="number" step="0.01" name="starting_price" class="form-control"
               value="{{ old('starting_price', $s?->starting_price) }}" placeholder="0.00">
    </div>
    <div class="col-lg-4">
        <label class="form-label">WhatsApp number <span class="text-muted">(optional)</span></label>
        <input type="text" name="whats_app_num" class="form-control"
               value="{{ old('whats_app_num', $s?->whats_app_num) }}" placeholder="e.g. +9665...">
        <small class="text-muted">If empty, API will use default from System Settings.</small>
    </div>

    <div class="col-12">
        <h5 class="mb-0">Content</h5>
        <hr class="mt-2 mb-0">
    </div>

    <div class="col-12">
        <label class="form-label">Overview</label>
        <textarea name="overview" class="form-control" rows="3" placeholder="Short overview">{{ old('overview', $s?->overview) }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Description (text editor)</label>
        <div id="serviceEditor" style="height: 240px;" class="bg-white border rounded-3"></div>
        <input type="hidden" name="description" id="descriptionInput" value="{{ old('description', $s?->description) }}">
    </div>

    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 border rounded-3 p-3 bg-light">
            <div>
                <div class="fw-semibold">Featured service</div>
                <div class="text-muted small">Show this service in featured sections.</div>
            </div>
            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="is_features" name="is_features" value="1"
                       @checked(old('is_features', (int) ($s?->is_features ?? 0)) == 1)>
                <label class="form-check-label" for="is_features">Enabled</label>
            </div>
        </div>
    </div>
</div>

