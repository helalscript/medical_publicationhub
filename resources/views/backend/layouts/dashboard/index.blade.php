@extends('backend.app')
@section('title', __('admin.dash.title'))

@php
    $chartLabels = [
        'users' => __('admin.chart.users'),
        'courses' => __('admin.chart.courses'),
        'opportunities' => __('admin.chart.opportunities'),
        'messages' => __('admin.chart.messages'),
    ];
    $barTitles = [
        'courses' => __('admin.dash.courses_status'),
        'opportunities' => __('admin.dash.opportunities_status'),
        'payments' => __('admin.dash.payments_status'),
    ];

    $userName = Auth::user()->name ?? 'Admin';
    $systemName = App\Models\SystemSetting::first()?->system_name ?? 'PublicationHub';
    
    // Safety fallback variables
    $uTotal = $cards['users']['total'] ?? 0;
    $uActive = $cards['users']['active'] ?? 0;
    $uThisMonth = $cards['users']['this_month'] ?? 0;
    $uMentors = $cards['users']['mentors'] ?? 0;
    $uResearchers = $cards['users']['researchers'] ?? 0;
    $uCoordinators = $cards['users']['coordinators'] ?? 0;

    $cTotal = $cards['courses']['total'] ?? 0;
    $cActive = $cards['courses']['active'] ?? 0;
    $cInactive = $cards['courses']['inactive'] ?? 0;
    $cThisMonth = $cards['courses']['this_month'] ?? 0;

    $oTotal = $cards['opportunities']['total'] ?? 0;
    $oActive = $cards['opportunities']['active'] ?? 0;
    $oInactive = $cards['opportunities']['inactive'] ?? 0;
    $oThisMonth = $cards['opportunities']['this_month'] ?? 0;
    $oPendingApps = $cards['opportunities']['pending_applications'] ?? 0;

    $srvCount = $cards['content']['services'] ?? 0;
    $pubCount = $cards['content']['publications'] ?? 0;
    $contentTotal = $srvCount + $pubCount;

    $payTotal = $cards['payments']['total'] ?? 0;
    $payPending = $cards['payments']['pending'] ?? 0;
    $payCompleted = max(0, $payTotal - $payPending);

    // Chat distribution overview
    $chatsTotal = $cards['chats']['total'] ?? 0;
    $chatsActive = $cards['chats']['active'] ?? 0;
    $chatsInactive = max(0, $chatsTotal - $chatsActive);
    $chatsActivePercent = $chatsTotal > 0 ? round(($chatsActive / $chatsTotal) * 100) : 0;

    $msg7Total = $cards['chats']['messages_last_7_days'] ?? 0;
