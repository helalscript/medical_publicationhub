@extends('backend.app')
@section('title', 'Offers')

@push('styles')
    <style>
        [type=checkbox]:checked, [type=radio]:checked { background-size: 40% !important; }
    </style>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="mb-1">Offers</h3>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-0 lh-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="fw-medium text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active"><span class="fw-medium">Offers</span></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-4">
                    <span class="position-relative table-src-form me-0">
                        <input type="text" class="form-control" placeholder="Search here" id="customSearchBox">
                        <i class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y">search</i>
                    </span>
                    @can('admin.offers.create')
                        <a href="{{ route('admin.offers.create') }}" class="btn btn-outline-primary py-1 px-2 px-sm-4 fs-14 fw-medium rounded-3 hover-bg">
                            <span class="py-sm-1 d-block">
                                <i class="ri-add-line d-none d-sm-inline-block"></i>
                                <span>Add New Offer</span>
                            </span>
                        </a>
                    @endcan
                </div>

                <div class="default-table-area style-two all-products">
                    <div class="table-responsive">
                        <table class="table align-middle" id="basic_tables">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Icon</th>
                                <th>Title</th>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Ends</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap">
                        <span class="fs-12 fw-medium"></span>
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0 justify-content-center">
                                <li class="page-item">
                                    <a class="page-link icon" href="#" id="prevPage" aria-label="Previous">
                                        <i class="material-symbols-outlined">keyboard_arrow_left</i>
                                    </a>
                                </li>
                                <li class="row" id="customPagination"></li>
                                <li class="page-item">
                                    <a class="page-link icon" href="#" id="nextPage" aria-label="Next">
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
@endsection

@push('scripts')
    <script src="{{ asset('backend') }}/admin/assets/datatables/data-tables.min.js"></script>
    <script>
        $(document).ready(function() {
            let dTable = $('#basic_tables').DataTable({
                order: [[5, 'asc']],
                destroy: true,
                lengthMenu: [[10, 25, 50, 100],[10, 25, 50, 100]],
                processing: true,
                responsive: true,
                serverSide: true,
                paging: true,
                language: {
                    processing: `<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>`
                },
                dom: "<'row justify-content-between table-topbar'<'col-md-6 col-sm-4 px-0'l>>tir",
                ajax: { url: "{{ route('admin.offers.index') }}", type: "get" },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'icon_display', name: 'icon', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'promo_code_badge', name: 'promo_code' },
                    { data: 'discount_percentage', name: 'discount_percentage' },
                    { data: 'end_date_fmt', name: 'end_date' },
                    { data: 'status', name: 'status', orderable: true, searchable: false },
                    { data: 'action', orderable: false, searchable: false },
                ],
                drawCallback: function(settings) {
                    const totalPages = Math.ceil(settings._iRecordsDisplay / settings._iDisplayLength) || 1;
                    const currentPage = settings._iDisplayStart / settings._iDisplayLength + 1;
                    updateCustomPagination(totalPages, currentPage);
                }
            });

            $('#customSearchBox').on('keyup', function() { dTable.search(this.value).draw(); });

            function updateCustomPagination(totalPages, currentPage) {
                const paginationContainer = $('#customPagination');
                paginationContainer.empty();
                for (let i = 1; i <= totalPages; i++) {
                    paginationContainer.append(`<li class="page-item col-auto"><a class="pagination-item page-link ${i === currentPage ? 'active' : ''}" href="#" data-page="${i}">${i}</a></li>`);
                }
                $('.pagination-item').off('click').on('click', function(e) {
                    e.preventDefault();
                    dTable.page($(this).data('page') - 1).draw('page');
                });
                $('#prevPage').off('click').on('click', function(e) {
                    e.preventDefault();
                    if (currentPage > 1) dTable.page(currentPage - 2).draw('page');
                });
                $('#nextPage').off('click').on('click', function(e) {
                    e.preventDefault();
                    if (currentPage < totalPages) dTable.page(currentPage).draw('page');
                });
            }
        });
    </script>
    <script src="{{ asset('backend/admin/assets/custom-actions.js') }}"></script>
    <script>
        function changeStatus(event, id) {
            showStatusChangeAlert(id, '{{ route('admin.offers.status', ':id') }}', event);
        }
        function deleteRecord(event, id) {
            showDeleteConfirm(id, '{{ route('admin.offers.destroy', ':id') }}', event);
        }
    </script>
@endpush
