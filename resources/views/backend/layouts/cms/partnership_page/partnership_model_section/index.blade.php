@extends('backend.app')
@section('title', 'Partnership - Partnership Model')

@push('styles')
    <style>[type=checkbox]:checked,[type=radio]:checked{background-size:40%!important}</style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Partnership Model</h3>
            <nav style="--bs-breadcrumb-divider:'>';">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><span class="fw-medium">Partnership Page</span></li>
                    <li class="breadcrumb-item active">Partnership Model</li>
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-5 col-xxl-5 col-lg-6">
        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <h4 class="fs-20 mb-3">Section (title & description)</h4>
                <form action="{{ route('cms.partnership_page.partnership_model_section.update_container') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="label text-secondary">Title</label>
                        <input type="text" name="title" class="form-control h-55" value="{{ old('title', $containerData->title ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="label text-secondary">Description</label>
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $containerData->description ?? '') }}</textarea>
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
                    <a href="{{ route('cms.partnership_page.partnership_model_section.create') }}" class="btn btn-outline-primary">Add item</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle" id="basic_tables">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Button</th>
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
    <script src="{{ asset('backend/admin/assets/datatables/data-tables.min.js') }}"></script>
    <script>
        $(function() {
            let dTable = $('#basic_tables').DataTable({
                order: [], destroy: true,
                lengthMenu: [[10,25,50,-1],[10,25,50,'All']],
                processing: true, serverSide: true, paging: true,
                dom: "<'row'<'col-md-6'l>>tir",
                ajax: { url: "{{ route('cms.partnership_page.partnership_model_section.index') }}", type: 'get' },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title', render: d => d && d.length > 35 ? d.substring(0,35)+'...' : (d||'') },
                    { data: 'description', name: 'description', render: function(d) { if (!d) return ''; var t = String(d).replace(/<[^>]+>/g,''); return t.length > 50 ? t.substring(0,50)+'...' : t; } },
                    { data: 'button_text', name: 'button_text' },
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
        function changeStatus(e,id){ e.preventDefault(); showStatusChangeAlert(id, '{{ route('cms.partnership_page.partnership_model_section.status', ':id') }}'.replace(':id', id)); }
        function deleteRecord(e,id){ e.preventDefault(); showDeleteConfirm(id, '{{ route('cms.partnership_page.partnership_model_section.destroy', ':id') }}'.replace(':id', id)); }
    </script>
@endpush
