@extends('backend.app')
@section('title', 'Create Mentor')

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
                            <label class="label">Account name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Gender</label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="others" {{ old('gender') == 'others' ? 'selected' : '' }}>Others</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">User avatar</label>
                            <input type="file" name="avatar"
                                   class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Password</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h6 class="mb-3 mt-2">Mentor profile</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="label">Mentor display name</label>
                            <input type="text" name="mentor_name" value="{{ old('mentor_name') }}"
                                   class="form-control @error('mentor_name') is-invalid @enderror" required>
                            @error('mentor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">Mentor avatar</label>
                            <input type="file" name="mentor_avatar"
                                   class="form-control @error('mentor_avatar') is-invalid @enderror">
                            @error('mentor_avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">University</label>
                            <input type="text" name="university" value="{{ old('university') }}"
                                   class="form-control @error('university') is-invalid @enderror">
                            @error('university')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">Department</label>
                            <input type="text" name="department" value="{{ old('department') }}"
                                   class="form-control @error('department') is-invalid @enderror">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="label">Bio</label>
                            <textarea name="bio" rows="3"
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
