@extends('backend.app')
@section('title', 'Edit Rubric')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Rubric Edit</h3>

            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb align-items-center mb-0 lh-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                            <i class="ri-home-4-line fs-18 text-primary me-1"></i>
                            <span class="text-secondary fw-medium hover">Dashboard</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.rubrics.index') }}" class="d-flex align-items-center text-decoration-none">
                            <span class="text-secondary fw-medium hover">Rubrics</span>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span class="fw-medium">Edit</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="mb-4">
                    <h4 class="fs-20 mb-1">Edit Rubric</h4>
                    <p class="fs-15">Modify evaluation criteria and max points for tasks.</p>
                </div>

                <form action="{{ route('admin.rubrics.update', $rubric->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Rubric Title <span class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control text-dark h-55 @error('title') is-invalid @enderror"
                                    name="title" value="{{ old('title', $rubric->title) }}" placeholder="e.g. Research Quality Rubric" required>
                                @error('title')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-4">
                                <label class="label text-secondary">Description</label>
                                <textarea class="form-control text-dark @error('description') is-invalid @enderror"
                                    name="description" rows="3" placeholder="Explain the purpose of this rubric...">{{ old('description', $rubric->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-12 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 fs-16">Evaluation Criteria</h5>
                                <button type="button" id="add-criterion-btn" class="btn btn-sm btn-primary">
                                    <i class="ri-add-line"></i> Add Criterion
                                </button>
                            </div>

                            @error('criteria')
                                <div class="text-danger mb-2">{{ $message }}</div>
                            @enderror

                            <div class="table-responsive">
                                <table class="table align-middle" id="criteria-table">
                                    <thead>
                                        <tr>
                                            <th>Criterion Name <span class="text-danger">*</span></th>
                                            <th style="width: 200px;">Max Points <span class="text-danger">*</span></th>
                                            <th style="width: 100px; text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="criteria-tbody">
                                        @php
                                            $criteriaList = old('criteria', $rubric->criteria ?? []);
                                        @endphp
                                        @if(count($criteriaList) > 0)
                                            @foreach($criteriaList as $index => $criterion)
                                                <tr class="criterion-row">
                                                    <td>
                                                        <input type="text" name="criteria[{{ $index }}][name]" 
                                                            class="form-control text-dark h-45 @error("criteria.{$index}.name") is-invalid @enderror"
                                                            value="{{ $criterion['name'] }}" placeholder="e.g. Methodology" required>
                                                        @error("criteria.{$index}.name")
                                                            <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="criteria[{{ $index }}][points]" 
                                                            class="form-control text-dark h-45 @error("criteria.{$index}.points") is-invalid @enderror"
                                                            value="{{ $criterion['points'] }}" min="0" placeholder="e.g. 25" required>
                                                        @error("criteria.{$index}.points")
                                                            <div class="text-danger mt-1 fs-12">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-danger remove-criterion-btn">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="criterion-row">
                                                <td>
                                                    <input type="text" name="criteria[0][name]" 
                                                        class="form-control text-dark h-45"
                                                        placeholder="e.g. Clarity of writing" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="criteria[0][points]" 
                                                        class="form-control text-dark h-45"
                                                        min="0" placeholder="e.g. 25" required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger remove-criterion-btn" disabled>
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('admin.rubrics.index') }}" class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white text-decoration-none">Cancel</a>
                                <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16">
                                    <i class="ri-check-line text-white fw-medium"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let criterionIndex = {{ count($criteriaList) > 0 ? count($criteriaList) : 1 }};

            function checkRemoveButtonState() {
                const rows = $('#criteria-tbody tr');
                if (rows.length <= 1) {
                    rows.find('.remove-criterion-btn').prop('disabled', true);
                } else {
                    rows.find('.remove-criterion-btn').prop('disabled', false);
                }
            }

            $('#add-criterion-btn').click(function() {
                const newRow = `
                    <tr class="criterion-row">
                        <td>
                            <input type="text" name="criteria[${criterionIndex}][name]" 
                                class="form-control text-dark h-45"
                                placeholder="e.g. Criterion Name" required>
                        </td>
                        <td>
                            <input type="number" name="criteria[${criterionIndex}][points]" 
                                class="form-control text-dark h-45"
                                min="0" placeholder="e.g. 25" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-criterion-btn">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#criteria-tbody').append(newRow);
                criterionIndex++;
                checkRemoveButtonState();
            });

            $(document).on('click', '.remove-criterion-btn', function() {
                $(this).closest('tr').remove();
                checkRemoveButtonState();
            });

            checkRemoveButtonState();
        });
    </script>
@endpush
