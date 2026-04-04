@extends('backend.app')
@section('title', 'Partnership - Manuscript')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <h3 class="mb-4">Manuscript Section</h3>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('cms.partnership_page.manuscript_section.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="label text-secondary">Title</label>
                        <input type="text" name="title" class="form-control h-55" value="{{ old('title', $data->title ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Sub Title</label>
                        <input type="text" name="sub_title" class="form-control h-55" value="{{ old('sub_title', $data->sub_title ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Description</label>
                        <div id="editor" style="height:300px;"></div>
                        <input type="hidden" name="description" id="description">
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('description').value=quill.root.innerHTML"><i class="ri-check-line"></i> Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}],['bold','italic'],['link','image']] } });
        quill.root.innerHTML = {!! json_encode(old('description', $data->description ?? '')) !!};
    </script>
@endpush
