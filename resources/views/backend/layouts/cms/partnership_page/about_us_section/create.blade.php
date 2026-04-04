@extends('backend.app')
@section('title', 'Partnership - About Us')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <style>.dropify-wrapper { height: 200px; }</style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <h3 class="mb-4">About Us Section</h3>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('cms.partnership_page.about_us_section.update') }}" method="POST" enctype="multipart/form-data">
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
                        <textarea name="description" rows="5" class="form-control">{{ old('description', $data->description ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Image</label>
                        <input class="dropify" type="file" name="image" accept="image/*" data-default-file="{{ !empty($data?->image) ? asset($data->image) : '' }}">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="ri-check-line"></i> Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>$('.dropify').dropify();</script>
@endpush
