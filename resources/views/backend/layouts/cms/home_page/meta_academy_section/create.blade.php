@extends('backend.app')
@section('title', 'Meta Academy Section')

@push('styles')
    {{--  Quill v2.0.2 only --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <style>
        .dropify-wrapper { height: 200px; }
        .dropify-wrapper .dropify-preview .dropify-render img { display: block; margin-left: auto; margin-right: auto; }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Meta Academy</h3>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="fs-20 mb-1">Meta Academy</h4>
                    <p class="fs-15">Section Title, Sub Title, Description (Editor), Image</p>
                </div>

                <form action="{{ route('cms.home_page.meta_academy_section.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Section Title</label>
                                <input type="text" class="form-control text-dark h-55 @error('title') is-invalid @enderror"
                                    name="title" value="{{ old('title', $data->title ?? '') }}" placeholder="Enter title">
                                @error('title')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Sub Title</label>
                                <input type="text" class="form-control text-dark h-55 @error('sub_title') is-invalid @enderror"
                                    name="sub_title" value="{{ old('sub_title', $data->sub_title ?? '') }}" placeholder="Enter sub title">
                                @error('sub_title')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Description</label>
                                <div id="editor" style="height: 350px;"></div>
                                <input type="hidden" name="description" id="description">
                                @error('description')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary mb-1">Image</label>
                                <input class="dropify form-control @error('image') is-invalid @enderror" type="file"
                                    name="image" accept="image/*"
                                    data-default-file="{{ !empty($data?->image) ? asset($data->image) : '' }}">
                                @error('image')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Button Text</label>
                                <input type="text" class="form-control text-dark h-55 @error('button_text') is-invalid @enderror"
                                    name="button_text" value="{{ old('button_text', $data->button_text ?? '') }}" placeholder="Enter button text">
                                @error('button_text')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16" onclick="syncEditor()">
                                <i class="ri-check-line text-white fw-medium"></i> Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>
        $(document).ready(function() { $('.dropify').dropify(); });

        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'font': [] }],
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
                    [{ 'size': ['small', false, 'large', 'huge'] }],
                ]
            }
        });

        @if (old('description'))
            quill.root.innerHTML = {!! json_encode(old('description')) !!};
        @else
            quill.root.innerHTML = {!! json_encode($data?->description) !!};
        @endif

        function syncEditor() {
            document.getElementById('description').value = quill.root.innerHTML;
        }
    </script>
@endpush

