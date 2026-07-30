@php
    /** @var \App\Models\Opportunity|null $op */
    $op = isset($data) ? $data : null;
    /**
     * Serialized field map (UI + validation reference)
     *
     * Required:
     * - mentor_id, title, overview, price, dead_line, durations, start_date
     *
     * Optional:
     * - thumbnail, end_date, is_future, status
     * - desciptions (JSON array of {title, description})
     * - category_ids[], speciality_ids[], studydesign_type_ids[], contribution_type_ids[],
     *   university_hospital_ids[], place_ids[], experience_level_ids[]
     */
@endphp

@php
    $opTitle = isset($op) ? $op->title : null;
    $opOverview = isset($op) ? $op->overview : null;
    $opPrice = isset($op) ? $op->price : null;
    $opTotalSeats = isset($op) ? $op->total_seats : null;
    $opAvailableSeats = isset($op) ? $op->available_seats : null;
    $opStatus = isset($op) ? ($op->status ?? 'active') : 'active';
    $opMentorId = isset($op) ? $op->mentor_id : null;
    $opThumbnail = isset($op) ? $op->thumbnail : null;
    $opDeadline = (isset($op) && $op->dead_line) ? $op->dead_line->format('Y-m-d') : null;
    $opStart = (isset($op) && $op->start_date) ? $op->start_date->format('Y-m-d') : null;
    $opEnd = (isset($op) && $op->end_date) ? $op->end_date->format('Y-m-d') : null;
    $opDuration = isset($op) ? $op->durations : null;
    $opIsFuture = isset($op) ? (int) ($op->is_future ?? 0) : 0;
    $opDesc = isset($op) ? ($op->desciptions ? json_encode($op->desciptions) : '') : '';
    $opCategoryIds = isset($op) ? ($op->category_ids ?? []) : [];
    $opSpecialityIds = isset($op) ? ($op->speciality_ids ?? []) : [];
    $opStudyDesignTypeIds = isset($op) ? ($op->studydesign_type_ids ?? []) : [];
    $opContributionTypeIds = isset($op) ? ($op->contribution_type_ids ?? []) : [];
    $opUniversityHospitalIds = isset($op) ? ($op->university_hospital_ids ?? []) : [];
    $opPlaceIds = isset($op) ? ($op->place_ids ?? []) : [];
    $opExperienceLevelIds = isset($op) ? ($op->experience_level_ids ?? []) : [];
    $opIsVerified = isset($op) ? (int) ($op->is_verified ?? 0) : 0;
    $opVerificationDeadline = (isset($op) && $op->verification_deadline) ? $op->verification_deadline->format('Y-m-d') : null;
    $opCoordinatorId = isset($op) ? $op->coordinator_id : null;
@endphp

