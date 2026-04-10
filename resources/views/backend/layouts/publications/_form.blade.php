@php
/** @var \App\Models\Publication|null $data */
$p = $data ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-9">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $p?->title) }}" placeholder="Publication title" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $p?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $p?->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
    </div>

    <div class="col-md-12">
        <label class="form-label">Categories <span class="text-muted fw-normal">(optional)</span></label>
        <select name="category_ids[]" id="publicationCategoryIds" class="form-select js-select2-publication-categories" multiple
                data-placeholder="Search and select publication categories...">
            <option value=""></option>
            @php
                $selectedRaw = old('category_ids', (array) ($p?->category_ids ?? []));
                $selected = array_map('intval', $selectedRaw);
            @endphp
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(in_array((int) $c->id, $selected, true))>{{ $c->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Publication-type categories only. Use search to filter.</small>
    </div>

    <div class="col-md-12">
        <label class="form-label">Overview</label>
        <textarea name="overview" class="form-control" rows="3" placeholder="Short overview">{{ old('overview', $p?->overview) }}</textarea>
    </div>

    <div class="col-md-12">
        <label class="form-label">Description (text editor)</label>
        <div id="pubEditor" style="height: 220px;" class="bg-white border rounded-3"></div>
        <input type="hidden" name="description" id="descriptionInput" value="{{ old('description', $p?->description) }}">
    </div>

    <div class="col-md-12">
        <label class="form-label">Attachment (optional)</label>
        <input type="file" name="attachment" class="form-control">
        @if(!empty($p?->attachment))
            <small class="text-muted d-block mt-1">Current: <a href="{{ asset($p->attachment) }}" target="_blank">Download</a></small>
        @endif
    </div>
</div>

