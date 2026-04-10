@extends('backend.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Permissions</h4>
            @haspermission('access_control.permissions.create')
                <a href="{{ route('admin.access_control.permissions.create') }}" class="btn btn-primary">Create Permission</a>
            @endhaspermission
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th style="width: 220px;">Group</th>
                            <th>Name</th>
                            <th style="width: 160px;">Guard</th>
                            <th style="width: 220px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($permissions as $index => $permission)
                            <tr>
                                <td>{{ $permissions->firstItem() + $index }}</td>
                                <td>{{ $permission->group ?? 'General' }}</td>
                                <td class="fw-semibold">{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                                <td>
                                    @haspermission('access_control.permissions.update')
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="{{ route('admin.access_control.permissions.edit', $permission) }}">
                                            Edit
                                        </a>
                                    @endhaspermission

                                    @haspermission('access_control.permissions.delete')
                                        <form method="POST" action="{{ route('admin.access_control.permissions.destroy', $permission) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this permission?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endhaspermission
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No permissions found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

