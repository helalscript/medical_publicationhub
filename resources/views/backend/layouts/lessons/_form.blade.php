@php
/** @var \App\Models\Lesson|null $data */
$l = $data ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Module <span class="text-danger">*</span></label>
        <select name="module_id" class="form-select" required>
            <option value="">Select module</option>
            @foreach($modules as $m)
                <option value="{{ $m->id }}" @selected(old('module_id', $l?->module_id ?? request('module_id')) == $m->id)>{{ $m->course?->title ? $m->course->title.' — ' : '' }}{{ $m->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $l?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $l?->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
    </div>

    <div class="col-md-12">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $l?->title) }}" placeholder="Lesson title">
    </div>

    <div class="col-md-6">
        <label class="form-label">Video URL</label>
        <input type="text" name="video" class="form-control" value="{{ old('video', $l?->video) }}" placeholder="https://...">
    </div>
    <div class="col-md-6">
        <label class="form-label">File URL</label>
        <input type="text" name="file" class="form-control" value="{{ old('file', $l?->file) }}" placeholder="https://...">
    </div>

    <div class="col-md-6">
        <label class="form-label">Attached file URL</label>
        <input type="text" name="attached_file" class="form-control" value="{{ old('attached_file', $l?->attached_file) }}" placeholder="https://...">
    </div>
    <div class="col-md-6">
        <label class="form-label">Document URL</label>
        <input type="text" name="document" class="form-control" value="{{ old('document', $l?->document) }}" placeholder="https://...">
    </div>

    <div class="col-md-12">
        <label class="form-label">Attached text</label>
        <textarea name="attached_text" class="form-control" rows="4" placeholder="Extra notes">{{ old('attached_text', $l?->attached_text) }}</textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label">Order</label>
        <input type="number" min="0" name="order_by" class="form-control" value="{{ old('order_by', $l?->order_by ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">Durations</label>
        <input type="text" name="durations" class="form-control" value="{{ old('durations', $l?->durations) }}" placeholder="e.g. 5m">
    </div>
</div>

