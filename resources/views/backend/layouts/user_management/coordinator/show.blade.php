@extends('backend.app')
@section('title', 'Coordinator Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Coordinator user</h3>
            <a href="{{ route('user_management.coordinator.edit', $data->id) }}" class="btn btn-primary">Edit</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $data->name ?? '-' }}</p>
                        <p><strong>Email:</strong> {{ $data->email ?? '-' }}</p>
                        <p><strong>Phone:</strong> {{ $data->phone ?? '-' }}</p>
                        <p><strong>Account status:</strong> {{ $data->status ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        @if (!empty($data->avatar))
                            <img src="{{ asset($data->avatar) }}" alt="avatar" style="height: 100px;" class="rounded-3">
                        @else
                            <p class="text-muted">No avatar.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

