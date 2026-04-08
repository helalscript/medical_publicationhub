@php
/** @var \App\Models\Module|null $data */
$m = $data ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Course <span class="text-danger">*</span></label>
        <select name="course_id" class="form-select" required>
            <option value="">Select course</option>
            @foreach($courses as $c)
                <option value="{{ $c->id }}" @selected(old('course_id', $m?->course_id ?? request('course_id')) == $c->id)>{{ $c->title }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            @foreach(['video'=>'Video','document'=>'Document','mixed'=>'Mixed'] as $k=>$v)
                <option value="{{ $k }}" @selected(old('type', $m?->type ?? 'video') === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $m?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $m?->status ?? 'active') === 'inactive')>Inactive</option>
        </select>
    </div>

    <div class="col-md-12">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $m?->title) }}" placeholder="Module title" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Video URL</label>
        <input type="text" name="video" class="form-control" value="{{ old('video', $m?->video) }}" placeholder="https://...">
    </div>
    <div class="col-md-6">
        <label class="form-label">Document URL</label>
        <input type="text" name="document" class="form-control" value="{{ old('document', $m?->document) }}" placeholder="https://...">
    </div>

    <div class="col-md-6">
        <label class="form-label">Attached file URL</label>
        <input type="text" name="attached_file" class="form-control" value="{{ old('attached_file', $m?->attached_file) }}" placeholder="https://...">
    </div>
    <div class="col-md-6">
        <label class="form-label">Durations</label>
        <input type="text" name="durations" class="form-control" value="{{ old('durations', $m?->durations) }}" placeholder="e.g. 12m">
    </div>

    <div class="col-md-12">
        <label class="form-label">Attached text</label>
        <textarea name="attached_text" class="form-control" rows="4" placeholder="Extra notes">{{ old('attached_text', $m?->attached_text) }}</textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label">Order</label>
        <input type="number" min="0" name="order_by" class="form-control" value="{{ old('order_by', $m?->order_by ?? 0) }}">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="is_locked" name="is_locked" value="1" @checked(old('is_locked', (int) ($m?->is_locked ?? 0)) == 1)>
            <label class="form-check-label" for="is_locked">Locked?</label>
        </div>
    </div>
</div>

