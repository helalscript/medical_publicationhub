@php
/** @var \App\Models\Course|null $data */
$course = $data ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $course?->title) }}" placeholder="Course title" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $course?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $course?->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $course?->price) }}" placeholder="0.00">
    </div>
    <div class="col-md-4">
        <label class="form-label">Old price</label>
        <input type="number" step="0.01" name="old_price" class="form-control" value="{{ old('old_price', $course?->old_price) }}" placeholder="0.00">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="is_future" name="is_future" value="1" @checked(old('is_future', (int) ($course?->is_future ?? 0)) == 1)>
            <label class="form-check-label" for="is_future">Is future course?</label>
        </div>
    </div>

    <div class="col-md-12">
        <label class="form-label">Short description</label>
        <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $course?->short_description) }}" placeholder="One line summary">
    </div>

    <div class="col-md-12">
        <label class="form-label">Overview</label>
        <textarea name="overview" class="form-control" rows="4" placeholder="Course overview">{{ old('overview', $course?->overview) }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label">Intro video URL</label>
        <input type="text" name="intro_video" class="form-control" value="{{ old('intro_video', $course?->intro_video) }}" placeholder="https://...">
    </div>
    <div class="col-md-3">
        <label class="form-label">Total durations</label>
        <input type="text" name="total_durations" class="form-control" value="{{ old('total_durations', $course?->total_durations) }}" placeholder="e.g. 3h 20m">
    </div>
    <div class="col-md-3">
        <label class="form-label">Start module</label>
        <input type="number" min="1" name="start_module" class="form-control" value="{{ old('start_module', $course?->start_module ?? 1) }}">
    </div>

    <div class="col-md-12">
        <label class="form-label">Thumbnail</label>
        <input type="file" name="thumbnail" class="form-control dropify" data-default-file="{{ $course?->thumbnail ? asset($course->thumbnail) : '' }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Thumbnail description</label>
        <input type="text" name="thumbnail_description" class="form-control" value="{{ old('thumbnail_description', $course?->thumbnail_description) }}" placeholder="Small text under thumbnail">
    </div>
    <div class="col-md-6">
        <label class="form-label">Thumbnail button text</label>
        <input type="text" name="thumbnail_button_text" class="form-control" value="{{ old('thumbnail_button_text', $course?->thumbnail_button_text) }}" placeholder="e.g. Buy Now">
    </div>

    <div class="col-md-6">
        <label class="form-label">Category IDs (JSON)</label>
        <textarea name="category_ids" class="form-control" rows="2" placeholder="[1,2,3]">{{ old('category_ids', $course?->category_ids ? json_encode($course->category_ids) : '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Mentor IDs (JSON)</label>
        <textarea name="mentor_ids" class="form-control" rows="2" placeholder="[5,6]">{{ old('mentor_ids', $course?->mentor_ids ? json_encode($course->mentor_ids) : '') }}</textarea>
    </div>

    <div class="col-md-12">
        <label class="form-label">Descriptions blocks (JSON)</label>
        <textarea name="descriptions" class="form-control" rows="4" placeholder='[{"title":"Section title","description":"<p>Html</p>"}]'>{{ old('descriptions', $course?->descriptions ? json_encode($course->descriptions) : '') }}</textarea>
        <small class="text-muted">For now it’s JSON (same as Opportunities). Next we can add dynamic builder UI.</small>
    </div>
</div>

