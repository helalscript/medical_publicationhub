@extends('backend.app')
@section('title', 'Partnership - Core Research')

@push('styles')
    <style>[type=checkbox]:checked,[type=radio]:checked{background-size:40%!important}</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Core Research</h3>
            <nav style="--bs-breadcrumb-divider:'>';">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><span class="fw-medium">Partnership Page</span></li>
                    <li class="breadcrumb-item active">Core Research</li>
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-5 col-xxl-5 col-lg-6">
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <h4 class="fs-20 mb-3">Section (title, subtitle, image & description)</h4>
                <form action="{{ route('cms.partnership_page.core_research_section.update_container') }}" method="POST" enctype="multipart/form-data" id="coreResearchContainerForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="label text-secondary">Title</label>
                            <input type="text" name="title" class="form-control h-55" value="{{ old('title', $containerData->title ?? '') }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="label text-secondary">Sub title</label>
                            <input type="text" name="sub_title" class="form-control h-55" value="{{ old('sub_title', $containerData->sub_title ?? '') }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="label text-secondary">Image</label>
                            <input type="file" name="image" class="dropify" data-default-file="{{ !empty($containerData?->image) ? asset($containerData->image) : '' }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="label text-secondary">Description</label>
                            <div id="editor_container" style="min-height:220px;"></div>
                            <input type="hidden" name="description" id="description_container">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save section</button>
                </form>
            </div>
        </div>
            </div>

            <div class="col-xl-7 col-xxl-7 col-lg-6">
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between flex-wrap gap-2 p-4">
                    <input type="text" class="form-control" placeholder="Search" id="customSearchBox" style="max-width:280px">
                    <a href="{{ route('cms.partnership_page.core_research_section.create') }}" class="btn btn-outline-primary">Add item</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" id="basic_tables">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item"><a class="page-link" href="#" id="prevPage"><i class="material-symbols-outlined">keyboard_arrow_left</i></a></li>
                            <li class="row" id="customPagination"></li>
                            <li class="page-item"><a class="page-link" href="#" id="nextPage"><i class="material-symbols-outlined">keyboard_arrow_right</i></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>$('.dropify').dropify();</script>
    <script>
        var quillContainer = new Quill('#editor_container', { theme: 'snow', modules: { toolbar: [[{header:[1,2,3,false]}],['bold','italic'],['link']] } });
        quillContainer.root.innerHTML = {!! json_encode(old('description', $containerData->description ?? '')) !!};
        document.getElementById('coreResearchContainerForm').addEventListener('submit', function() {
            document.getElementById('description_container').value = quillContainer.root.innerHTML;
        });
    </script>
    <script src="{{ asset('backend/admin/assets/datatables/data-tables.min.js') }}"></script>
    <script>
        $(function() {
            let dTable = $('#basic_tables').DataTable({
                order: [], destroy: true,
                lengthMenu: [[10,25,50,-1],[10,25,50,'All']],
                processing: true, serverSide: true, paging: true,
                dom: "<'row'<'col-md-6'l>>tir",
                ajax: { url: "{{ route('cms.partnership_page.core_research_section.index') }}", type: 'get' },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title', render: d => d && d.length > 40 ? d.substring(0,40)+'...' : (d||'') },
                    { data: 'description', name: 'description', render: function(d) { if (!d) return ''; var t = String(d).replace(/<[^>]+>/g,''); return t.length > 60 ? t.substring(0,60)+'...' : t; } },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                drawCallback: function(s) {
                    const tp = Math.ceil(s._iRecordsDisplay / s._iDisplayLength);
                    const cp = s._iDisplayStart / s._iDisplayLength + 1;
                    const pc = $('#customPagination'); pc.empty();
                    for (let i = 1; i <= tp; i++) {
                        pc.append(`<li class="page-item"><a href="#" class="page-link pagination-item ${i===cp?'active':''}" data-page="${i}">${i}</a></li>`);
                    }
                    $('.pagination-item').off('click').on('click', function(e) { e.preventDefault(); dTable.page($(this).data('page')-1).draw('page'); });
                    $('#prevPage').off('click').on('click', function(e){ e.preventDefault(); if(cp>1) dTable.page(cp-2).draw('page'); });
                    $('#nextPage').off('click').on('click', function(e){ e.preventDefault(); if(cp<tp) dTable.page(cp).draw('page'); });
                }
            });
            $('#customSearchBox').on('keyup', function() { dTable.search(this.value).draw(); });
        });
    </script>
    <script src="{{ asset('backend/admin/assets/custom-actions.js') }}"></script>
    <script>
        function changeStatus(e,id){ e.preventDefault(); showStatusChangeAlert(id, '{{ route('cms.partnership_page.core_research_section.status', ':id') }}'.replace(':id', id)); }
        function deleteRecord(e,id){ e.preventDefault(); showDeleteConfirm(id, '{{ route('cms.partnership_page.core_research_section.destroy', ':id') }}'.replace(':id', id)); }
    </script>
@endpush
