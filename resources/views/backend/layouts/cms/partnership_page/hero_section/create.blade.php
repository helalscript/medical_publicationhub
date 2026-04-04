@extends('backend.app')
@section('title', 'Partnership Page - Hero Section')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Partnership Page — Hero</h3>
        </div>
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <p class="fs-15 mb-4">Title, Sub Title, Description, Button Text, Button Text 2, Sub Description</p>
                <form action="{{ route('cms.partnership_page.hero_section.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="label text-secondary">Title</label>
                            <input type="text" name="title" class="form-control h-55 @error('title') is-invalid @enderror"
                                value="{{ old('title', $data->title ?? '') }}">
                            @error('title')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="label text-secondary">Sub Title</label>
                            <input type="text" name="sub_title" class="form-control h-55 @error('sub_title') is-invalid @enderror"
                                value="{{ old('sub_title', $data->sub_title ?? '') }}">
                            @error('sub_title')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="label text-secondary">Description</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $data->description ?? '') }}</textarea>
                            @error('description')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="label text-secondary">Button Text</label>
                            <input type="text" name="button_text" class="form-control h-55" value="{{ old('button_text', $data->button_text ?? '') }}">
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="label text-secondary">Button Text 2</label>
                            <input type="text" name="button_text_2" class="form-control h-55" value="{{ old('button_text_2', $data->button_text_2 ?? '') }}">
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="label text-secondary">Sub Description</label>
                            <textarea name="sub_description" rows="3" class="form-control">{{ old('sub_description', $data->sub_description ?? '') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16"><i class="ri-check-line text-white fw-medium"></i> Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection
