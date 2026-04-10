@extends('backend.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Roles</h4>
            @haspermission('access_control.roles.create')
                <a href="{{ route('admin.access_control.roles.create') }}" class="btn btn-primary">Create Role</a>
            @endhaspermission
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Name</th>
                            <th style="width: 160px;">Guard</th>
                            <th style="width: 260px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($roles as $index => $role)
                            <tr>
                                <td>{{ $roles->firstItem() + $index }}</td>
                                <td class="fw-semibold text-capitalize">{{ $role->name }}</td>
                                <td>{{ $role->guard_name }}</td>
                                <td>
                                    @haspermission('access_control.roles.assign_permissions')
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="{{ route('admin.access_control.roles.permissions.edit', $role) }}">
                                            Permissions
                                        </a>
                                    @endhaspermission

                                    @haspermission('access_control.roles.update')
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="{{ route('admin.access_control.roles.edit', $role) }}">
                                            Edit
                                        </a>
                                    @endhaspermission

                                    @haspermission('access_control.roles.delete')
                                        <form method="POST" action="{{ route('admin.access_control.roles.destroy', $role) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this role?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endhaspermission
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No roles found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