<div class="row">
    <div class="col-lg-12 mb-2">
        <h6 class="mb-0">Basic info</h6>
        <small class="text-muted">Fields marked with <span class="text-danger">*</span> are required.</small>
    </div>
    <div class="col-md-7 mb-3">
        <label class="label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               placeholder="e.g. Research Mentor Program"
               value="{{ old('title', $opTitle) }}" required>
        <small class="text-muted">Required</small>
    </div>
    <div class="col-md-5 mb-3">
        <label class="label">Overview <span class="text-danger">*</span></label>
        <textarea name="overview" rows="2" class="form-control"
                  placeholder="Short summary shown in cards/lists"
                  required>{{ old('overview', $opOverview) }}</textarea>
        <small class="text-muted">Required</small>
    </div>
    <div class="col-md-2 mb-3">
        <label class="label">Price <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="price" class="form-control"
               placeholder="e.g. 199.00"
               value="{{ old('price', $opPrice) }}" required>
        <small class="text-muted">Required (0 = free)</small>
    </div>
    <div class="col-md-2 mb-3">
        <label class="label">Total Seats</label>
        <input type="number" min="1" name="total_seats" class="form-control"
               placeholder="e.g. 20"
               value="{{ old('total_seats', $opTotalSeats) }}">
        <small class="text-muted">Leave blank = unlimited</small>
    </div>
    @if(isset($op) && $opTotalSeats !== null)
    <div class="col-md-2 mb-3">
        <label class="label">Available Seats</label>
        <input type="number" min="0" name="available_seats" class="form-control"
               placeholder=""
               value="{{ old('available_seats', $opAvailableSeats) }}">
        <small class="text-muted">Remaining seats</small>
    </div>
    @endif
    <div class="col-md-2 mb-3">
        <label class="label">Status</label>
        <select name="status" class="form-select">
            <option value="active" {{ old('status', $opStatus) == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $opStatus) == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <small class="text-muted">Optional</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="label">Mentor <span class="text-danger">*</span></label>
        <select name="mentor_id" class="form-select js-select2" data-placeholder="Search mentor..." required>
            <option value="">Select mentor</option>
            @foreach (($mentors ?? collect()) as $m)
                <option value="{{ $m->id }}" {{ (string)old('mentor_id', $opMentorId) === (string)$m->id ? 'selected' : '' }}>
                    #{{ $m->id }} - {{ $m->user?->name ?? '—' }} ({{ $m->user?->email ?? '-' }})
                </option>
            @endforeach
        </select>
        <small class="text-muted">Required</small>
    </div>
    <div class="col-md-12 mb-6">
        <label class="label">Thumbnail</label>
        <input class="dropify form-control" type="file" name="thumbnail" accept="image/*"
               data-default-file="{{ !empty($opThumbnail) ? asset($opThumbnail) : '' }}">
        <small class="text-muted">Optional (jpg/png/webp)</small>
    </div>
</div>

<div class="row mt-2">
    <div class="col-lg-12 mb-2">
        <h6 class="mb-0">Schedule</h6>
        <small class="text-muted">Defaults will be auto-selected, but you can change them.</small>
    </div>
    <div class="col-md-3 mb-3">
        <label class="label">Deadline <span class="text-danger">*</span></label>
        <input type="date" name="dead_line" id="dead_line" class="form-control"
               value="{{ old('dead_line', $opDeadline) }}" required>
        <small class="text-muted">Required</small>
    </div>
    <div class="col-md-3 mb-3">
        <label class="label">Start date <span class="text-danger">*</span></label>
        <input type="date" name="start_date" id="start_date" class="form-control"
               value="{{ old('start_date', $opStart) }}" required>
        <small class="text-muted">Required</small>
    </div>
    <div class="col-md-3 mb-3">
        <label class="label">End date</label>
        <input type="date" name="end_date" id="end_date" class="form-control"
               value="{{ old('end_date', $opEnd) }}">
        <small class="text-muted">Optional</small>
    </div>
    <div class="col-md-3 mb-3">
        <label class="label">Duration <span class="text-danger">*</span></label>
        <input type="text" name="durations" class="form-control"
               placeholder="e.g. 12 weeks / 3 months"
               value="{{ old('durations', $opDuration) }}" required>
        <small class="text-muted">Required</small>
    </div>
    <div class="col-md-3 mb-3">
        <label class="label">Is Future</label>
        <select name="is_future" class="form-select">
            <option value="0" {{ old('is_future', $opIsFuture) == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ old('is_future', $opIsFuture) == 1 ? 'selected' : '' }}>Yes</option>
        </select>
        <small class="text-muted">Optional</small>
    </div>
</div>

<div class="row mt-2">
    <div class="col-lg-12 mb-2">
        <h6 class="mb-0">Verification & Assignment</h6>
        <small class="text-muted">Fields to verify the opportunity and assign a coordinator.</small>
    </div>
    <div class="col-md-3 mb-3">
        <label class="label">Verified</label>
        <select name="is_verified" class="form-select">
            <option value="0" {{ old('is_verified', $opIsVerified) == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ old('is_verified', $opIsVerified) == 1 ? 'selected' : '' }}>Yes</option>
        </select>
        <small class="text-muted">Must have price > 0 to verify.</small>
    </div>
    <div class="col-md-3 mb-3">
        <label class="label">Verification Deadline</label>
        <input type="date" name="verification_deadline" class="form-control"
               value="{{ old('verification_deadline', $opVerificationDeadline) }}">
        <small class="text-muted">Required if Verified is Yes.</small>
    </div>
    <div class="col-md-6 mb-3">
        <label class="label">Coordinator</label>
        <select name="coordinator_id" class="form-select js-select2" data-placeholder="Select coordinator...">
            <option value="">None</option>
            @foreach (($coordinators ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ (string)old('coordinator_id', $opCoordinatorId) === (string)$c->id ? 'selected' : '' }}>
                    #{{ $c->id }} - {{ $c->name }} ({{ $c->email }})
                </option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>
</div>

<div class="mb-3">
    <label class="label">Descriptions</label>
    <input type="hidden" name="desciptions" id="desciptions_json"
        value="{{ old('desciptions', $opDesc) }}">
    <div id="desciptions_builder" class="border rounded-3 p-3"></div>
    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add_desciptions_item">+ Add</button>
    <small class="text-muted d-block mt-1">Optional. Add one or more description blocks.</small>
</div>

<div class="row mt-2">
    <div class="col-lg-12 mb-2">
        <h6 class="mb-0">Filters & tags</h6>
        <small class="text-muted">All fields below are optional and support search + multi-select.</small>
    </div>
    <div class="col-md-6 mb-3">
        <label class="label">Categories (course)</label>
        <select name="category_ids[]" class="form-select js-select2" multiple data-placeholder="Select one or more categories...">
            @foreach (($categories['course'] ?? collect()) as $c)
                <option value="{{ $c->id }}"
                    {{ in_array($c->id, (array)old('category_ids', $opCategoryIds), true) ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="label">Speciality</label>
        <select name="speciality_ids[]" class="form-select js-select2" multiple data-placeholder="Select one or more specialities...">
            @foreach (($categories['speciality'] ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, (array)old('speciality_ids', $opSpecialityIds), true) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="label">Study design type</label>
        <select name="studydesign_type_ids[]" class="form-select js-select2" multiple data-placeholder="Select one or more study design types...">
            @foreach (($categories['studydesign_type'] ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, (array)old('studydesign_type_ids', $opStudyDesignTypeIds), true) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="label">Contribution type</label>
        <select name="contribution_type_ids[]" class="form-select js-select2" multiple data-placeholder="Select one or more contribution types...">
            @foreach (($categories['contribution_type'] ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, (array)old('contribution_type_ids', $opContributionTypeIds), true) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="label">University / hospital</label>
        <select name="university_hospital_ids[]" class="form-select js-select2" multiple data-placeholder="Select one or more universities/hospitals...">
            @foreach (($categories['university_hospital'] ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, (array)old('university_hospital_ids', $opUniversityHospitalIds), true) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="label">Place</label>
        <select name="place_ids[]" class="form-select js-select2" multiple data-placeholder="Select one or more places...">
            @foreach (($categories['place'] ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, (array)old('place_ids', $opPlaceIds), true) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>

    <div class="col-md-6 mb-3">
        <label class="label">Experience level</label>
        <select name="experience_level_ids[]" class="form-select js-select2" multiple data-placeholder="Select one or more experience levels...">
            @foreach (($categories['experience_level'] ?? collect()) as $c)
                <option value="{{ $c->id }}" {{ in_array($c->id, (array)old('experience_level_ids', $opExperienceLevelIds), true) ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">Optional</small>
    </div>
</div>

