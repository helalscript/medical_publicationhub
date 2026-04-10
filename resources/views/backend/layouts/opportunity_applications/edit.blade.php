@extends('backend.app')
@section('title', 'Edit Application')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Edit Application</h3>
            <a href="{{ route('admin.opportunity_applications.show', $data->id) }}" class="btn btn-outline-secondary">View</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.opportunity_applications.update', $data->id) }}" method="POST" class="card bg-white border-0 rounded-3 mb-4">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <p><strong>Opportunity:</strong> {{ $data->opportunity?->title ?? '-' }}</p>
                <p><strong>Applicant:</strong> {{ $data->first_name }} {{ $data->last_name }}</p>
                <div class="mb-3">
                    <label class="label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach (['pending','accept','reject'] as $st)
                            <option value="{{ $st }}" {{ old('status', $data->status) === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('admin.opportunity_applications.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
@endsection

