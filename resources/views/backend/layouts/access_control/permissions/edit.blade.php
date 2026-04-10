@extends('backend.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Edit Permission</h4>
            <a href="{{ route('admin.access_control.permissions.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.access_control.permissions.update', $permission) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Group <span class="text-muted">(Optional)</span></label>
                            <input list="permission_groups" type="text" name="group" value="{{ old('group', $permission->group) }}"
                                   class="form-control @error('group') is-invalid @enderror"
                                   placeholder="ex: Courses">
                            <datalist id="permission_groups">
                                @foreach($groups as $g)
                                    <option value="{{ $g }}"></option>
                                @endforeach
                            </datalist>
                            @error('group')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Permission Name <span class="text-danger">(Required)</span></label>
                            <input type="text" name="name" value="{{ old('name', $permission->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="ex: courses.manage">
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