@endphp

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        /* Modern Premium Dashboard Styling */
        .dash-container {
            padding: 0.5rem 0;
        }

        /* Banner Welcome Style */
        .premium-welcome-banner {
            background: linear-gradient(135deg, #13213c 0%, #1e293b 100%);
            border-radius: 24px;
            padding: 2.25rem 2.5rem;
            color: #ffffff !important;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(19, 33, 60, 0.15);
            margin-bottom: 1.75rem;
        }

        .premium-welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(12, 186, 184, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .premium-welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: 10%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .banner-subtitle-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #0cbab8 !important;
            margin-bottom: 0.75rem;
        }

        .premium-welcome-banner h2 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            letter-spacing: -0.02em;
            color: #ffffff !important;
        }

        .premium-welcome-banner p {
            margin: 0 0 1.75rem 0;
            opacity: 0.8;
            font-size: 0.95rem;
            max-width: 550px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .banner-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-banner-primary {
            background-color: #0cbab8;
            color: #ffffff !important;
            border: none;
            padding: 0.65rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(12, 186, 184, 0.25);
            text-decoration: none;
        }

        .btn-banner-primary:hover {
            background-color: #0a9d9b;
            transform: translateY(-1px);
        }

        .btn-banner-secondary {
            background-color: transparent;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.65rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-banner-secondary:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-1px);
        }

        /* Translucent Stats Grid inside Banner */
        .banner-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
        }

        .banner-stat-box {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            backdrop-filter: blur(8px);
        }

        .banner-stat-label {
            font-size: 0.6875rem;
            color: rgba(255, 255, 255, 0.45) !important;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .banner-stat-val {
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff !important;
        }

        .banner-stat-val.status-online { color: #22c55e !important; }
        .banner-stat-val.color-purple { color: #a78bfa !important; }
        .banner-stat-val.color-yellow { color: #fcd34d !important; }
        .banner-stat-val.color-cyan { color: #0cbab8 !important; }

        /* Row of 5 Cards Styling */
        .stats-cards-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        @media (max-width: 1200px) {
            .stats-cards-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-cards-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card-premium {
            background-color: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            padding: 1.25rem;
            box-shadow: var(--dash-shadow-sm);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-card-premium:hover {
            transform: translateY(-3px);
            box-shadow: var(--dash-shadow);
            border-color: rgba(12, 186, 184, 0.3);
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .stat-card-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card-icon-box.cyan { background-color: rgba(12, 186, 184, 0.08); color: #0cbab8; }
        .stat-card-icon-box.green { background-color: rgba(34, 197, 94, 0.08); color: #22c55e; }
        .stat-card-icon-box.purple { background-color: rgba(139, 92, 246, 0.08); color: #8b5cf6; }
        .stat-card-icon-box.orange { background-color: rgba(249, 115, 22, 0.08); color: #f97316; }
        .stat-card-icon-box.grey { background-color: rgba(100, 116, 139, 0.08); color: #64748b; }

        .stat-card-icon-box span {
            font-size: 20px;
        }

        .stat-growth-badge {
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
        }

        .stat-growth-badge.positive { background-color: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .stat-growth-badge.neutral { background-color: rgba(100, 116, 139, 0.08); color: #64748b; }

        .stat-card-value {
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--dash-text);
            line-height: 1.1;
        }

        .stat-card-title {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--dash-muted);
            margin-top: 0.15rem;
        }

        .stat-card-sparkline {
            height: 40px;
            margin: 0.75rem -0.5rem 0.5rem -0.5rem;
        }

        .stat-card-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-top: 0.5rem;
            padding-top: 0.65rem;
            border-top: 1px solid var(--dash-border);
        }

        .stat-card-pill-item {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.15rem 0.45rem;
            border-radius: 30px;
        }

        .stat-card-pill-item.cyan { background-color: rgba(12, 186, 184, 0.05); color: #0cbab8; }
        .stat-card-pill-item.green { background-color: rgba(34, 197, 94, 0.05); color: #22c55e; }
        .stat-card-pill-item.purple { background-color: rgba(139, 92, 246, 0.05); color: #8b5cf6; }
        .stat-card-pill-item.orange { background-color: rgba(249, 115, 22, 0.05); color: #f97316; }
        .stat-card-pill-item.grey { background-color: rgba(100, 116, 139, 0.05); color: #64748b; }

        /* Panels layout spacing */
        .dash-premium-panel {
            background-color: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            box-shadow: var(--dash-shadow-sm);
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .panel-premium-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--dash-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-premium-header h5 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--dash-text);
        }

        .panel-premium-header p {
            margin: 0.15rem 0 0 0;
            font-size: 0.72rem;
            color: var(--dash-muted);
        }

        .panel-premium-body {
            padding: 1.25rem 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Chat Donut styling */
        .chat-donut-footer {
            display: flex;
            justify-content: center;
            gap: 1.25rem;
            margin-top: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--dash-muted);
        }

        .chat-indicator-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
        }
        .chat-indicator-dot.active { background-color: #0cbab8; }
        .chat-indicator-dot.inactive { background-color: #e2e8f0; }
        [data-theme="dark"] .chat-indicator-dot.inactive { background-color: #334155; }

        /* Quick Links styling */
        .quick-links-vertical {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .quick-link-premium-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--dash-surface);
            border: 1px solid var(--dash-border);
            border-radius: 12px;
            padding: 0.75rem 1.1rem;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }

        .quick-link-premium-item:hover {
            transform: translateY(-1px);
            box-shadow: var(--dash-shadow-sm);
            border-color: var(--dash-primary);
        }

        .quick-link-premium-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--dash-text);
        }

        .quick-link-premium-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .quick-link-premium-dot.cyan { background-color: #0cbab8; }
        .quick-link-premium-dot.green { background-color: #22c55e; }
        .quick-link-premium-dot.purple { background-color: #8b5cf6; }
        .quick-link-premium-dot.orange { background-color: #f97316; }

        .quick-link-premium-arrow {
            font-size: 14px;
            color: var(--dash-muted);
            transition: transform 0.2s ease;
        }

        .quick-link-premium-item:hover .quick-link-premium-arrow {
            transform: translateX(3px);
            color: var(--dash-primary);
        }
        
        html[dir="rtl"] .quick-link-premium-item:hover .quick-link-premium-arrow {
            transform: translateX(-3px);
        }

        /* Responsive grid overrides */
        .bottom-charts-row {
            margin-top: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="dash-container">
        {{-- Header Title Row --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0 fw-bold fs-20">{{ __('admin.dash.title') }} - {{ __('admin.dash.overview_title') }}</h4>
                <p class="text-muted small mb-0">{{ __('admin.dash.overview') }}</p>
            </div>
        </div>

        {{-- Top Welcome Banner --}}
        <div class="premium-welcome-banner">
            <div class="row align-items-center g-4">
                <div class="col-xl-7 col-lg-6">
                    <span class="banner-subtitle-badge">+ {{ __('admin.dash.welcome_back_badge') }}</span>
                    <h2>{{ __('admin.dash.welcome', ['name' => $userName]) }}!</h2>
                    <p>{{ __('admin.dash.overview_desc') }}</p>
                    <div class="banner-actions">
                        <a href="{{ route('admin.opportunities.index') }}" class="btn-banner-primary">{{ __('admin.dash.view_reports') }}</a>
                        <a href="javascript:void(0);" class="btn-banner-secondary">{{ __('admin.dash.export_data') }}</a>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-6">
                    <div class="banner-stats-grid">
                        <div class="banner-stat-box">
                            <div class="banner-stat-label">{{ __('admin.dash.last_updated') }}</div>
                            <div class="banner-stat-val color-cyan">{{ __('admin.dash.just_now') }}</div>
                        </div>
                        <div class="banner-stat-box">
                            <div class="banner-stat-label">{{ __('admin.dash.platform_status') }}</div>
                            <div class="banner-stat-val status-online">{{ __('admin.dash.online') }}</div>
                        </div>
                        <div class="banner-stat-box">
                            <div class="banner-stat-label">{{ __('admin.dash.active_sessions') }}</div>
                            <div class="banner-stat-val color-purple">3</div>
                        </div>
                        <div class="banner-stat-box">
                            <div class="banner-stat-label">{{ __('admin.dash.system_health') }}</div>
                            <div class="banner-stat-val color-yellow">100%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row of 5 Stats Cards --}}
        <div class="stats-cards-grid">
            {{-- Card 1: Users --}}
            <div class="stat-card-premium">
                <div>
                    <div class="stat-card-header">
                        <div class="stat-card-icon-box cyan">
                            <span class="material-symbols-outlined">group</span>
                        </div>
                        <span class="stat-growth-badge positive">+12%</span>
                    </div>
                    <div class="stat-card-value">{{ number_format($uTotal) }}</div>
                    <div class="stat-card-title">{{ __('admin.dash.users') }}</div>
                </div>
                <div>
                    <div id="sparkline_users" class="stat-card-sparkline"></div>
                    <div class="stat-card-pills">
                        <span class="stat-card-pill-item cyan">{{ __('admin.menu.mentor') }}: {{ $uMentors }}</span>
                        <span class="stat-card-pill-item cyan">{{ __('admin.menu.researcher') }}: {{ $uResearchers }}</span>
                        <span class="stat-card-pill-item cyan">{{ __('admin.menu.coordinator') }}: {{ $uCoordinators }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Courses --}}
            <div class="stat-card-premium">
                <div>
                    <div class="stat-card-header">
                        <div class="stat-card-icon-box green">
                            <span class="material-symbols-outlined">school</span>
                        </div>
                        <span class="stat-growth-badge positive">+8%</span>
                    </div>
                    <div class="stat-card-value">{{ number_format($cTotal) }}</div>
                    <div class="stat-card-title">{{ __('admin.dash.courses') }}</div>
                </div>
                <div>
                    <div id="sparkline_courses" class="stat-card-sparkline"></div>
                    <div class="stat-card-pills">
                        <span class="stat-card-pill-item green">{{ __('admin.status.active') }}: {{ $cActive }}</span>
                        <span class="stat-card-pill-item green">{{ __('admin.status.inactive') }}: {{ $cInactive }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Opportunities --}}
            <div class="stat-card-premium">
                <div>
                    <div class="stat-card-header">
                        <div class="stat-card-icon-box purple">
                            <span class="material-symbols-outlined">work</span>
                        </div>
                        <span class="stat-growth-badge positive">+15%</span>
                    </div>
                    <div class="stat-card-value">{{ number_format($oTotal) }}</div>
                    <div class="stat-card-title">{{ __('admin.dash.opportunities_label') }}</div>
                </div>
                <div>
                    <div id="sparkline_opportunities" class="stat-card-sparkline"></div>
                    <div class="stat-card-pills">
                        <span class="stat-card-pill-item purple">{{ __('admin.status.open') }}: {{ $oActive }}</span>
                        <span class="stat-card-pill-item purple">{{ __('admin.status.closed') }}: {{ $oInactive }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Services & Publications --}}
            <div class="stat-card-premium">
                <div>
                    <div class="stat-card-header">
                        <div class="stat-card-icon-box orange">
                            <span class="material-symbols-outlined">article</span>
                        </div>
                        <span class="stat-growth-badge positive">+5%</span>
                    </div>
                    <div class="stat-card-value">{{ number_format($contentTotal) }}</div>
                    <div class="stat-card-title">{{ __('admin.dash.services_publications') }}</div>
                </div>
                <div>
                    <div id="sparkline_content" class="stat-card-sparkline"></div>
                    <div class="stat-card-pills">
                        <span class="stat-card-pill-item orange">{{ __('admin.menu.services') }}: {{ $srvCount }}</span>
                        <span class="stat-card-pill-item orange">{{ __('admin.menu.publications') }}: {{ $pubCount }}</span>
                    </div>
                </div>
            </div>

            {{-- Card 5: Payments --}}
            <div class="stat-card-premium">
                <div>
                    <div class="stat-card-header">
                        <div class="stat-card-icon-box grey">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                        <span class="stat-growth-badge neutral">-</span>
                    </div>
                    <div class="stat-card-value">{{ number_format($payTotal) }}</div>
                    <div class="stat-card-title">{{ __('admin.dash.payments') }}</div>
                </div>
                <div>
                    <div id="sparkline_payments" class="stat-card-sparkline"></div>
                    <div class="stat-card-pills">
                        <span class="stat-card-pill-item grey">{{ __('admin.status.completed') }}: {{ $payCompleted }}</span>
                        <span class="stat-card-pill-item grey">{{ __('admin.status.pending') }}: {{ $payPending }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Middle Grid: Chat Overview, Messages Bar, Quick Links --}}
        <div class="row g-3">
            {{-- Chat Overview (Donut Chart) --}}
            <div class="col-lg-4">
                <div class="dash-premium-panel">
                    <div class="panel-premium-header">
                        <div>
                            <h5>{{ __('admin.dash.chat_overview') }}</h5>
                            <p>{{ __('admin.dash.total_chats_count', ['count' => $chatsTotal]) }}</p>
                        </div>
                    </div>
                    <div class="panel-premium-body">
                        <div id="premium_chat_donut"></div>
                        <div class="text-center mt-3">
                            <span class="badge-excellent">{{ __('admin.dash.excellent') }}</span>
                        </div>
                        <div class="chat-donut-footer">
                            <span><span class="chat-indicator-dot active"></span>{{ __('admin.status.active') }}: {{ $chatsActive }}</span>
                            <span><span class="chat-indicator-dot inactive"></span>{{ __('admin.status.inactive') }}: {{ $chatsInactive }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages (Last 7 Days Bar Chart) --}}
            <div class="col-lg-4">
                <div class="dash-premium-panel">
                    <div class="panel-premium-header">
                        <div>
                            <h5>{{ __('admin.dash.messages') }}</h5>
                            <p>{{ __('admin.dash.last_7_days') }}</p>
                        </div>
                        <span class="fw-bold fs-18 color-primary">{{ $msg7Total }}</span>
                    </div>
                    <div class="panel-premium-body pt-2">
                        <div id="premium_messages_bar"></div>
                    </div>
                </div>
            </div>

            {{-- Quick Links (Fast Navigation) --}}
            <div class="col-lg-4">
                <div class="dash-premium-panel">
                    <div class="panel-premium-header">
                        <div>
                            <h5>{{ __('admin.dash.quick_links') }}</h5>
                            <p>{{ __('admin.dash.fast_navigation') }}</p>
                        </div>
                    </div>
                    <div class="panel-premium-body justify-content-start">
                        <div class="quick-links-vertical">
                            @php
                                $dotColors = [
                                    'work' => 'cyan',
                                    'school' => 'green',
                                    'forum' => 'purple',
                                    'assignment' => 'cyan',
                                    'handshake' => 'orange',
                                    'local_offer' => 'purple'
                                ];
                            @endphp
                            @foreach($quickLinks as $link)
                                <a href="{{ $link['route'] }}" class="quick-link-premium-item">
                                    <span class="quick-link-premium-title">
                                        <span class="quick-link-premium-dot {{ $dotColors[$link['icon']] ?? 'grey' }}"></span>
                                        {{ $link['label'] }}
                                    </span>
                                    <span class="material-symbols-outlined quick-link-premium-arrow">arrow_forward</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Row: Courses Overview, Opportunities Overview, Payments Overview --}}
        <div class="row g-3 bottom-charts-row">
            {{-- Courses Overview Status --}}
            <div class="col-lg-4">
                <div class="dash-premium-panel">
                    <div class="panel-premium-header">
                        <div>
                            <h5>{{ __('admin.dash.courses_overview') }}</h5>
                            <p>{{ __('admin.dash.active_vs_inactive_courses') }}</p>
                        </div>
                    </div>
                    <div class="panel-premium-body pt-2">
                        <div id="premium_courses_bar"></div>
                    </div>
                </div>
            </div>

            {{-- Opportunities Overview --}}
            <div class="col-lg-4">
                <div class="dash-premium-panel">
                    <div class="panel-premium-header">
                        <div>
                            <h5>{{ __('admin.dash.opportunities_overview') }}</h5>
                            <p>{{ __('admin.dash.active_vs_inactive_opportunities') }}</p>
                        </div>
                    </div>
                    <div class="panel-premium-body pt-2">
                        <div id="premium_opportunities_bar"></div>
                    </div>
                </div>
            </div>

            {{-- Payments Overview (Transaction Analytics) --}}
            <div class="col-lg-4">
                <div class="dash-premium-panel">
                    <div class="panel-premium-header">
                        <div>
                            <h5>{{ __('admin.dash.payments_overview') }}</h5>
                            <p>{{ __('admin.dash.transaction_analytics') }}</p>
                        </div>
                    </div>
                    <div class="panel-premium-body pt-2">
                        <div id="premium_payments_area"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            if (typeof ApexCharts === 'undefined') return;

            const isDark = document.body.getAttribute('data-theme') === 'dark';
            const chartFg = isDark ? '#94a3b8' : '#64748b';
            const chartGrid = isDark ? '#1e294b' : '#eef2f7';

            // Sparkline option constructor
            const sparklineOpts = (elId, seriesData, color) => {
                const el = document.getElementById(elId);
                if (!el) return;
                
                // Pad data if it's too short
                let finalData = seriesData || [0, 0, 0, 0, 0];
                if (finalData.length < 5) {
                    finalData = [...finalData, ...Array(5 - finalData.length).fill(finalData[finalData.length - 1] || 0)];
                }

                new ApexCharts(el, {
                    chart: {
                        type: 'area',
                        height: 40,
                        sparkline: { enabled: true },
                        animations: { enabled: true }
                    },
                    stroke: { curve: 'smooth', width: 2.2 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    colors: [color],
                    series: [{ data: finalData }],
                    tooltip: { enabled: false }
                }).render();
            };

            // Instantiate Sparklines
            const growthData = @json($growthChart ?? null);
            sparklineOpts('sparkline_users', (growthData ? growthData.users : [5, 12, 9, 14, 15, 17]), '#0cbab8');
            sparklineOpts('sparkline_courses', (growthData ? growthData.courses : [8, 10, 14, 12, 16, 18]), '#22c55e');
            sparklineOpts('sparkline_opportunities', (growthData ? growthData.opportunities : [10, 15, 12, 17, 19, 20]), '#8b5cf6');
            
            // Sparkline content curve ending at actual totalContent
            const totalContent = {{ $contentTotal }};
            sparklineOpts('sparkline_content', [
                Math.round(totalContent * 0.4),
                Math.round(totalContent * 0.6),
                Math.round(totalContent * 0.8),
                totalContent
            ], '#f97316');

            // Sparkline payments curve ending at actual payments count
            const totalPayments = {{ $payTotal }};
            sparklineOpts('sparkline_payments', [
                Math.round(totalPayments * 0.3),
                Math.round(totalPayments * 0.6),
                Math.round(totalPayments * 0.8),
                totalPayments
            ], '#64748b');

            // Chat Overview Circular Donut Progress
            const donutEl = document.querySelector('#premium_chat_donut');
            if (donutEl) {
                new ApexCharts(donutEl, {
                    chart: {
                        type: 'donut',
                        height: 220
                    },
                    labels: ['{{ __('admin.status.active') }}', '{{ __('admin.status.inactive') }}'],
                    series: [{{ $chatsActive }}, {{ $chatsInactive }}],
                    colors: ['#0cbab8', isDark ? '#1e294b' : '#e2e8f0'],
                    legend: { show: false },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '74%',
                                labels: {
                                    show: true,
                                    name: { show: false },
                                    value: {
                                        show: true,
                                        fontSize: '28px',
                                        fontWeight: '700',
                                        color: isDark ? '#f1f5f9' : '#0f172a',
                                        offsetY: 8,
                                        formatter: () => '{{ $chatsActivePercent }}%'
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { width: 0 }
                }).render();
            }

            // Messages Last 7 Days (Column Chart)
            const msg7 = @json($metrics['messages_last_7_days'] ?? null);
            const msgEl = document.querySelector('#premium_messages_bar');
            if (msgEl && msg7?.series?.length) {
                new ApexCharts(msgEl, {
                    chart: {
                        type: 'bar',
                        height: 180,
                        toolbar: { show: false }
                    },
                    colors: ['#0cbab8'],
                    series: [{ name: '{{ __('admin.dash.messages') }}', data: msg7.series }],
                    xaxis: {
                        categories: msg7.labels,
                        labels: { style: { colors: chartFg } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: { style: { colors: chartFg } }
                    },
                    grid: { borderColor: chartGrid },
                    plotOptions: {
                        bar: {
                            borderRadius: 5,
                            columnWidth: '40%'
                        }
                    },
                    dataLabels: { enabled: false }
                }).render();
            }

            // Bottom Courses Bar Chart (Active vs Inactive)
            const coursesEl = document.querySelector('#premium_courses_bar');
            if (coursesEl) {
                new ApexCharts(coursesEl, {
                    chart: {
                        type: 'bar',
                        height: 200,
                        toolbar: { show: false }
                    },
                    colors: ['#0cbab8', '#e2e8f0'],
                    series: [{
                        name: '{{ __('admin.dash.courses') }}',
                        data: [{{ $cActive }}, {{ $cInactive }}]
                    }],
                    xaxis: {
                        categories: ['{{ __('admin.status.active') }}', '{{ __('admin.status.inactive') }}'],
                        labels: { style: { colors: chartFg } },
                        axisBorder: { show: false }
                    },
                    yaxis: {
                        labels: { style: { colors: chartFg } }
                    },
                    grid: { borderColor: chartGrid },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '35%',
                            distributed: true
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: { show: false }
                }).render();
            }

            // Bottom Opportunities Bar Chart (Active vs Inactive)
            const oppsEl = document.querySelector('#premium_opportunities_bar');
            if (oppsEl) {
                new ApexCharts(oppsEl, {
                    chart: {
                        type: 'bar',
                        height: 200,
                        toolbar: { show: false }
                    },
                    colors: ['#8b5cf6', '#e2e8f0'],
                    series: [{
                        name: '{{ __('admin.dash.opportunities_label') }}',
                        data: [{{ $oActive }}, {{ $oInactive }}]
                    }],
                    xaxis: {
                        categories: ['{{ __('admin.status.open') }}', '{{ __('admin.status.closed') }}'],
                        labels: { style: { colors: chartFg } },
                        axisBorder: { show: false }
                    },
                    yaxis: {
                        labels: { style: { colors: chartFg } }
                    },
                    grid: { borderColor: chartGrid },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '35%',
                            distributed: true
                        }
                    },
                    dataLabels: { enabled: false },
                    legend: { show: false }
                }).render();
            }

            // Bottom Payments Area Chart
            const payStatus = @json($statusBars['payments'] ?? null);
            const payEl = document.querySelector('#premium_payments_area');
            if (payEl && payStatus?.labels?.length) {
                new ApexCharts(payEl, {
                    chart: {
                        type: 'area',
                        height: 200,
                        toolbar: { show: false }
                    },
                    colors: ['#6366f1'],
                    stroke: { curve: 'smooth', width: 2 },
                    fill: {
                        type: 'gradient',
                        gradient: { opacityFrom: 0.35, opacityTo: 0.05 }
                    },
                    series: [{
                        name: '{{ __('admin.dash.payments') }}',
                        data: payStatus.series
                    }],
                    xaxis: {
                        categories: payStatus.labels,
                        labels: { style: { colors: chartFg } }
                    },
                    yaxis: {
                        labels: { style: { colors: chartFg } }
                    },
                    grid: { borderColor: chartGrid },
                    dataLabels: { enabled: false }
                }).render();
            }
        })();
    </script>
@endpush
