@extends('backend.app')
@section('title', 'View Rubric Details')

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Rubric Details</h3>

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
                        <span class="fw-medium">Details</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="fs-22 mb-1 text-dark">{{ $rubric->title }}</h4>
                        <span class="badge bg-primary fs-12">Global / System Rubric</span>
                    </div>
                    <div>
                        @can('admin.rubrics.edit')
                        <a href="{{ route('admin.rubrics.edit', $rubric->id) }}" class="btn btn-primary btn-sm px-3">
                            <i class="ri-edit-line"></i> Edit Rubric
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fs-16 text-secondary mb-2">Description</h5>
                    <p class="fs-15 text-dark bg-light p-3 rounded">
                        {{ $rubric->description ?? 'No description provided for this rubric.' }}
                    </p>
                </div>

                <div class="mb-4">
                    <h5 class="fs-16 text-secondary mb-3">Evaluation Criteria & Points</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 80px;" class="text-center">#</th>
                                    <th scope="col">Criterion Name</th>
                                    <th scope="col" style="width: 200px;" class="text-center">Max Points</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPoints = 0;
                                @endphp
                                @if(is_array($rubric->criteria))
                                    @foreach($rubric->criteria as $index => $criterion)
                                        @php
                                            $totalPoints += (int) ($criterion['points'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-medium">{{ $index + 1 }}</td>
                                            <td>{{ $criterion['name'] ?? '' }}</td>
                                            <td class="text-center fw-bold text-primary">{{ $criterion['points'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No criteria defined.</td>
                                    </tr>
                                @endif
                            </tbody>
                            @if($totalPoints > 0)
                                <tfoot>
                                    <tr class="table-warning">
                                        <td colspan="2" class="text-end fw-bold">Total Max Score:</td>
                                        <td class="text-center fw-bold text-dark fs-15">{{ $totalPoints }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.rubrics.index') }}" class="btn btn-secondary py-2 px-4 fw-medium fs-16">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
