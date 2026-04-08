@extends('backend.app')
@section('title', 'Edit Publication')

@push('styles')
    <link href="{{ asset('backend/admin/assets/quill/quill.snow.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Edit Publication</h3>
            <a href="{{ route('admin.publications.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.publications.update', $data->id) }}" method="POST" enctype="multipart/form-data" id="pubForm">
                    @csrf
                    @method('PUT')
                    @include('backend.layouts.publications._form', ['data' => $data])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.publications.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/admin/assets/quill/quill.min.js') }}"></script>
    <script>
        $(function () {
            const quill = new Quill('#pubEditor', { theme: 'snow' });
            const input = document.getElementById('descriptionInput');
            if (input && input.value) quill.root.innerHTML = input.value;

            document.getElementById('pubForm').addEventListener('submit', function () {
                input.value = quill.root.innerHTML;
            });
        });
    </script>
@endpush

