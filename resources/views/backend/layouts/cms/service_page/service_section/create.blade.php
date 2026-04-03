@extends('backend.app')
@section('title', 'Service Section')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Service Page</h3>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="fs-20 mb-1">Service Section</h4>
                    <p class="fs-15">Title, Sub Title, Description</p>
                </div>

                <form action="{{ route('cms.service_page.service_section.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Title</label>
                                <input type="text"
                                    class="form-control text-dark h-55 @error('title') is-invalid @enderror"
                                    name="title" value="{{ old('title', $data->title ?? '') }}" placeholder="Enter title">
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Sub Title</label>
                                <input type="text"
                                    class="form-control text-dark h-55 @error('sub_title') is-invalid @enderror"
                                    name="sub_title" value="{{ old('sub_title', $data->sub_title ?? '') }}" placeholder="Enter sub title">
                                @error('sub_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Enter description" rows="6">{{ old('description', $data->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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

