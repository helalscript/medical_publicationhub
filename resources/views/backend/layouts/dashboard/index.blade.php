@extends('backend.app')

@section('title')
    Dashboard
@endsection

@push('scripts')
    <script>
        (function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            const chatStatusOptions = {
                chart: {
                    type: 'pie',
                    height: 260
                },
                labels: @json($chatPie['labels'] ?? []),
                series: @json($chatPie['series'] ?? []),
                colors: ['#22C55E', '#94A3B8'],
                legend: {
                    position: 'bottom'
                }
            };

            const chatTypeOptions = {
                chart: {
                    type: 'pie',
                    height: 260
                },
                labels: @json($chatTypePie['labels'] ?? []),
                series: @json($chatTypePie['series'] ?? []),
                colors: ['#3B82F6', '#A855F7'],
                legend: {
                    position: 'bottom'
                }
            };

            const messages7Options = {
                chart: {
                    type: 'line',
                    height: 260,
                    toolbar: { show: false }
                },
                stroke: { curve: 'smooth', width: 3 },
                colors: ['#0EA5E9'],
                series: [{
                    name: 'Messages',
                    data: @json($metrics['messages_last_7_days']['series'] ?? [])
                }],
                xaxis: {
                    categories: @json($metrics['messages_last_7_days']['labels'] ?? [])
                },
                grid: { borderColor: '#eef2f7' }
            };

            const statusBars = @json($statusBars ?? []);

            const makeBarOptions = (title, labels, series) => ({
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                plotOptions: { bar: { borderRadius: 8, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                colors: ['#0EA5E9'],
                series: [{ name: title, data: series || [] }],
                xaxis: { categories: labels || [] },
                grid: { borderColor: '#eef2f7' }
            });

            const statusEl = document.querySelector('#chat_status_pie');
            const typeEl = document.querySelector('#chat_type_pie');
            const messagesEl = document.querySelector('#messages_7_line');
            const statusBarsEl = document.querySelector('#status_bars');

            if (statusEl && (chatStatusOptions.series || []).length) {
                const chartStatus = new ApexCharts(statusEl, chatStatusOptions);
                chartStatus.render();
            }
            if (typeEl && (chatTypeOptions.series || []).length) {
                const chartType = new ApexCharts(typeEl, chatTypeOptions);
                chartType.render();
            }
            if (messagesEl && (messages7Options.series?.[0]?.data || []).length) {
                const chartMessages = new ApexCharts(messagesEl, messages7Options);
                chartMessages.render();
            }

            if (statusBarsEl) {
                const blocks = [];
                if (statusBars.courses?.labels?.length) blocks.push({ key: 'courses', title: 'Courses' });
                if (statusBars.opportunities?.labels?.length) blocks.push({ key: 'opportunities', title: 'Opportunities' });
                if (statusBars.payments?.labels?.length) blocks.push({ key: 'payments', title: 'Payments' });

                if (blocks.length) {
                    statusBarsEl.innerHTML = blocks.map(b => `
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card border-0 rounded-3 bg-white h-100">
                                <div class="custom-padding-30">
                                    <span class="d-block mb-1 text-muted">${b.title} overview</span>
                                    <div id="bar_${b.key}"></div>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    blocks.forEach(b => {
                        const el = document.querySelector(`#bar_${b.key}`);
                        if (!el) return;
                        const cfg = statusBars[b.key];
                        const chart = new ApexCharts(el, makeBarOptions(b.title, cfg.labels, cfg.series));
                        chart.render();
                    });
                }
            }
        })();
    </script>
