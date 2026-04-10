@php
/** @var \App\Models\Module|null $data */
$m = $data ?? null;
$videoLink = (!empty($m?->video) && !str_starts_with((string) $m?->video, 'http')) ? asset('storage/'.$m->video) : ($m?->video ?? null);
$docLink = (!empty($m?->document) && !str_starts_with((string) $m?->document, 'http')) ? asset('storage/'.$m->document) : ($m?->document ?? null);
$attachedLink = (!empty($m?->attached_file) && !str_starts_with((string) $m?->attached_file, 'http')) ? asset('storage/'.$m->attached_file) : ($m?->attached_file ?? null);
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
        <label class="form-label">Video (upload)</label>
        <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska">
        @if(!empty($videoLink))
            <small class="text-muted d-block mt-1">Current: <a href="{{ $videoLink }}" target="_blank">Open</a></small>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label">Document (upload)</label>
        <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
        @if(!empty($docLink))
            <small class="text-muted d-block mt-1">Current: <a href="{{ $docLink }}" target="_blank">Open</a></small>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label">Attached file (upload)</label>
        <input type="file" name="attached_file" class="form-control">
        @if(!empty($attachedLink))
            <small class="text-muted d-block mt-1">Current: <a href="{{ $attachedLink }}" target="_blank">Open</a></small>
        @endif
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

