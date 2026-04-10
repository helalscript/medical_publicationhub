@extends('backend.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">Role Permissions</h4>
                <div class="text-muted">Role: <span class="fw-semibold text-capitalize">{{ $role->name }}</span></div>
            </div>
            <a href="{{ route('admin.access_control.roles.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.access_control.roles.permissions.update', $role) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        @foreach($permissions as $groupName => $groupPermissions)
                            <div class="col-lg-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="fw-semibold">{{ $groupName }}</div>
                                        <small class="text-muted">{{ $groupPermissions->count() }} permissions</small>
                                    </div>

                                    <div class="row">
                                        @foreach($groupPermissions as $p)
                                            <div class="col-md-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="permission_ids[]"
                                                           value="{{ $p->id }}"
                                                           id="perm_{{ $p->id }}"
                                                           {{ in_array((int)$p->id, $assigned, true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $p->id }}">
                                                        {{ $p->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary">Save Permissions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

