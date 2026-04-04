@extends('backend.app')
@section('title', 'Research Opportunities - Edit Item')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <h3 class="mb-4">Research Opportunities — Edit Item</h3>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('cms.partnership_page.research_opportunitie_section.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="label text-secondary">Title</label>
                        <input type="text" name="title" class="form-control h-55" value="{{ old('title', $data->title) }}">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Image</label>
                        <input type="file" name="image" class="dropify" data-default-file="{{ !empty($data->image) ? asset($data->image) : '' }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('cms.partnership_page.research_opportunitie_section.index') }}" class="btn btn-danger ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>$('.dropify').dropify();</script>
@endpush
