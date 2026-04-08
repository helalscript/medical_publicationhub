@extends('backend.app')
@section('title', 'Publication Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Publication Details</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.publications.index') }}" class="fw-medium text-decoration-none">Publications</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Details</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.publications.edit', $data->id) }}" class="btn btn-outline-secondary">Edit</a>
                <a href="{{ route('admin.publications.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                    <div>
                        <div class="fs-5 fw-semibold">{{ $data->title }}</div>
                        <div class="text-muted">{{ $data->overview ?? '-' }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $data->status === 'active' ? 'success' : 'secondary' }}">{{ $data->status }}</span>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="text-muted">Categories</div>
                    <div class="mt-1">
                        @forelse($categories as $c)
                            <span class="badge bg-secondary">{{ $c }}</span>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </div>
                </div>

                <hr class="my-4">

                <div>
                    <div class="fw-semibold mb-2">Description</div>
                    <div class="border rounded-3 p-3 bg-white">
                        {!! $data->description ?: '<span class="text-muted">-</span>' !!}
                    </div>
                </div>

                <div class="mt-4">
                    <div class="fw-semibold mb-2">Attachment</div>
                    @if(!empty($data->attachment))
                        <a href="{{ asset($data->attachment) }}" target="_blank" class="btn btn-outline-primary btn-sm">Download</a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

