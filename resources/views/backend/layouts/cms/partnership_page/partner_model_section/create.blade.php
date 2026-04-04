@extends('backend.app')
@section('title', 'Partner Model - Add Item')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <h3 class="mb-4">Partner Model — Add Item</h3>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('cms.partnership_page.partner_model_section.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="label text-secondary">Title</label>
                        <input type="text" name="title" class="form-control h-55" value="{{ old('title') }}">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Description</label>
                        <div id="editor" style="height:260px;"></div>
                        <input type="hidden" name="description" id="description">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Image</label>
                        <input type="file" name="image" class="dropify">
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('description').value=quill.root.innerHTML">Submit</button>
                    <a href="{{ route('cms.partnership_page.partner_model_section.index') }}" class="btn btn-danger ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>$('.dropify').dropify();</script>
    <script>
        var quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}],['bold','italic'],['link']] } });
        quill.root.innerHTML = {!! json_encode(old('description', '')) !!};
    </script>
@endpush
