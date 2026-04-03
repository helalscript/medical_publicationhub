@extends('backend.app')
@section('title', 'Research Journey Section')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Research Journey</h3>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="fs-20 mb-1">Research Journey</h4>
                    <p class="fs-15">Section Title, Sub Title, Description, Button Text, Button Text 2, Sub Description</p>
                </div>

                <form action="{{ route('cms.home_page.research_journey_section.update') }}" method="POST">
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
                                <label class="label text-secondary">Section Sub Title</label>
                                <input type="text" class="form-control text-dark h-55 @error('sub_title') is-invalid @enderror"
                                    name="sub_title" value="{{ old('sub_title', $data->sub_title ?? '') }}" placeholder="Enter sub title">
                                @error('sub_title')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Section Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Enter description" rows="5">{{ old('description', $data->description ?? '') }}</textarea>
                                @error('description')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Button Text</label>
                                <input type="text" class="form-control text-dark h-55 @error('button_text') is-invalid @enderror"
                                    name="button_text" value="{{ old('button_text', $data->button_text ?? '') }}" placeholder="Enter button text">
                                @error('button_text')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Button Text 2</label>
                                <input type="text" class="form-control text-dark h-55 @error('button_text_2') is-invalid @enderror"
                                    name="button_text_2" value="{{ old('button_text_2', $data->button_text_2 ?? '') }}" placeholder="Enter button text 2">
                                @error('button_text_2')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Sub Description</label>
                                <textarea name="sub_description" class="form-control @error('sub_description') is-invalid @enderror"
                                    placeholder="Enter sub description" rows="4">{{ old('sub_description', $data->sub_description ?? '') }}</textarea>
                                @error('sub_description')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16">
                                <i class="ri-check-line text-white fw-medium"></i> Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

