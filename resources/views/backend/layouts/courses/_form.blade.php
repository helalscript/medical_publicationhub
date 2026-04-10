@php
    /** @var \App\Models\Course|null $data */
    $course = $data ?? null;
    $metaAcademyCategories = $metaAcademyCategories ?? collect();
    $mentors = $mentors ?? collect();

    $cTitle = $course?->title;
    $cStatus = $course?->status ?? 'active';
    $cPrice = $course?->price;
    $cOldPrice = $course?->old_price;
    $cShort = $course?->short_description;
    $cOverview = $course?->overview;
    $cThumbDesc = $course?->thumbnail_description;
    $cThumbBtn = $course?->thumbnail_button_text;
    $cTotalDur = $course?->total_durations;
    $cStartMod = $course?->start_module ?? 1;
    $cIsFuture = (int) ($course?->is_future ?? 0);
    $cThumb = $course?->thumbnail;
    $cIntro = $course?->intro_video;
    $cThumbDefaultUrl = null;
    $cIntroCurrentUrl = null;

    if (!empty($cThumb)) {
        $cThumbDefaultUrl = str_starts_with((string) $cThumb, 'uploads/') ? asset($cThumb) : asset('storage/'.$cThumb);
    }
    if (!empty($cIntro)) {
        if (str_starts_with((string) $cIntro, 'http')) {
            $cIntroCurrentUrl = $cIntro;
        } elseif (str_starts_with((string) $cIntro, 'uploads/')) {
            $cIntroCurrentUrl = asset($cIntro);
        } else {
            $cIntroCurrentUrl = asset('storage/'.$cIntro);
        }
    }
    $cCatIds = (array) old('category_ids', $course?->category_ids ?? []);
    $cMentorIds = (array) old('mentor_ids', $course?->mentor_ids ?? []);
    $cDescJson = old('descriptions', $course?->descriptions ? json_encode($course->descriptions) : '');
@endphp

<div class="row g-4">
    <div class="col-12">
        <h5 class="mb-0">Basic info</h5>
        <hr class="mt-2 mb-3">
        <small class="text-muted"><span class="text-danger">*</span> Required fields.</small>
    </div>

    <div class="col-lg-8">
        <label class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $cTitle) }}" placeholder="e.g. Advanced Research Methods" required>
    </div>
    <div class="col-lg-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            <option value="active" @selected(old('status', $cStatus) === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $cStatus) === 'inactive')>Inactive</option>
        </select>
        <small class="text-muted">Optional</small>
    </div>

    {{-- <div class="col-lg-12">
        <label class="form-label">Short description <span class="text-muted">(optional)</span></label>
        <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $cShort) }}" placeholder="One line summary for cards">
    </div> --}}

    <div class="col-lg-12">
        <label class="form-label">Overview <span class="text-muted">(optional)</span></label>
        <textarea name="overview" class="form-control" rows="4" placeholder="Course overview">{{ old('overview', $cOverview) }}</textarea>
    </div>

    <div class="col-12 mt-2">
        <h5 class="mb-0">Pricing &amp; schedule</h5>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-md-4">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $cPrice) }}" placeholder="0.00">
    </div>
    <div class="col-md-4">
        <label class="form-label">Old price</label>
        <input type="number" step="0.01" name="old_price" class="form-control" value="{{ old('old_price', $cOldPrice) }}" placeholder="0.00">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch w-100">
            <input class="form-check-input" type="checkbox" role="switch" id="is_future" name="is_future" value="1" @checked(old('is_future', $cIsFuture) == 1)>
            <label class="form-check-label" for="is_future">Is future course?</label>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Total durations</label>
        <input type="text" name="total_durations" class="form-control" value="{{ old('total_durations', $cTotalDur) }}" placeholder="e.g. 12 weeks">
    </div>
    <div class="col-md-6">
        <label class="form-label">Start module</label>
        <input type="number" min="1" name="start_module" class="form-control" value="{{ old('start_module', $cStartMod) }}">
    </div>

    <div class="col-12 mt-2">
        <h5 class="mb-0">Media</h5>
        <hr class="mt-2 mb-3">
    </div>

    <div class="col-lg-6">
        <label class="form-label">Thumbnail <span class="text-muted">(optional)</span></label>
        <input type="file" name="thumbnail" class="form-control dropify" accept="image/*"
               data-height="200"
               data-default-file="{{ !empty($cThumbDefaultUrl) ? $cThumbDefaultUrl : '' }}">
        <small class="text-muted">JPG, PNG, WebP — Dropify preview</small>
    </div>

    <div class="col-lg-6">
        <label class="form-label">Intro video <span class="text-muted">(optional)</span></label>
        <input type="file" name="intro_video" class="form-control" accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska">
        <small class="text-muted">MP4, WebM, MOV, AVI, MKV — max ~100MB</small>
        @if(!empty($cIntro))
            <div class="mt-2 small">
                @if(str_starts_with((string) $cIntro, 'http'))
                    <span class="text-muted">Current:</span> <a href="{{ $cIntroCurrentUrl }}" target="_blank" rel="noopener">Open link</a>
                @else
                    <span class="text-muted">Current file:</span> <a href="{{ $cIntroCurrentUrl }}" target="_blank">View / download</a>
                @endif
            </div>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label">Thumbnail description</label>
        <input type="text" name="thumbnail_description" class="form-control" value="{{ old('thumbnail_description', $cThumbDesc) }}" placeholder="Text under thumbnail">
    </div>
    <div class="col-md-6">
        <label class="form-label">Thumbnail button text</label>
        <input type="text" name="thumbnail_button_text" class="form-control" value="{{ old('thumbnail_button_text', $cThumbBtn) }}" placeholder="e.g. Enroll now">
    </div>

    <div class="col-12 mt-2">
        <h5 class="mb-0">Categories &amp; mentors</h5>
        <hr class="mt-2 mb-3">
        <small class="text-muted">Meta Academy categories and mentors — search + multi-select (optional).</small>
    </div>

    <div class="col-lg-6">
        <label class="form-label">Categories (Meta Academy)</label>
        <select name="category_ids[]" class="form-select js-select2" multiple data-placeholder="Search meta academy categories...">
            <option value=""></option>
            @foreach ($metaAcademyCategories as $c)
                <option value="{{ $c->id }}" @selected(in_array((int) $c->id, array_map('intval', $cCatIds), true))>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-6">
        <label class="form-label">Mentors</label>
        <select name="mentor_ids[]" class="form-select js-select2" multiple data-placeholder="Search mentors...">
            <option value=""></option>
            @foreach ($mentors as $m)
                <option value="{{ $m->id }}" @selected(in_array((int) $m->id, array_map('intval', $cMentorIds), true))>
                    #{{ $m->id }} — {{ $m->user?->name ?? '—' }} @if($m->user) ({{ $m->user->email }}) @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 mt-2">
        <h5 class="mb-0">Description blocks</h5>
        <hr class="mt-2 mb-3">
        <small class="text-muted">Optional. Each block has a title and rich text (stored as JSON).</small>
    </div>

    <div class="col-12">
        <input type="hidden" name="descriptions" id="course_descriptions_json" value="{{ e($cDescJson) }}">
        <div id="course_descriptions_builder" class="border rounded-3 p-3 bg-light"></div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add_course_description_item">+ Add block</button>
    </div>
</div>
