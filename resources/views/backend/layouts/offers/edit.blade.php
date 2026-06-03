@extends('backend.app')
@section('title', 'Edit Offer')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Edit Offer</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.offers.index') }}" class="fw-medium text-decoration-none">Offers</a></li>
                        <li class="breadcrumb-item active"><span class="fw-medium">Edit</span></li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.offers.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('backend.layouts.offers._form', ['data' => $data])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.offers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>$(function () { $('.dropify').dropify(); });</script>
@endpush
