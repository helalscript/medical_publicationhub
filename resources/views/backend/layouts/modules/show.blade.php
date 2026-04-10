@extends('backend.app')
@section('title', 'Module Details')

@php
    $mediaUrl = function (?string $path): ?string {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'uploads/')) {
            return asset($path);
        }
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    };

    $videoPath = $data->getRawOriginal('video');
    $documentPath = $data->getRawOriginal('document');
    $attachedFilePath = $data->getRawOriginal('attached_file');

    $videoUrl = $mediaUrl($videoPath);
    $documentUrl = $mediaUrl($documentPath);
    $attachedFileUrl = $mediaUrl($attachedFilePath);

    $ext = static fn (?string $path): string => $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';

    $videoExt = $ext($videoPath);
    $documentExt = $ext($documentPath);
    $attachedExt = $ext($attachedFilePath);

    $videoMime = match ($videoExt) {
        'webm' => 'video/webm',
        'mov' => 'video/quicktime',
        'avi' => 'video/x-msvideo',
        'mkv' => 'video/x-matroska',
        default => 'video/mp4',
    };

    $mimeForVideoExt = static fn (string $e): string => match ($e) {
        'webm' => 'video/webm',
        'mov' => 'video/quicktime',
        'avi' => 'video/x-msvideo',
        'mkv' => 'video/x-matroska',
        default => 'video/mp4',
    };

    $isImageExt = static fn (string $e): bool => in_array($e, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'], true);
    $isPdfExt = static fn (string $e): bool => $e === 'pdf';
    $isAudioExt = static fn (string $e): bool => in_array($e, ['mp3', 'wav', 'ogg', 'm4a', 'aac'], true);
@endphp

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Module Details</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.modules.index', ['course_id' => $data->course_id]) }}"
                               class="fw-medium text-decoration-none">Modules</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span class="fw-medium">Details</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @can('admin.lessons.index')
                    <a href="{{ route('admin.lessons.index', ['module_id' => $data->id]) }}" class="btn btn-outline-primary">Lessons</a>
                @endcan
                @can('admin.modules.edit')
                    <a href="{{ route('admin.modules.edit', $data->id) }}" class="btn btn-outline-secondary">Edit</a>
                @endcan
                <a href="{{ route('admin.modules.index', ['course_id' => $data->course_id]) }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body">
                <div class="row g-4">
                    @if($videoUrl)
                        <div class="col-lg-7">
                            <div class="border rounded-3 p-3 h-100 bg-light bg-opacity-50">
                                <div class="fw-semibold mb-3">Video</div>
                                <video controls class="w-100 rounded-3" style="max-height: 520px; background: #000;">
                                    <source src="{{ $videoUrl }}" type="{{ $videoMime }}">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="mt-2 small text-muted text-break">{{ $videoPath }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="{{ $videoUrl ? 'col-lg-5' : 'col-12' }}">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="text-muted small">Title</div>
                                    <div class="fw-semibold fs-5">{{ $data->title }}</div>
                                </div>
                                <div class="text-end d-flex flex-column gap-1 align-items-end">
                                    <span class="badge bg-{{ $data->status === 'active' ? 'success' : 'secondary' }}">{{ $data->status }}</span>
                                    <span class="badge bg-secondary text-capitalize">{{ $data->type }}</span>
                                    <span class="badge bg-{{ $data->is_locked ? 'warning' : 'light text-dark' }}">{{ $data->is_locked ? 'Locked' : 'Open' }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="small">
                                <div class="text-muted">Course</div>
                                <div class="fw-medium">{{ $data->course?->title ?? '—' }}</div>
                            </div>
                            @if($data->mentor)
                                <div class="small mt-2">
                                    <div class="text-muted">Mentor</div>
                                    <div class="fw-medium">{{ $data->mentor->user?->name ?? '—' }}</div>
                                </div>
                            @endif
                            <div class="small mt-2">
                                <div class="text-muted">Order / Duration</div>
                                <div>{{ $data->order_by ?? '0' }} @if($data->durations) · {{ $data->durations }} @endif</div>
                            </div>
                            @if($data->attached_text)
                                <div class="mt-3">
                                    <div class="text-muted small">Attached text</div>
                                    <div class="mt-1">{!! nl2br(e($data->attached_text)) !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($documentUrl)
            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body">
                    <div class="fw-semibold mb-3">Document</div>
                    @if($isPdfExt($documentExt))
                        <iframe src="{{ $documentUrl }}" class="w-100 rounded-3 border" style="min-height: 640px;" title="Document preview"></iframe>
                    @elseif($isImageExt($documentExt))
                        <div class="text-center bg-light rounded-3 p-3">
                            <img src="{{ $documentUrl }}" class="img-fluid rounded border" alt="Document">
                        </div>
                    @else
                        <div class="border rounded-3 p-4 text-center">
                            <p class="text-muted mb-3">Preview is not available for this file type ({{ $documentExt ?: 'unknown' }}).</p>
                            <a href="{{ $documentUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Open / download document</a>
                        </div>
                    @endif
                    <div class="mt-2 small text-muted text-break">{{ $documentPath }}</div>
                </div>
            </div>
        @endif

        @if($attachedFileUrl)
            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body">
                    <div class="fw-semibold mb-3">Attachment</div>
                    @if($isPdfExt($attachedExt))
                        <iframe src="{{ $attachedFileUrl }}" class="w-100 rounded-3 border" style="min-height: 640px;" title="Attachment preview"></iframe>
                    @elseif($isImageExt($attachedExt))
                        <div class="text-center bg-light rounded-3 p-3">
                            <img src="{{ $attachedFileUrl }}" class="img-fluid rounded border" alt="Attachment">
                        </div>
                    @elseif($isAudioExt($attachedExt))
                        <div class="border rounded-3 p-4">
                            <audio controls class="w-100" src="{{ $attachedFileUrl }}">Your browser does not support audio.</audio>
                        </div>
                    @elseif(in_array($attachedExt, ['mp4', 'webm', 'mov', 'avi', 'mkv'], true))
                        <video controls class="w-100 rounded-3" style="max-height: 520px; background: #000;">
                            <source src="{{ $attachedFileUrl }}" type="{{ $mimeForVideoExt($attachedExt) }}">
                        </video>
                    @else
                        <div class="border rounded-3 p-4 text-center">
                            <p class="text-muted mb-3">Preview is not available ({{ $attachedExt ?: 'unknown' }}).</p>
                            <a href="{{ $attachedFileUrl }}" target="_blank" rel="noopener" class="btn btn-primary">Open / download attachment</a>
                        </div>
                    @endif
                    <div class="mt-2 small text-muted text-break">{{ $attachedFilePath }}</div>
                </div>
            </div>
        @endif

        @if($data->lessons->isNotEmpty())
            <div class="card bg-white border-0 rounded-3 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="fw-semibold mb-0">Lessons ({{ $data->lessons->count() }})</div>
                        @can('admin.lessons.index')
                            <a href="{{ route('admin.lessons.index', ['module_id' => $data->id]) }}" class="btn btn-sm btn-outline-primary">Manage lessons</a>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>Title</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data->lessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->order_by }}</td>
                                    <td>{{ $lesson->title ?? '—' }}</td>
                                    <td>{{ $lesson->durations ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $lesson->status === 'active' ? 'success' : 'secondary' }}">{{ $lesson->status }}</span>
                                    </td>
                                    <td class="text-end">
                                        @can('admin.lessons.show')
                                            <a href="{{ route('admin.lessons.show', $lesson->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        @endcan
                                        @can('admin.lessons.edit')
                                            <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
