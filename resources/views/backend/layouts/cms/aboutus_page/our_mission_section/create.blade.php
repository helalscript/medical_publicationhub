@extends('backend.app')
@section('title', 'Our Mission Section - Aboutus Page')
@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Our Mission Section</h3>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="fs-20 mb-1">Our Mission Section</h4>
                    <p class="fs-15">Title, Subtitle, Description</p>
                </div>

                <form action="{{ route('cms.aboutus_page.our_mission_section.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Title</label>
                                <input type="text"
                                    class="form-control text-dark h-55 @error('title') is-invalid @enderror"
                                    name="title"
                                    value="{{ old('title', $data->title ?? '') }}"
                                    placeholder="Enter Title">
                                @error('title')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Subtitle</label>
                                <input type="text"
                                    class="form-control text-dark h-55 @error('subtitle') is-invalid @enderror"
                                    name="subtitle"
                                    value="{{ old('subtitle', $data->subtitle ?? '') }}"
                                    placeholder="Enter Subtitle">
                                @error('subtitle')<div class="text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Description</label>
                                <textarea class="form-control text-dark @error('description') is-invalid @enderror"
                                    name="description"
                                    rows="4"
                                    placeholder="Enter Description">{{ old('description', $data->description ?? '') }}</textarea>
                                @error('description')<div class="text-danger">{{ $message }}</div>@enderror
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
