@extends('backend.app')
@section('title', 'Opportunity Chats')

@push('styles')
    <style>
        .oc-chats-page {
            --oc-surface: #ffffff;
            --oc-surface-muted: #fafbfc;
            --oc-border: #eef2f7;
            --oc-border-soft: #e2e8f0;
            --oc-row-border: #f1f5f9;
            --oc-text: #0f172a;
            --oc-text-secondary: #334155;
            --oc-text-muted: #64748b;
            --oc-text-faint: #94a3b8;
            --oc-pill-bg: #ffffff;
            --oc-pill-text: #475569;
            --oc-pill-active-bg: #eff6ff;
            --oc-pill-active-border: #bfdbfe;
            --oc-pill-active-text: #2563eb;
            --oc-action-hover: #f1f5f9;
            --oc-action-text: #64748b;
            --oc-avatar-ring: #ffffff;
            --oc-bar-track: #e2e8f0;
            --oc-status-active-bg: #dcfce7;
            --oc-status-active-text: #15803d;
            --oc-status-idle-bg: #ffedd5;
            --oc-status-idle-text: #c2410c;
            --oc-status-silent-bg: #f1f5f9;
            --oc-status-silent-text: #64748b;
        }

        [data-theme="dark"] .oc-chats-page {
            --oc-surface: #0c1427;
            --oc-surface-muted: #15203c;
            --oc-border: #172036;
            --oc-border-soft: #1e294b;
            --oc-row-border: #172036;
            --oc-text: #f1f5f9;
            --oc-text-secondary: #cbd5e1;
            --oc-text-muted: #94a3b8;
            --oc-text-faint: #64748b;
            --oc-pill-bg: #15203c;
            --oc-pill-text: #cbd5e1;
            --oc-pill-active-bg: rgba(96, 93, 255, 0.18);
            --oc-pill-active-border: rgba(96, 93, 255, 0.45);
            --oc-pill-active-text: #a5b4fc;
            --oc-action-hover: #1e294b;
            --oc-action-text: #94a3b8;
            --oc-avatar-ring: #0c1427;
            --oc-bar-track: #1e294b;
            --oc-status-active-bg: rgba(34, 197, 94, 0.16);
            --oc-status-active-text: #4ade80;
            --oc-status-idle-bg: rgba(251, 146, 60, 0.16);
            --oc-status-idle-text: #fb923c;
            --oc-status-silent-bg: rgba(148, 163, 184, 0.16);
            --oc-status-silent-text: #94a3b8;
        }

        .oc-page-header h3 {
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .oc-stat-card {
            background: var(--oc-surface);
            border: 1px solid var(--oc-border);
            border-radius: 14px;
            padding: 1.25rem 1.35rem;
            height: 100%;
        }

        .oc-stat-label {
            color: var(--oc-text-muted);
            font-size: 0.875rem;
            margin-bottom: 0.35rem;
        }

        .oc-stat-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--oc-text);
            line-height: 1.2;
        }

        .oc-stat-meta {
            font-size: 0.8125rem;
            margin-top: 0.35rem;
        }

        .oc-stat-meta.text-success { color: #16a34a !important; }
        .oc-stat-meta.text-warning { color: #d97706 !important; }
        .oc-stat-meta.text-muted { color: var(--oc-text-faint) !important; }

        [data-theme="dark"] .oc-chats-page .oc-stat-meta.text-success { color: #4ade80 !important; }
        [data-theme="dark"] .oc-chats-page .oc-stat-meta.text-warning { color: #fb923c !important; }

        .oc-toolbar-card {
            background: var(--oc-surface);
            border: 1px solid var(--oc-border);
            border-radius: 14px;
            overflow: hidden;
        }

        .oc-search-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
            max-width: 420px;
        }

        .oc-search-wrap input {
            padding-left: 2.5rem;
            border-radius: 10px;
            border-color: var(--oc-border-soft);
            height: 42px;
        }

        .oc-search-wrap i {
            left: 0.85rem;
            color: var(--oc-text-faint);
            font-size: 20px;
        }

        .oc-filter-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .oc-filter-pill {
            border: 1px solid var(--oc-border-soft);
            background: var(--oc-pill-bg);
            color: var(--oc-pill-text);
            border-radius: 999px;
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .oc-filter-pill:hover,
        .oc-filter-pill.active {
            background: var(--oc-pill-active-bg);
            border-color: var(--oc-pill-active-border);
            color: var(--oc-pill-active-text);
        }

        .oc-chats-page .oc-table thead th {
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--oc-text-faint) !important;
            border-bottom: 1px solid var(--oc-border) !important;
            padding: 0.9rem 1rem;
            background: var(--oc-surface-muted) !important;
        }

        .oc-chats-page .oc-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--oc-row-border) !important;
            color: var(--oc-text-secondary) !important;
            background: transparent !important;
        }

        .oc-opportunity-title {
            display: block;
            font-weight: 600;
            color: var(--oc-text);
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .oc-opportunity-subtitle {
            display: block;
            font-size: 0.8125rem;
            color: var(--oc-text-faint);
            margin-top: 0.15rem;
        }

        .oc-participants {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .oc-participant-avatar,
        .oc-participant-initial {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            border: 2px solid var(--oc-avatar-ring);
            margin-left: -6px;
        }

        .oc-participants > :first-child {
            margin-left: 0;
        }

        .oc-participant-count {
            font-size: 0.875rem;
            color: var(--oc-text-muted);
            margin-left: 0.35rem;
        }

        .oc-activity {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            min-width: 120px;
        }

        .oc-activity-bar {
            flex: 1;
            height: 6px;
            background: var(--oc-bar-track);
            border-radius: 999px;
            overflow: hidden;
        }

        .oc-activity-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, #60a5fa, #3b82f6);
            border-radius: 999px;
        }

        .oc-activity-value {
            font-size: 0.8125rem;
            color: var(--oc-text-muted);
            min-width: 32px;
        }

        .oc-last-message-title {
            display: block;
            font-size: 0.875rem;
            color: var(--oc-text-secondary);
        }

        .oc-last-message-sub {
            display: block;
            font-size: 0.75rem;
            color: var(--oc-text-faint);
            margin-top: 0.1rem;
        }

        .oc-status-badge {
            display: inline-block;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .oc-status-active {
            background: var(--oc-status-active-bg);
            color: var(--oc-status-active-text);
        }

        .oc-status-idle {
            background: var(--oc-status-idle-bg);
            color: var(--oc-status-idle-text);
        }

        .oc-status-silent {
            background: var(--oc-status-silent-bg);
            color: var(--oc-status-silent-text);
        }

        .oc-actions {
            display: flex;
            align-items: center;
            gap: 0.15rem;
        }

        .oc-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 0;
            background: transparent;
            border-radius: 8px;
            color: var(--oc-action-text);
            text-decoration: none;
        }

        .oc-action-btn:hover {
            background: var(--oc-action-hover);
            color: var(--oc-pill-active-text);
        }

        [data-theme="dark"] .oc-chats-page .default-table-area.style-two .showing-wrap {
            border-top-color: var(--oc-border) !important;
        }

        [data-theme="dark"] .oc-chats-page .dropdown-menu {
            background-color: #0c1427;
            border-color: #172036;
        }

        [data-theme="dark"] .oc-chats-page .dropdown-item {
            color: #cbd5e1;
        }

        [data-theme="dark"] .oc-chats-page .dropdown-item:hover {
            background-color: #15203c;
            color: #fff;
        }

        .oc-chats-page .table > :not(caption) > * > * {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
    </style>
@endpush

@section('content')
    @php
        $stats = $stats ?? [
            'total' => 0,
            'this_week' => 0,
            'active_now' => 0,
            'idle' => 0,
            'engagement_percent' => 0,
            'total_participants' => 0,
        ];
    @endphp

    <div class="main-content-container overflow-hidden oc-chats-page">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 oc-page-header">
            <h3 class="mb-0">Opportunity chats</h3>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" id="exportChatsBtn" class="btn btn-outline-secondary rounded-3 px-3">
                    <i class="material-symbols-outlined fs-18 align-middle me-1">open_in_new</i>
                    Export
                </button>
                @can('admin.opportunities.index')
                    <a href="{{ route('admin.opportunities.index') }}" class="btn btn-outline-primary rounded-3 px-3">
                        <i class="ri-add-line align-middle me-1"></i>
                        New chat
                    </a>
                @endcan
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="oc-stat-card">
                    <div class="oc-stat-label">Total chats</div>
                    <div class="oc-stat-value">{{ number_format($stats['total']) }}</div>
                    @if ($stats['this_week'] > 0)
                        <div class="oc-stat-meta text-success">↑ {{ $stats['this_week'] }} this week</div>
                    @else
                        <div class="oc-stat-meta text-muted">No new chats this week</div>
                    @endif
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="oc-stat-card">
                    <div class="oc-stat-label">Active now</div>
                    <div class="oc-stat-value">{{ number_format($stats['active_now']) }}</div>
                    <div class="oc-stat-meta text-success">{{ $stats['engagement_percent'] }}% engagement</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="oc-stat-card">
                    <div class="oc-stat-label">Idle chats</div>
                    <div class="oc-stat-value">{{ number_format($stats['idle']) }}</div>
                    <div class="oc-stat-meta text-warning">No msg &gt;48h</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="oc-stat-card">
                    <div class="oc-stat-label">Total participants</div>
                    <div class="oc-stat-value">{{ number_format($stats['total_participants']) }}</div>
                    <div class="oc-stat-meta text-muted">Across all chats</div>
                </div>
            </div>
        </div>

        <div class="oc-toolbar-card mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-4 border-bottom">
                <div class="oc-search-wrap position-relative">
                    <input type="text" class="form-control" placeholder="Search opportunities..." id="customSearchBox">
                    <i class="material-symbols-outlined position-absolute top-50 translate-middle-y">search</i>
                </div>
                <div class="oc-filter-pills" id="engagementFilters">
                    <button type="button" class="oc-filter-pill active" data-filter="all">All</button>
                    <button type="button" class="oc-filter-pill" data-filter="active">Active</button>
                    <button type="button" class="oc-filter-pill" data-filter="idle">Idle</button>
                    <button type="button" class="oc-filter-pill" data-filter="silent">Silent</button>
                </div>
            </div>

            <div class="default-table-area style-two all-products">
                <div class="table-responsive">
                    <table class="table align-middle oc-table mb-0" id="basic_tables">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Opportunity</th>
                            <th scope="col">Participants</th>
                            <th scope="col">Activity</th>
                            <th scope="col">Last message</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center justify-content-sm-between align-items-center text-center flex-wrap gap-2 showing-wrap p-3 border-top">
                    <span class="fs-12 fw-medium text-muted" id="tableInfo"></span>
                    <nav aria-label="Page navigation">
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
@endsection

@push('scripts')
    <script src="{{ asset('backend') }}/admin/assets/datatables/data-tables.min.js"></script>
    <script src="{{ asset('backend') }}/admin/assets/datatables/datatables.buttons.min.js"></script>
    <script src="{{ asset('backend') }}/admin/assets/datatables/jszip.min.js"></script>
    <script src="{{ asset('backend') }}/admin/assets/datatables/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            let engagementFilter = 'all';

            let dTable = $('#basic_tables').DataTable({
                order: [],
                destroy: true,
                lengthMenu: [[10, 25, 50, 100],[10, 25, 50, 100]],
                processing: true,
                responsive: true,
                serverSide: true,
                paging: true,
                autoWidth: false,
                searching: true,
                language: {
                    lengthMenu: `<span style="margin-left: 20px;">Show _MENU_ entries</span>`,
                    processing: `<div class="text-center py-4">
                        <div class="spinner-border text-primary" style="width: 2.5rem; height: 2.5rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>`
                },
                dom: 'tir',
                ajax: {
                    url: "{{ route('admin.opportunity_chats.index') }}",
                    type: "get",
                    data: function(d) {
                        d.engagement_filter = engagementFilter;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'opportunity', name: 'chatable.title', orderable: false },
                    { data: 'participants', name: 'participants', orderable: false, searchable: false },
                    { data: 'activity', name: 'activity', orderable: false, searchable: false },
                    { data: 'last_message', name: 'last_message', orderable: false, searchable: false },
                    { data: 'engagement_status', name: 'engagement_status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                drawCallback: function(settings) {
                    const api = this.api();
                    const info = api.page.info();
                    const totalPages = Math.ceil(info.recordsDisplay / info.length) || 1;
                    const currentPage = info.page + 1;

                    $('#tableInfo').text(
                        info.recordsDisplay === 0
                            ? 'Showing 0 of 0'
                            : `Showing ${info.start + 1}–${info.end} of ${info.recordsDisplay}`
                    );

                    updateCustomPagination(totalPages, currentPage);
                }
            });

            $('#customSearchBox').on('keyup', function() {
                dTable.search(this.value).draw();
            });

            $('#engagementFilters .oc-filter-pill').on('click', function() {
                $('#engagementFilters .oc-filter-pill').removeClass('active');
                $(this).addClass('active');
                engagementFilter = $(this).data('filter');
                dTable.ajax.reload();
            });

            $('#exportChatsBtn').on('click', function() {
                const params = new URLSearchParams({
                    engagement_filter: engagementFilter,
                    search: $('#customSearchBox').val() || ''
                });
                window.location.href = "{{ route('admin.opportunity_chats.index') }}?" + params.toString() + '&export=1';
            });

            function updateCustomPagination(totalPages, currentPage) {
                const paginationContainer = $('#customPagination');
                paginationContainer.empty();

                const maxVisiblePages = 5;
                let startPage, endPage;

                if (totalPages <= maxVisiblePages) {
                    startPage = 1;
                    endPage = totalPages;
                } else if (currentPage <= Math.floor(maxVisiblePages / 2)) {
                    startPage = 1;
                    endPage = maxVisiblePages;
                } else if (currentPage + Math.floor(maxVisiblePages / 2) >= totalPages) {
                    startPage = totalPages - maxVisiblePages + 1;
                    endPage = totalPages;
                } else {
                    startPage = currentPage - Math.floor(maxVisiblePages / 2);
                    endPage = currentPage + Math.floor(maxVisiblePages / 2);
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationContainer.append(
                        `<li class="page-item col-auto"><a class="pagination-item page-link ${i === currentPage ? 'active' : ''}" href="#" data-page="${i}">${i}</a></li>`
                    );
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
@endpush
