@extends('backend.app')
@section('title', 'Platform Impact - Add Stat Item')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Add Platform Impact Stat</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active"><span class="fw-medium">Home Page</span></li>
                    <li class="breadcrumb-item active"><span class="fw-medium">Platform Impact</span></li>
                    <li class="breadcrumb-item active"><span class="fw-medium">Add</span></li>
                </ol>
            </nav>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="fs-20 mb-1">New Stat Item</h4>
                    <p class="fs-15">Add a single statistic (e.g., 500+ Research Opportunities) to the Platform Impact section.</p>
                </div>

                <form action="{{ route('cms.home_page.platform_impact_section.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Number / Value <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control text-dark h-55 @error('title') is-invalid @enderror"
                                    name="title" value="{{ old('title') }}"
                                    placeholder="e.g. 500+, 150+, 98%">
                                @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Label <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control text-dark h-55 @error('sub_title') is-invalid @enderror"
                                    name="sub_title" value="{{ old('sub_title') }}"
                                    placeholder="e.g. Research Opportunities">
                                @error('sub_title') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Category <span class="text-danger">*</span></label>
                                <select class="form-select h-55 @error('button_text') is-invalid @enderror" name="button_text">
                                    <option value="">-- Select Category --</option>
                                    <option value="opportunities" {{ old('button_text') == 'opportunities' ? 'selected' : '' }}>Opportunities</option>
                                    <option value="services" {{ old('button_text') == 'services' ? 'selected' : '' }}>Services</option>
                                    <option value="academy" {{ old('button_text') == 'academy' ? 'selected' : '' }}>Academy</option>
                                </select>
                                @error('button_text') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <button type="reset" class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white"
                            onclick="window.location.href='{{ route('cms.home_page.platform_impact_section.index') }}'">Cancel</button>
                        <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16">
                            <i class="ri-check-line text-white fw-medium"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
