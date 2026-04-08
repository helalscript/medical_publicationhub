@extends('backend.app')
@section('title', 'Create Course')

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/dropify/css/dropify.min.css') }}">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Create Course</h3>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('backend.layouts.courses._form')
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/admin/assets/dropify/js/dropify.min.js') }}"></script>
    <script>
        $(function () {
            $('.dropify').dropify();
        });
    </script>
@endpush

