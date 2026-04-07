@extends('backend.app')
@section('title', 'Create Opportunity')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <style>
        .dropify-wrapper { height: 200px; }
        .select2-container .select2-selection--multiple { min-height: 38px; }
        .desc-item { border: 1px solid rgba(0,0,0,.08); border-radius: 12px; padding: 12px; margin-bottom: 12px; background: #fff; }
        .desc-item .ql-container { min-height: 140px; }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Create Opportunity</h3>
            <a href="{{ route('admin.opportunities.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form id="opportunityForm" action="{{ route('admin.opportunities.store') }}" method="POST" enctype="multipart/form-data" class="card bg-white border-0 rounded-3 mb-4">
            @csrf
            <div class="card-body p-4">
                @include('backend.layouts.opportunities._form')
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function toYmd(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }
        function addDays(ymd, days) {
            const d = new Date(ymd + 'T00:00:00');
            d.setDate(d.getDate() + days);
            return toYmd(d);
        }
        function addMonths(ymd, months) {
            const d = new Date(ymd + 'T00:00:00');
            d.setMonth(d.getMonth() + months);
            return toYmd(d);
        }

        $(document).ready(function() {
            $('.dropify').dropify();
            $('.js-select2').select2({ width: '100%', allowClear: true });

            // -------- Descriptions builder (title + rich text) ----------
            const builder = document.getElementById('desciptions_builder');
            const hidden = document.getElementById('desciptions_json');
            const addBtn = document.getElementById('add_desciptions_item');
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

            function createItem(initial) {
                const item = document.createElement('div');
                item.className = 'desc-item';
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                        <div class="flex-grow-1">
                            <label class="label mb-1">Description title</label>
                            <input type="text" class="form-control desc-title" placeholder="Title" value="${(initial?.title ?? initial?.heading ?? '').replaceAll('"','&quot;')}">
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm mt-4 desc-remove">Remove</button>
                    </div>
                    <label class="label mb-1">Description</label>
                    <div class="desc-editor"></div>
                `;
                builder.appendChild(item);

                const editorEl = item.querySelector('.desc-editor');
                const q = new Quill(editorEl, { theme: 'snow' });
                q.root.innerHTML = (initial?.description ?? initial?.description_description ?? '') || '';
                quills.push({ item, q });

                item.querySelector('.desc-remove').addEventListener('click', () => {
                    const idx = quills.findIndex(x => x.item === item);
                    if (idx >= 0) quills.splice(idx, 1);
                    item.remove();
                    syncDescriptions();
                });
            }

            function syncDescriptions() {
                const out = quills.map(({ item, q }) => ({
                    title: (item.querySelector('.desc-title')?.value ?? '').trim(),
                    description: q.root.innerHTML ?? '',
                })).filter(x => x.title !== '' || (x.description ?? '').replace(/<[^>]*>/g,'').trim() !== '');

                hidden.value = JSON.stringify(out);
            }

            addBtn.addEventListener('click', () => createItem({}));

            const existing = parseExisting();
            if (existing.length) {
                existing.forEach(x => createItem(x));
            } else {
                createItem({});
            }

            document.getElementById('opportunityForm').addEventListener('submit', function() {
                syncDescriptions();
            });

            const $deadline = $('#dead_line');
            const $start = $('#start_date');
            const $end = $('#end_date');

            const today = new Date();
            const deadlineDefault = toYmd(new Date(today.getFullYear(), today.getMonth(), today.getDate() + 30));

            if (!$deadline.val()) $deadline.val(deadlineDefault);
            if (!$start.val()) $start.val(addDays($deadline.val(), 1));
            if (!$end.val()) $end.val(addMonths($start.val(), 3));

            let startTouched = false;
            let endTouched = false;
            $start.on('change', () => startTouched = true);
            $end.on('change', () => endTouched = true);

            $deadline.on('change', function() {
                if (!startTouched) $start.val(addDays($deadline.val(), 1));
                if (!endTouched) $end.val(addMonths($start.val(), 3));
            });
            $start.on('change', function() {
                if (!endTouched) $end.val(addMonths($start.val(), 3));
            });
        });
    </script>
@endpush

