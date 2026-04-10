@extends('backend.app')
@section('title', 'Research idea')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Research idea</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.research_ideas.index') }}" class="fw-medium text-decoration-none">Research Ideas</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Details</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.research_ideas.edit', $data->id) }}" class="btn btn-primary">Edit / Review</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <p><strong>Status:</strong> {{ $data->status }}</p>
                <p><strong>Mentor:</strong> {{ $data->mentor?->user?->name ?? '-' }} ({{ $data->mentor?->user?->email ?? '-' }})</p>
                <p><strong>Title:</strong> {{ $data->study_title }}</p>
                <p><strong>Deadline:</strong> {{ $data->dead_line?->format('Y-m-d') ?? '-' }}</p>
                <p><strong>Start:</strong> {{ $data->start_date?->format('Y-m-d') ?? '-' }}</p>
                <p><strong>End:</strong> {{ $data->end_date?->format('Y-m-d') ?? '-' }}</p>
                <hr>
                <h6>Descriptions</h6>
                <p class="mb-2">{{ $data->study_descritions }}</p>
                <h6>Objectives</h6>
                <p class="mb-2">{{ $data->research_objectives }}</p>
                <h6>Methodology</h6>
                <p class="mb-2">{{ $data->perposed_methodology }}</p>
                @if ($data->submission_proof)
                    <h6>Submission proof</h6>
                    <ul>
                        @foreach ($data->submission_proof as $item)
                            <li>
                                @if (!empty($item['path']))
                                    <a href="{{ asset($item['path']) }}" target="_blank">{{ $item['original_name'] ?? basename($item['path']) }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                <p class="text-muted small mt-3">Last updated by: {{ $data->updatedByUser?->email ?? '—' }}</p>
            </div>
        </div>
    </div>
@endsection
