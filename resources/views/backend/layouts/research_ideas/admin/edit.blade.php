@extends('backend.app')
@section('title', 'Review research idea')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Review research idea</h3>
            <a href="{{ route('admin.research_ideas.show', $data->id) }}" class="btn btn-outline-secondary">View</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <p><strong>Title:</strong> {{ $data->study_title }}</p>
                <p><strong>Mentor:</strong> {{ $data->mentor?->name ?? '-' }}</p>

                <form action="{{ route('admin.research_ideas.update', $data->id) }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach (['pending', 'on_review', 'accept', 'reject'] as $st)
                                    <option value="{{ $st }}" {{ old('status', $data->status) === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Set <strong>on_review</strong> while reviewing; then <strong>accept</strong> or <strong>reject</strong>.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">Deadline</label>
                            <input type="date" name="dead_line" class="form-control"
                                   value="{{ old('dead_line', $data->dead_line?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">Start date</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date', $data->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="label">End date</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ old('end_date', $data->end_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.research_ideas.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
@endsection
