@extends('backend.app')
@section('title', 'Service Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Service Details</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.services.index') }}" class="fw-medium text-decoration-none">Services</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Details</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.services.edit', $data->id) }}" class="btn btn-outline-secondary">Edit</a>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Back</a>
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
                        <span class="badge bg-{{ $data->is_features ? 'info' : 'secondary' }}">{{ $data->is_features ? 'Featured' : 'Not featured' }}</span>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="text-muted">Starting price</div>
                        <div class="fw-semibold">{{ $data->starting_price ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">WhatsApp</div>
                        <div class="fw-semibold">{{ $data->whats_app_num ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted">Icon</div>
                        @if(!empty($data->icon))
                            <img src="{{ asset($data->icon) }}" alt="icon" style="width:44px;height:44px;object-fit:contain" class="border rounded-3 p-1 bg-white">
                        @else
                            <div class="fw-semibold">-</div>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <div>
                    <div class="fw-semibold mb-2">Description</div>
                    <div class="border rounded-3 p-3 bg-white">
                        {!! $data->description ?: '<span class="text-muted">-</span>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

