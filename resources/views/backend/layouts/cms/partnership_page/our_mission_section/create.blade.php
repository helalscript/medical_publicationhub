@extends('backend.app')
@section('title', 'Partnership - Our Mission')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <style>.dropify-wrapper { height: 200px; }</style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <h3 class="mb-4">Our Mission Section</h3>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('cms.partnership_page.our_mission_section.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="label text-secondary">Title</label>
                        <input type="text" name="title" class="form-control h-55" value="{{ old('title', $data->title ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Description</label>
                        <div id="editor" style="height:280px;"></div>
                        <input type="hidden" name="description" id="description">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Image</label>
                        <input class="dropify" type="file" name="image" accept="image/*" data-default-file="{{ !empty($data?->image) ? asset($data->image) : '' }}">
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('description').value=quill.root.innerHTML"><i class="ri-check-line"></i> Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>
        $('.dropify').dropify();
        var quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}],['bold','italic','underline'],['link']] } });
        quill.root.innerHTML = {!! json_encode(old('description', $data->description ?? '')) !!};
    </script>
@endpush
