@extends('backend.app')
@section('title', 'Edit Researcher')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Edit Researcher User</h3>
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

                <form action="{{ route('user_management.researcher.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $data->name ?? '') }}"
                                   placeholder="Enter full name"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $data->email ?? '') }}"
                                   placeholder="Enter email"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Phone <span class="text-muted">(Optional)</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $data->phone ?? '') }}"
                                   placeholder="Enter phone number"
                                   class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Gender <span class="text-muted">(Optional)</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender', $data->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $data->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="others" {{ old('gender', $data->gender) == 'others' ? 'selected' : '' }}>Others</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Avatar <span class="text-muted">(Optional)</span></label>
                            <input type="file" name="avatar" data-height="160"
                                   data-default-file="{{ !empty($data->avatar) ? asset($data->avatar) : '' }}"
                                   class="dropify form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="label">Password (leave blank to keep current)</label>
                            <input type="password" name="password" placeholder="Leave blank to keep current" class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary">Update</button>
                        <a href="{{ route('user_management.researcher.index') }}" class="btn btn-secondary">Cancel</a>
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

