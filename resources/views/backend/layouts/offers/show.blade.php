@extends('backend.app')
@section('title', 'Offer Details')

@section('content')
    @php
        $iconRaw = $data->getRawOriginal('icon');
        $iconIsImage = $data->isIconImage();
        $countdown = $data->countdown();
    @endphp

    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Offer Details</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.offers.index') }}" class="fw-medium text-decoration-none">Offers</a></li>
                        <li class="breadcrumb-item active"><span class="fw-medium">Details</span></li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                @can('admin.offers.edit')
                    <a href="{{ route('admin.offers.edit', $data->id) }}" class="btn btn-outline-secondary">Edit</a>
                @endcan
                <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card bg-white border-0 rounded-3 mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fs-5 fw-semibold">{{ $data->title }}</div>
                                <div class="text-muted mt-1">{{ $data->description }}</div>
                            </div>
                            <span class="badge bg-{{ $data->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($data->status) }}</span>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <div class="text-muted small">Promo code</div>
                                <div class="fw-semibold">{{ $data->promo_code }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Discount</div>
                                <div class="fw-semibold">{{ $data->discount_percentage ? $data->discount_percentage.'%' : '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Sort order</div>
                                <div class="fw-semibold">{{ $data->sort_order }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Ends at</div>
                                <div class="fw-semibold">{{ $data->end_date?->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small">Background</div>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="width:28px;height:28px;border-radius:6px;background:{{ $data->background_color }};border:1px solid #ddd;"></span>
                                    <span class="fw-semibold">{{ $data->background_color }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 rounded-3 mb-4" style="background: {{ $data->background_color ?? '#E3F2FD' }};">
                    <div class="card-body text-center">
                        @if($iconRaw)
                            @if($iconIsImage)
                                <img src="{{ asset($iconRaw) }}" alt="" style="width:48px;height:48px;object-fit:contain" class="mb-2">
                            @else
                                <div class="fs-1 mb-2">{{ $iconRaw }}</div>
                            @endif
                        @endif
                        <div class="fw-bold">{{ $data->title }}</div>
                        <div class="small text-muted mt-2">
                            @if($countdown['is_expired'])
                                Expired
                            @else
                                {{ $countdown['days'] }}d : {{ $countdown['hours'] }}h : {{ $countdown['minutes'] }}m : {{ $countdown['seconds'] }}s
                            @endif
                        </div>
                        <button type="button" class="btn btn-dark btn-sm mt-3" disabled>Copy Code</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
