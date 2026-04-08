@php
/** @var \App\Models\Lesson|null $data */
$l = $data ?? null;
$videoLink = (!empty($l?->video) && !str_starts_with((string) $l?->video, 'http')) ? asset('storage/'.$l->video) : ($l?->video ?? null);
$docLink = (!empty($l?->document) && !str_starts_with((string) $l?->document, 'http')) ? asset('storage/'.$l->document) : ($l?->document ?? null);
$attachedLink = (!empty($l?->attached_file) && !str_starts_with((string) $l?->attached_file, 'http')) ? asset('storage/'.$l->attached_file) : ($l?->attached_file ?? null);
$fileLink = (!empty($l?->file) && !str_starts_with((string) $l?->file, 'http')) ? asset('storage/'.$l->file) : ($l?->file ?? null);
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
        <label class="form-label">Video (upload)</label>
        <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska">
        @if(!empty($videoLink))
            <small class="text-muted d-block mt-1">Current: <a href="{{ $videoLink }}" target="_blank">Open</a></small>
        @endif
    </div>
    <div class="col-md-6">
        <label class="form-label">File (upload)</label>
        <input type="file" name="file" class="form-control">
        @if(!empty($fileLink))
            <small class="text-muted d-block mt-1">Current: <a href="{{ $fileLink }}" target="_blank">Open</a></small>
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
        <label class="form-label">Document (upload)</label>
        <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
        @if(!empty($docLink))
            <small class="text-muted d-block mt-1">Current: <a href="{{ $docLink }}" target="_blank">Open</a></small>
        @endif
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

