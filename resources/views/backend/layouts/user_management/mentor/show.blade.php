@extends('backend.app')
@section('title', 'Mentor Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Mentor user</h3>
            <a href="{{ route('user_management.mentor.edit', $data->id) }}" class="btn btn-primary">
                Edit
            </a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Account name:</strong> {{ $data->name }}</p>
                        <p><strong>Email:</strong> {{ $data->email }}</p>
                        <p><strong>Phone:</strong> {{ $data->phone ?? '-' }}</p>
                        <p><strong>Account status:</strong> {{ $data->status }}</p>
                    </div>
                    <div class="col-md-6">
                        @if ($data->mentor)
                            <p><strong>Mentor name:</strong> {{ $data->mentor->name }}</p>
                            <p><strong>University:</strong> {{ $data->mentor->university ?? '-' }}</p>
                            <p><strong>Department:</strong> {{ $data->mentor->department ?? '-' }}</p>
                            <p><strong>Mentor profile status:</strong> {{ $data->mentor->status }}</p>
                            <p><strong>Bio:</strong> {{ $data->mentor->bio ?? '-' }}</p>
                        @else
                            <p class="text-warning">No mentor profile record.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
