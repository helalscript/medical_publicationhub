@extends('backend.app')
@section('title', 'Partnership Request Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Partnership Request</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.partnership_requests.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="fs-5 fw-semibold">{{ $data->full_name }}</div>
                        <div class="text-muted">{{ $data->organization ?? '-' }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $data->status === 'reviewed' ? 'success' : 'warning' }}">{{ $data->status }}</span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted">Email</div>
                        <div class="fw-semibold">{{ $data->email ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Phone</div>
                        <div class="fw-semibold">{{ $data->phone ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">IP</div>
                        <div class="fw-semibold">{{ $data->ip ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Created</div>
                        <div class="fw-semibold">{{ $data->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="fw-semibold mb-2">Message</div>
                    <div class="border rounded-3 p-3 bg-white">
                        {!! $data->message ? nl2br(e($data->message)) : '<span class="text-muted">-</span>' !!}
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <form action="{{ route('admin.partnership_requests.status', $data->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            Mark as {{ $data->status === 'pending' ? 'reviewed' : 'pending' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