@endpush

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
    <div class="main-content-container overflow-hidden">
        <div class="row">
            <div class="col-md-7">
                <div class="mb-4">
                    <h3 class="fs-20 fw-semibold mb-1">
                        Welcome Back,
                        <span class="text-primary">{{ Auth::user()->name ?? 'Admin' }}!</span>
                    </h3>
                    <p style="line-height: 1.4;">Overview of mentors, researchers, courses, opportunities and chats.</p>
                </div>
            </div>
        </div>

        <div class="row">
            @if(isset($cards['users']))
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 rounded-3 bg-white h-100">
                        <div class="custom-padding-30">
                            <span class="d-block mb-1 text-muted">Users</span>
                            <h3 class="fw-medium fs-20 mb-1">{{ $cards['users']['total'] ?? 0 }}</h3>
                            <div class="d-flex flex-wrap gap-2 small text-muted">
                                <span>Mentor: {{ $cards['users']['mentors'] ?? 0 }}</span>
                                <span>Researcher: {{ $cards['users']['researchers'] ?? 0 }}</span>
                                <span>Coordinator: {{ $cards['users']['coordinators'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($cards['courses']))
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 rounded-3 bg-white h-100">
                        <div class="custom-padding-30">
                            <span class="d-block mb-1 text-muted">Courses</span>
                            <h3 class="fw-medium fs-20 mb-1">{{ $cards['courses']['total'] ?? 0 }}</h3>
                            <div class="small text-muted">Active: {{ $cards['courses']['active'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($cards['opportunities']))
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 rounded-3 bg-white h-100">
                        <div class="custom-padding-30">
                            <span class="d-block mb-1 text-muted">Opportunities</span>
                            <h3 class="fw-medium fs-20 mb-1">{{ $cards['opportunities']['total'] ?? 0 }}</h3>
                            <div class="small text-muted">Active: {{ $cards['opportunities']['active'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($cards['content']))
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 rounded-3 bg-white h-100">
                        <div class="custom-padding-30">
                            <span class="d-block mb-1 text-muted">Services &amp; Publications</span>
                            <h3 class="fw-medium fs-20 mb-1">
                                {{ ($cards['content']['services'] ?? 0) + ($cards['content']['publications'] ?? 0) }}
                            </h3>
                            <div class="small text-muted">
                                Services: {{ $cards['content']['services'] ?? 0 }},
                                Publications: {{ $cards['content']['publications'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($cards['payments']))
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 rounded-3 bg-white h-100">
                        <div class="custom-padding-30">
                            <span class="d-block mb-1 text-muted">Payments</span>
                            <h3 class="fw-medium fs-20 mb-1">{{ $cards['payments']['total'] ?? 0 }}</h3>
                            <div class="small text-muted">Total payments records</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="row">
            @if(isset($cards['chats']))
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card border-0 rounded-3 bg-white h-100">
                        <div class="custom-padding-30">
                            <span class="d-block mb-1 text-muted">Chats overview</span>
                            <h3 class="fw-medium fs-20 mb-1">{{ $cards['chats']['total'] ?? 0 }} chats</h3>
                            <div class="small text-muted mb-2">
                                Messages: {{ $cards['chats']['messages'] ?? 0 }},
                                last 7 days: {{ $cards['chats']['messages_last_7_days'] ?? 0 }}
                            </div>
                            <div id="chat_status_pie"></div>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($cards['chats']))
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="card border-0 rounded-3 bg-white h-100">
                        <div class="custom-padding-30">
                            <span class="d-block mb-1 text-muted">Messages (last 7 days)</span>
                            <h3 class="fw-medium fs-20 mb-1">{{ $cards['chats']['messages_last_7_days'] ?? 0 }}</h3>
                            <div id="messages_7_line"></div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card border-0 rounded-3 bg-white h-100">
                    <div class="custom-padding-30">
                        <span class="d-block mb-1 text-muted">Quick links</span>
                        <div class="d-flex flex-column gap-2 mt-2">
                            @can('admin.opportunities.index')
                                <a href="{{ route('admin.opportunities.index') }}" class="btn btn-sm btn-outline-primary text-start">View Opportunities</a>
                            @endcan
                            @can('admin.courses.index')
                                <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-primary text-start">View Courses</a>
                            @endcan
                            @can('admin.opportunity_chats.index')
                                <a href="{{ route('admin.opportunity_chats.index') }}" class="btn btn-sm btn-outline-primary text-start">View Opportunity Chats</a>
                            @endcan
                            @can('admin.partnership_requests.index')
                                <a href="{{ route('admin.partnership_requests.index') }}" class="btn btn-sm btn-outline-primary text-start">Partnership Requests</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="status_bars"></div>

        <div class="row">
            {{-- <div class="col-lg-4 col-md-6">
                <div class="card border-0 rounded-3 bg-white mb-4">
                    <div class="custom-padding-30 position-relative">
                        <div class="d-flex align-items-center mb-4 pb-2">
                            <div class="flex-shrink-0">
                                <div class="text-center rounded-2 bg-primary-50"
                                    style="width: 44px; height: 44px; line-height: 44px;">
                                    <img src="{{ asset('backend/admin/assets/images/icon-employees.svg') }}"
                                        alt="icon-employees">
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <span class="d-block mb-1">Total Employees</span>
                                <h3 class="fw-medium fs-20 mb-0">15,720</h3>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="ri-arrow-right-up-line d-inline-block text-center rounded-1 fs-18 text-success-50"
                                style="width: 26px; height: 26px; line-height: 26px; background-color: #D8FFC8;"></i>
                            <p class="ms-2"><span class="text-secondary fw-medium">+12%</span> last year</p>
                        </div>

                        <div id="total_employees" class="chart-position top-50 translate-middle-y"></div>
                    </div>
                </div>
            </div> --}}
            {{-- <div class="col-lg-4 col-md-6">
                <div class="card border-0 rounded-3 bg-white mb-4">
                    <div class="custom-padding-30 position-relative">
                        <div class="d-flex align-items-center mb-4 pb-2">
                            <div class="flex-shrink-0">
                                <div class="text-center rounded-2 bg-danger-50"
                                    style="width: 44px; height: 44px; line-height: 44px;">
                                    <img src="{{ asset('backend/admin/assets/images/icon-resigned.svg') }}"
                                        alt="icon-resigned">
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <span class="d-block mb-1">Resigned Employees</span>
                                <h3 class="fw-medium fs-20 mb-0">3,18</h3>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="ri-arrow-right-down-line d-inline-block text-center rounded-1 fs-18 text-danger-50"
                                style="width: 26px; height: 26px; line-height: 26px; background-color: #FFE8D4;"></i>
                            <p class="ms-2"><span class="text-secondary fw-medium">-5%</span> last year</p>
                        </div>

                        <div id="resigned_employees" class="chart-position top-50 translate-middle-y"></div>
                    </div>
                </div>
            </div> --}}
            {{-- <div class="col-lg-4 col-md-12">
                <div class="card border-0 rounded-3 bg-white mb-4">
                    <div class="custom-padding-30 position-relative">
                        <div class="d-flex align-items-center mb-4 pb-2">
                            <div class="flex-shrink-0">
                                <div class="text-center rounded-2 bg-primary-div-50"
                                    style="width: 44px; height: 44px; line-height: 44px;">
                                    <img src="{{ asset('backend/admin/assets') }}/images/icon-employees.svg"
                                        alt="icon-employees">
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <span class="d-block mb-1">New Employees</span>
                                <h3 class="fw-medium fs-20 mb-0">8,24</h3>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="ri-arrow-right-up-line d-inline-block text-center rounded-1 fs-18 text-success-50"
                                style="width: 26px; height: 26px; line-height: 26px; background-color: #D8FFC8;"></i>
                            <p class="ms-2"><span class="text-secondary fw-medium">+10%</span> last year</p>
                        </div>

                        <div id="new_employees" class="chart-position top-50 translate-middle-y me-1 mt-2">
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        {{-- <div class="card bg-white border-0 rounded-3 mb-4">
            <div class="card-body p-0">
                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3 custom-padding-30 border-bottom pb-4">
                    <h3 class="mb-0">Employee List</h3>
                    <div class="d-flex align-items-center">
                        <form class="position-relative table-src-form">
                            <input type="text" id="SearchControl" class="form-control border-0" style="width: 265px;"
                                placeholder="Search for a name....">
                            <i
                                class="material-symbols-outlined position-absolute top-50 start-0 translate-middle-y">search</i>
                        </form>
                        <div class="dropdown action-opt">
                            <button class="btn bg-transparent p-0" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end bg-white border box-shadow">
                                <li>
                                    <a class="dropdown-item" href="javascript:;">
                                        <i data-feather="clock"></i>
                                        Today
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;">
                                        <i data-feather="pie-chart"></i>
                                        Last 7 Days
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;">
                                        <i data-feather="rotate-cw"></i>
                                        Last Month
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;">
                                        <i data-feather="calendar"></i>
                                        Last 1 Year
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;">
                                        <i data-feather="bar-chart"></i>
                                        All Time
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;">
                                        <i data-feather="eye"></i>
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;">
                                        <i data-feather="trash"></i>
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="default-table-area style-three employee-list for-data-table">
                    <div class="table-responsive">
                        <table class="table align-middle border-0" id="myTable">
                            <thead class="border-bottom">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Employee</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Position</th>
                                    <th scope="col">Joining Date</th>
                                    <th scope="col">Salary</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-medium">EMP001</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-6.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Olivia Turner</h4>
                                                <span class="fs-12 text-body">olivia@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Marketing</td>
                                    <td>Marketing Lead</td>
                                    <td>Jan 15, 2020</td>
                                    <td>$85,000</td>
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success p-2 fs-12 fw-normal">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP002</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-7.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Liam Bennett</h4>
                                                <span class="fs-12 text-body">liam@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Human Resources</td>
                                    <td>HR Manager</td>
                                    <td>Mar 10, 2021</td>
                                    <td>$75,000</td>
                                    <td>
                                        <span
                                            class="badge bg-primary-div bg-opacity-10 text-primary-div p-2 fs-12 fw-normal">On
                                            Leave</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP003</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-8.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Sophia Myers</h4>
                                                <span class="fs-12 text-body">sophia@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>IT</td>
                                    <td>Software Engineer</td>
                                    <td>Feb 22, 2019</td>
                                    <td>$95,000</td>
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success p-2 fs-12 fw-normal">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP004</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-9.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Ethan Collins</h4>
                                                <span class="fs-12 text-body">ethan@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Sales</td>
                                    <td>Sales Manager</td>
                                    <td>Apr 12, 2022</td>
                                    <td>$90,000</td>
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success p-2 fs-12 fw-normal">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP005</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-10.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Isabella Reed</h4>
                                                <span class="fs-12 text-body">isabella@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Finance</td>
                                    <td>Financial Analyst</td>
                                    <td>Aug 05, 2020</td>
                                    <td>$80,000</td>
                                    <td>
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger p-2 fs-12 fw-normal">Resigned</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP006</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-11.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Sophia Myers</h4>
                                                <span class="fs-12 text-body">sophia@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>IT</td>
                                    <td>Software Engineer</td>
                                    <td>Feb 22, 2019</td>
                                    <td>$95,000</td>
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success p-2 fs-12 fw-normal">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP007</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-12.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Isabella Reed</h4>
                                                <span class="fs-12 text-body">isabella@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Finance</td>
                                    <td>Financial Analyst</td>
                                    <td>Aug 05, 2020</td>
                                    <td>$80,000</td>
                                    <td>
                                        <span
                                            class="badge bg-danger bg-opacity-10 text-danger p-2 fs-12 fw-normal">Resigned</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP008</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-13.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Olivia Turner</h4>
                                                <span class="fs-12 text-body">olivia@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Marketing</td>
                                    <td>Marketing Lead</td>
                                    <td>Jan 15, 2020</td>
                                    <td>$85,000</td>
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success p-2 fs-12 fw-normal">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP009</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-14.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Liam Bennett</h4>
                                                <span class="fs-12 text-body">liam@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Human Resources</td>
                                    <td>HR Manager</td>
                                    <td>Mar 10, 2021</td>
                                    <td>$75,000</td>
                                    <td>
                                        <span
                                            class="badge bg-primary-div bg-opacity-10 text-primary-div p-2 fs-12 fw-normal">On
                                            Leave</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">EMP0010</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <img src="{{ asset('backend/admin/assets') }}/images/user-15.jpg"
                                                    class="rounded-circle" alt="user">
                                            </div>
                                            <div class="flex-grow-1 ms-2">
                                                <h4 class="fs-14 fw-medium mb-0">Ethan Collins</h4>
                                                <span class="fs-12 text-body">ethan@gmail.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Sales</td>
                                    <td>Sales Manager</td>
                                    <td>Apr 12, 2022</td>
                                    <td>$90,000</td>
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success p-2 fs-12 fw-normal">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-primary">visibility</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-body">edit</i>
                                            </button>
                                            <button class="ps-0 border-0 bg-transparent lh-1 position-relative top-2">
                                                <i class="material-symbols-outlined fs-18 text-danger">delete</i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection


@push('scripts')
@endpush
