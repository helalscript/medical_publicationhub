@extends('backend.app')
@section('title', 'Edit Service')

@push('styles')
    <link href="{{ asset('backend/admin/assets/quill/quill.snow.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Edit Service</h3>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <form action="{{ route('admin.services.update', $data->id) }}" method="POST" id="serviceForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('backend.layouts.services._form', ['data' => $data])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/admin/assets/quill/quill.min.js') }}"></script>
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>
        $(function () {
            const quill = new Quill('#serviceEditor', { theme: 'snow' });
            const input = document.getElementById('descriptionInput');
            if (input && input.value) quill.root.innerHTML = input.value;
            $('.dropify').dropify();

            document.getElementById('serviceForm').addEventListener('submit', function () {
                input.value = quill.root.innerHTML;
            });
        });
    </script>
@endpush

