@extends('backend.app')
@section('title', 'Application')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Application</h3>
            <div>
                <a href="{{ route('admin.opportunity_applications.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.opportunity_applications.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <p><strong>Opportunity:</strong> {{ $data->opportunity?->title ?? '-' }}</p>
                <p><strong>Status:</strong> {{ $data->status }}</p>
                <hr>
                <p><strong>Name:</strong> {{ $data->first_name }} {{ $data->last_name }}</p>
                <p><strong>Email:</strong> {{ $data->email }}</p>
                <p><strong>Phone:</strong> {{ $data->phone ?? '-' }}</p>
                <p><strong>Highest education:</strong> {{ $data->highest_edu_level ?? '-' }}</p>
                <p><strong>Institution:</strong> {{ $data->institution ?? '-' }}</p>
                <p><strong>Field of study:</strong> {{ $data->field_of_study ?? '-' }}</p>
                <p><strong>Current position:</strong> {{ $data->current_position ?? '-' }}</p>
                <p><strong>LinkedIn:</strong> {{ $data->linkedin_profile ?? '-' }}</p>
                <p><strong>Website:</strong> {{ $data->personal_website ?? '-' }}</p>
                <p><strong>Accept term:</strong> {{ $data->accept_term ? 'Yes' : 'No' }}</p>
                @if ($data->cover_letter)
                    <hr>
                    <h6>Cover letter</h6>
                    <p class="mb-0">{{ $data->cover_letter }}</p>
                @endif
                @if ($data->documents)
                    <hr>
                    <h6>Documents</h6>
                    <ul class="mb-0">
                        @foreach ($data->documents as $doc)
                            <li>
                                @if (!empty($doc['path']))
                                    <a href="{{ asset($doc['path']) }}" target="_blank">{{ $doc['original_name'] ?? basename($doc['path']) }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection

