@extends('backend.app')
@section('title', 'Create Mentor')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Create Mentor User</h3>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('user_management.mentor.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h6 class="mb-3">Account</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="label">Account name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Enter account name"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="Enter email"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Phone <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="Enter phone number"
                                   class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Gender <span class="text-muted">(Optional)</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="others" {{ old('gender') == 'others' ? 'selected' : '' }}>Others</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">User avatar <span class="text-muted">(Optional)</span></label>
                            <input type="file" name="avatar" data-height="160"
                                   class="dropify form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   placeholder="Enter password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="mb-3 mt-2">Mentor profile</h6>
                    <p class="text-muted small mb-3">Public name and photo use the <strong>Account name</strong> and <strong>User avatar</strong> fields above.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="label">University <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="university" value="{{ old('university') }}"
                                   placeholder="Enter university"
                                   class="form-control @error('university') is-invalid @enderror">
                            @error('university')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">Department <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="department" value="{{ old('department') }}"
                                   placeholder="Enter department"
                                   class="form-control @error('department') is-invalid @enderror">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="label">Bio <span class="text-muted">(Optional)</span></label>
                            <textarea name="bio" rows="3"
                                      placeholder="Write a short bio"
                                      class="form-control @error('bio') is-invalid @enderror">{{ old('bio') }}</textarea>
                            @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="label">Social links (JSON array, e.g. [{"label":"LinkedIn","url":"https://..."}])</label>
                            <textarea name="social_links" rows="3"
                                      class="form-control @error('social_links') is-invalid @enderror"
                                      placeholder='[]'>{{ old('social_links') }}</textarea>
                            @error('social_links')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary">Save</button>
                        <a href="{{ route('user_management.mentor.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.dropify').dropify();
        });
    </script>
@endpush
