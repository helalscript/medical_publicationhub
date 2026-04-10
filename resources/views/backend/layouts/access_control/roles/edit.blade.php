@extends('backend.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Edit Role</h4>
            <a href="{{ route('admin.access_control.roles.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.access_control.roles.update', $role) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Role Name <span class="text-danger">(Required)</span></label>
                            <input type="text" name="name" value="{{ old('name', $role->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="ex: coordinator">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

