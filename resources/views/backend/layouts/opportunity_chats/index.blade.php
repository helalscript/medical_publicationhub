@extends('backend.app')
@section('title', 'Opportunity Chats')

@push('styles')
    <style>
        [type=checkbox]:checked,
        [type=radio]:checked { background-size: 40% !important; }
        .table > :not(caption) > * > * {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h3 class="mb-0">Opportunity Chats</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-12 col-xxl-12 col-lg-12">
                <div class="card bg-white border-0 rounded-3 mb-4">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-4">
                            <span class="position-relative table-src-form me-0">
                                <input type="text" class="form-control" placeholder="Search here" id="customSearchBox">
                                <i class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y">search</i>
                            </span>
                        </div>

                        <div class="default-table-area style-two all-products">
                            <div class="table-responsive">
                                <table class="table align-middle" id="basic_tables">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Opportunity</th>
                                        <th scope="col">Participants</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap">
                                <span class="fs-12 fw-medium"></span>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination mb-0 justify-content-center">
                                        <li class="page-item">
                                            <a class="page-link icon" aria-label="Previous" href="#" id="prevPage">
                                                <i class="material-symbols-outlined">keyboard_arrow_left</i>
                                            </a>
                                        </li>
                                        <li class="row" id="customPagination"></li>
                                        <li class="page-item">
                                            <a class="page-link icon" aria-label="Next" href="#" id="nextPage">
                                                <i class="material-symbols-outlined">keyboard_arrow_right</i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend') }}/admin/assets/datatables/data-tables.min.js"></script>
    <script>
        $(document).ready(function() {
            let dTable = $('#basic_tables').DataTable({
                order: [],
                destroy: true,
                lengthMenu: [[10, 25, 50, 100, 200, 500, -1],[10, 25, 50, 100, 200, 500, "All"]],
                processing: true,
                responsive: true,
                serverSide: true,
                paging: true,
                autoWidth: false,
                columnDefs: [
                    { width: "5%", targets: 0 },
                    { width: "50%", targets: 1 },
                    { width: "15%", targets: 2 },
                    { width: "15%", targets: 3 },
                    { width: "15%", targets: 4 },
                ],
                language: {
                    lengthMenu: `<span style="margin-left: 20px;">Show _MENU_ entries</span>`,
                    processing: `<div class="text-center">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>`
                },
                dom: "<'row justify-content-between table-topbar'<'col-md-6 col-sm-4 px-0'l>>tir",
                ajax: { url: "{{ route('admin.opportunity_chats.index') }}", type: "get" },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'opportunity', name: 'chatable.title', orderable: false },
                    { data: 'participants', name: 'participants', orderable: false },
                    { data: 'status_badge', name: 'status', orderable: true, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                drawCallback: function(settings) {
                    const totalPages = Math.ceil(settings._iRecordsDisplay / settings._iDisplayLength);
                    const currentPage = settings._iDisplayStart / settings._iDisplayLength + 1;
                    updateCustomPagination(totalPages, currentPage);
                }
            });

            $('#customSearchBox').on('keyup', function() { dTable.search(this.value).draw(); });

            function updateCustomPagination(totalPages, currentPage) {
                const paginationContainer = $('#customPagination');
                paginationContainer.empty();

                const maxVisiblePages = 5;
                let startPage, endPage;

                if (totalPages <= maxVisiblePages) { startPage = 1; endPage = totalPages; }
                else if (currentPage <= Math.floor(maxVisiblePages / 2)) { startPage = 1; endPage = maxVisiblePages; }
                else if (currentPage + Math.floor(maxVisiblePages / 2) >= totalPages) { startPage = totalPages - maxVisiblePages + 1; endPage = totalPages; }
                else { startPage = currentPage - Math.floor(maxVisiblePages / 2); endPage = currentPage + Math.floor(maxVisiblePages / 2); }

                if (startPage > 1) {
                    paginationContainer.append(` <li class="page-item col-1"><a class="page-link active" href="#" data-page="1">1</a></li>`);
                    if (startPage > 2) paginationContainer.append(`<span class="ellipsis">...</span>`);
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationContainer.append(` <li class="page-item col-1"><a class="pagination-item page-link ${i === currentPage ? 'active' : ''}" href="#" data-page="${i}">${i}</a></li>`);
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) paginationContainer.append(`<span class="ellipsis">...</span>`);
                    paginationContainer.append(`<li class="page-item col-1"><a class="pagination-item page-link" data-page="${totalPages}">${totalPages}</a></li>`);
                }

                $('.pagination-item').off().on('click', function(e) {
                    e.preventDefault();
                    dTable.page($(this).data('page') - 1).draw('page');
                });
                $('#prevPage').off().on('click', function(e) {
                    e.preventDefault();
                    if (currentPage > 1) dTable.page(currentPage - 2).draw('page');
                });
                $('#nextPage').off().on('click', function(e) {
                    e.preventDefault();
                    if (currentPage < totalPages) dTable.page(currentPage).draw('page');
                });
            }
        });
    </script>
@endpush

