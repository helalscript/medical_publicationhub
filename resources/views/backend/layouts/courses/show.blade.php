@extends('backend.app')
@section('title', 'Course Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Course Details</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.modules.index', ['course_id' => $data->id]) }}" class="btn btn-outline-primary">Modules</a>
                <a href="{{ route('admin.courses.edit', $data->id) }}" class="btn btn-outline-secondary">Edit</a>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="fw-semibold mb-2">Thumbnail</div>
                            @if(!empty($data->thumbnail))
                                <img src="{{ asset($data->thumbnail) }}" class="img-fluid rounded-3" alt="thumbnail">
                            @else
                                <div class="text-muted">No thumbnail</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fs-5 fw-semibold">{{ $data->title }}</div>
                                    <div class="text-muted">{{ $data->short_description ?? '-' }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-{{ $data->status === 'active' ? 'success' : 'secondary' }}">{{ $data->status }}</div>
                                    <div class="badge bg-{{ $data->is_future ? 'info' : 'secondary' }} mt-1">{{ $data->is_future ? 'Future' : 'Current' }}</div>
                                </div>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="text-muted">Price</div>
                                    <div class="fw-semibold">{{ $data->price ?? '0.00' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted">Old price</div>
                                    <div class="fw-semibold">{{ $data->old_price ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted">Total durations</div>
                                    <div class="fw-semibold">{{ $data->total_durations ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="text-muted">Overview</div>
                                <div class="mt-1">{!! $data->overview ? nl2br(e($data->overview)) : '<span class="text-muted">-</span>' !!}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="fw-semibold mb-2">Modules</div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Locked</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($data->modules as $m)
                                <tr>
                                    <td>{{ $m->id }}</td>
                                    <td>{{ $m->title }}</td>
                                    <td>{{ $m->type }}</td>
                                    <td>{{ $m->is_locked ? 'Yes' : 'No' }}</td>
                                    <td>{{ $m->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted">No modules yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

