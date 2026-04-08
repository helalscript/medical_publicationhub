@extends('backend.app')
@section('title', 'Edit Course')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <style>
        .dropify-wrapper { min-height: 200px; }
        .select2-container .select2-selection--multiple { min-height: 38px; }
        .desc-item { border: 1px solid rgba(0,0,0,.08); border-radius: 12px; padding: 12px; margin-bottom: 12px; background: #fff; }
        .desc-item .ql-container { min-height: 140px; }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Edit Course</h3>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <form action="{{ route('admin.courses.update', $data->id) }}" method="POST" enctype="multipart/form-data" id="courseForm">
                    @csrf
                    @method('PUT')
                    @include('backend.layouts.courses._form', ['data' => $data, 'metaAcademyCategories' => $metaAcademyCategories, 'mentors' => $mentors])
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            $('.dropify').dropify();
            $('.js-select2').select2({ width: '100%', allowClear: true });

            const builder = document.getElementById('course_descriptions_builder');
            const hidden = document.getElementById('course_descriptions_json');
            const addBtn = document.getElementById('add_course_description_item');
            const quills = [];

            function parseExisting() {
                try {
                    const raw = (hidden.value || '').trim();
                    if (!raw) return [];
                    const parsed = JSON.parse(raw);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            }

            function escAttr(s) {
                return String(s ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function createItem(initial) {
                const titleVal = escAttr(initial?.title ?? initial?.heading ?? '');
                const item = document.createElement('div');
                item.className = 'desc-item';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                        <div class="flex-grow-1">
                            <label class="form-label mb-1">Block title</label>
                            <input type="text" class="form-control desc-title" placeholder="Section title" value="${titleVal}">
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm mt-4 desc-remove">Remove</button>
                    </div>
                    <label class="form-label mb-1">Description</label>
                    <div class="desc-editor"></div>
                `;
                builder.appendChild(item);

                const editorEl = item.querySelector('.desc-editor');
                const q = new Quill(editorEl, { theme: 'snow' });
                q.root.innerHTML = (initial?.description ?? '') || '';
                quills.push({ item, q });

                item.querySelector('.desc-remove').addEventListener('click', () => {
                    const idx = quills.findIndex(x => x.item === item);
                    if (idx >= 0) quills.splice(idx, 1);
                    item.remove();
                    syncDescriptions();
                });
                q.on('text-change', () => syncDescriptions());
                item.querySelector('.desc-title').addEventListener('input', syncDescriptions);
            }

            function syncDescriptions() {
                const out = quills.map(({ item, q }) => ({
                    title: (item.querySelector('.desc-title')?.value ?? '').trim(),
                    description: q.root.innerHTML ?? '',
                })).filter(x => x.title !== '' || (x.description ?? '').replace(/<[^>]*>/g, '').trim() !== '');
                hidden.value = JSON.stringify(out);
            }

            addBtn.addEventListener('click', () => createItem({}));

            const existing = parseExisting();
            if (existing.length) {
                existing.forEach(x => createItem(x));
            } else {
                createItem({});
            }

            document.getElementById('courseForm').addEventListener('submit', function () {
                syncDescriptions();
            });
        });
    </script>
@endpush
