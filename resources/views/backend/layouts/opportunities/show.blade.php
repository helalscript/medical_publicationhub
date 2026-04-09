@extends('backend.app')
@section('title', 'Opportunity')

@push('styles')
    <style>
        .opp-hero {
            background: #fff;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, .06);
            color: #0f172a;
        }
        .opp-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            background: rgba(15, 23, 42, .04);
            color: rgba(15, 23, 42, .9);
            border: 1px solid rgba(15, 23, 42, .08);
        }
        .opp-stat {
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 12px;
            padding: 10px 12px;
            min-height: 56px;
        }
        .opp-stat .k { font-size: 12px; color: rgba(15, 23, 42, .65); }
        .opp-stat .v { font-weight: 600; }
        .desc-block h5 { margin-bottom: 8px; }
        .desc-block .ql-editor { padding: 0; }
        .mentor-card {
            background: #fff;
            color: #0f172a;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, .06);
        }
        .mentor-sub { color: rgba(15, 23, 42, .65); font-size: 12px; }
        .mentor-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
        .opp-thumb {
            width: 100%;
            max-height: 260px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, .10);
        }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Opportunity</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.opportunities.index') }}" class="fw-medium text-decoration-none">Opportunities</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Details</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('admin.opportunities.edit', $data->id) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.opportunities.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="opp-hero p-4">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach (($specialityTags ?? collect())->take(6) as $t)
                            <span class="opp-chip">{{ $t }}</span>
                        @endforeach
                        @foreach (($courseTags ?? collect())->take(6) as $t)
                            <span class="opp-chip">{{ $t }}</span>
                        @endforeach
                        <span class="opp-chip">Status: {{ $data->status ?? '-' }}</span>
                        <span class="opp-chip">Future: {{ !empty($data->is_future) ? 'Yes' : 'No' }}</span>
                    </div>

                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                        <div>
                            <h2 class="mb-2" style="line-height: 1.1">{{ $data->title ?? '—' }}</h2>
                            @if (!empty($data->overview))
                                <p class="mb-0 text-muted">{{ $data->overview }}</p>
                            @endif
                        </div>
                        @if (!empty($data->thumbnail))
                            <a href="{{ asset($data->thumbnail) }}" target="_blank" class="btn btn-outline-primary btn-sm">Open image</a>
                        @endif
                    </div>

                    @if (!empty($data->thumbnail))
                        <div class="mt-3">
                            <img class="opp-thumb" src="{{ asset($data->thumbnail) }}" alt="thumbnail">
                        </div>
                    @endif

                    <div class="row g-2 mt-3">
                        <div class="col-md-4">
                            <div class="opp-stat">
                                <div class="k">Deadline</div>
                                <div class="v">{{ $data->dead_line?->format('M d, Y') ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="opp-stat">
                                <div class="k">Duration</div>
                                <div class="v">{{ $data->durations ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="opp-stat">
                                <div class="k">Start date</div>
                                <div class="v">{{ $data->start_date?->format('M d, Y') ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border-0 rounded-3 mt-3">
                    <div class="card-body p-4">
                        <h5 class="mb-3">About this opportunity</h5>
                        @if (!empty($data->overview))
                            <p class="text-muted mb-3">{{ $data->overview }}</p>
                        @endif

                        @php
                            $blocks = is_array($data->desciptions ?? null) ? $data->desciptions : [];
                        @endphp

                        @forelse ($blocks as $b)
                            @php
                                $bt = $b['title'] ?? $b['heading'] ?? null;
                                $bd = $b['description'] ?? $b['description_description'] ?? null;
                            @endphp
                            @if (!empty($bt) || !empty($bd))
                                <div class="desc-block mb-4">
                                    @if (!empty($bt))
                                        <h5 class="mb-2">{{ $bt }}</h5>
                                    @endif
                                    @if (!empty($bd))
                                        <div class="text-body">{!! $bd !!}</div>
                                    @else
                                        <p class="text-muted mb-0">—</p>
                                    @endif
                                </div>
                            @endif
                        @empty
                            <p class="text-muted mb-0">No description blocks added.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card bg-white border-0 rounded-3 mt-3">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Applications</h5>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($data->applications ?? [] as $app)
                                    <tr>
                                        <td>{{ $app->id }}</td>
                                        <td>{{ trim(($app->first_name ?? '').' '.($app->last_name ?? '')) ?: '—' }}</td>
                                        <td>{{ $app->email ?? '—' }}</td>
                                        <td>{{ $app->status ?? '—' }}</td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.opportunity_applications.show', $app->id) }}">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No applications yet.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="mentor-card p-3">
                    <div class="d-flex align-items-center gap-3">
                        @php
                            $mentor = $data->mentor ?? null;
                            $mentorAvatar = $mentor?->user?->avatar ? asset($mentor->user->avatar) : asset('backend/admin/assets/images/avatar_defult.png');
                        @endphp
                        <img class="mentor-avatar" src="{{ $mentorAvatar }}" alt="mentor">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $mentor?->user?->name ?? 'Mentor not assigned' }}</div>
                            <div class="mentor-sub">
                                {{ $mentor?->department ?? '' }}
                                @if (!empty($mentor?->department) && !empty($mentor?->university)) · @endif
                                {{ $mentor?->university ?? '' }}
                            </div>
                        </div>
                    </div>
                    @if (!empty($mentor?->user_id))
                        <div class="mt-3">
                            <a href="{{ route('user_management.mentor.show', $mentor->user_id) }}" class="btn btn-outline-primary w-100">
                                View mentor profile
                            </a>
                        </div>
                    @endif
                    @if (!empty($mentor?->bio))
                        <div class="mt-3 mentor-sub">{!! nl2br(e($mentor->bio)) !!}</div>
                    @endif
                </div>

                <div class="card bg-white border-0 rounded-3 mt-3">
                    <div class="card-body p-4">
                        <h6 class="mb-2">Quick details</h6>
                        <p class="mb-1"><strong>Price:</strong> {{ $data->price !== null ? $data->price : '—' }}</p>
                        <p class="mb-1"><strong>End date:</strong> {{ $data->end_date?->format('M d, Y') ?? '—' }}</p>
                        <p class="mb-0"><strong>Created:</strong> {{ $data->created_at?->format('M d, Y') ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

