@extends('backend.app')
@section('title', 'Research Section')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Research Page</h3>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="fs-20 mb-1">Research Section</h4>
                    <p class="fs-15">Section Title, Section Description</p>
                </div>

                <form action="{{ route('cms.research_page.research_section.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Section Title</label>
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
                                <label class="label text-secondary">Section Description</label>
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

