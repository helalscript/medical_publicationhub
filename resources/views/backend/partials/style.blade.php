<!-- All CSS files -->
<link rel="stylesheet" href="{{ asset('backend/admin/assets/css/sidebar-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/sweetalert.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/google-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/fullcalendar.main.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/jsvectormap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/lightpick.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap">

    
<link href="{{ asset('vendor/flasher/flasher.min.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.0/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .fl-wrapper {
        z-index: 999999 !important;
    }
</style>

<style>
    /* Premium Theme CSS Variables */
    :root {
        --dash-bg: #f8fafc;
        --dash-surface: #ffffff;
        --dash-border: #e2e8f0;
        --dash-text: #0f172a;
        --dash-muted: #64748b;
        --dash-faint: #94a3b8;
        --dash-primary: #0cbab8;
        --dash-primary-hover: #0a9d9b;
        --dash-primary-rgb: 12, 186, 184;
        --dash-secondary: #4f46e5;
        --dash-shadow: 0 10px 30px rgba(15, 23, 42, 0.03);
        --dash-shadow-sm: 0 4px 12px rgba(15, 23, 42, 0.02);
        --sidebar-bg: #ffffff;
        --sidebar-border: #ecf0ff;
        --sidebar-text: #475569;
        --sidebar-active-bg: #0cbab8;
        --sidebar-active-text: #ffffff;
    }

    [data-theme="dark"] {
        --dash-bg: #080d19;
        --dash-surface: #0f172a;
        --dash-border: #1e293b;
        --dash-text: #f1f5f9;
        --dash-muted: #94a3b8;
        --dash-faint: #475569;
        --dash-primary: #0cbab8;
        --dash-primary-hover: #14b8a6;
        --dash-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        --dash-shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.2);
        --sidebar-bg: #0f172a;
        --sidebar-border: #1e293b;
        --sidebar-text: #94a3b8;
        --sidebar-active-bg: #0cbab8;
        --sidebar-active-text: #ffffff;
    }

    /* Base Body & Layout Customization */
    body {
        background-color: var(--dash-bg) !important;
        color: var(--dash-text) !important;
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Main Content Area adjustments */
    .main-content {
        background-color: var(--dash-bg) !important;
        transition: all 0.3s ease;
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
    }

    /* Header styling */
    .header-area {
        background-color: var(--dash-surface) !important;
        border: 1px solid var(--dash-border) !important;
        box-shadow: var(--dash-shadow-sm) !important;
        border-radius: 16px !important;
        padding: 1rem 1.5rem !important;
        margin-bottom: 1.5rem !important;
        transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .header-area .text-body {
        color: var(--dash-text) !important;
    }

    /* Notifications & Dropdowns */
    .dropdown-menu {
        background-color: var(--dash-surface) !important;
        border: 1px solid var(--dash-border) !important;
        box-shadow: var(--dash-shadow) !important;
        border-radius: 12px !important;
    }

    .dropdown-item {
        color: var(--dash-text) !important;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: rgba(12, 186, 184, 0.08) !important;
        color: var(--dash-primary) !important;
    }

    /* Language Switcher */
    .lang-switcher .dropdown-item.active {
        background-color: rgba(12, 186, 184, 0.12) !important;
        color: var(--dash-primary) !important;
    }

    /* Sidebar Container styling */
    .sidebar-area {
        background-color: var(--sidebar-bg) !important;
        border-right: 1px solid var(--sidebar-border) !important;
        box-shadow: var(--dash-shadow-sm) !important;
        display: flex;
        flex-direction: column;
        padding-bottom: 0;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1040;
    }

    [data-theme="dark"] .sidebar-area {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
    }

    .sidebar-area .sidebar-brand.logo {
        flex-shrink: 0;
        min-height: 80px;
        height: auto;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid var(--sidebar-border);
        background: transparent !important;
    }

    .sidebar-brand-link {
        flex: 1;
        min-width: 0;
        justify-content: flex-start !important;
        background: transparent !important;
    }

    .sidebar-brand-logo {
        display: block;
        max-height: 40px;
        width: auto;
        max-width: 170px;
        object-fit: contain;
    }

    .sidebar-user {
        flex-shrink: 0;
        padding: 18px 24px;
        border-bottom: 1px solid var(--sidebar-border) !important;
        background: transparent !important;
    }

    .sidebar-user-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid var(--dash-border);
        box-shadow: var(--dash-shadow-sm);
    }

    .sidebar-user-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--dash-text) !important;
        line-height: 1.3;
    }

    .sidebar-user-role {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        color: var(--dash-primary);
        margin-top: 2px;
    }

    /* Sidebar Menu Styling */
    .sidebar-area .layout-menu {
        flex: 1 1 auto;
        padding: 20px 16px;
    }

    .menu-vertical .menu-item {
        margin-bottom: 6px;
    }

    .menu-vertical .menu-item .menu-link {
        border-radius: 12px;
        padding: 11px 16px;
        background: transparent !important;
        color: var(--sidebar-text) !important;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .menu-vertical .menu-item .menu-link .menu-icon {
        color: var(--dash-muted) !important;
        font-size: 20px;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .menu-vertical .menu-item .menu-link:hover {
        background-color: rgba(12, 186, 184, 0.08) !important;
        color: var(--dash-primary) !important;
    }

    .menu-vertical .menu-item .menu-link:hover .menu-icon {
        color: var(--dash-primary) !important;
        transform: scale(1.05);
    }

    /* Active Dashboard Menu Item Pill Style (teal background) */
    .menu-vertical .menu-item > .menu-link.active:not(.menu-toggle) {
        background: var(--sidebar-active-bg) !important;
        color: var(--sidebar-active-text) !important;
        box-shadow: 0 4px 12px rgba(12, 186, 184, 0.3) !important;
    }

    .menu-vertical .menu-item > .menu-link.active:not(.menu-toggle) .menu-icon {
        color: var(--sidebar-active-text) !important;
    }

    /* Dropdown Toggle items */
    .menu-vertical .menu-item.open > .menu-link.menu-toggle {
        background: rgba(12, 186, 184, 0.04) !important;
        color: var(--dash-text) !important;
    }

    .menu-vertical .menu-item.open > .menu-link.menu-toggle .menu-icon {
        color: var(--dash-primary) !important;
    }

    .menu-vertical .menu-sub {
        padding-left: 14px;
        margin-top: 4px;
        border-left: 1px dashed var(--dash-border);
    }

    .menu-vertical .menu-sub .menu-item .menu-link {
        padding: 8px 16px;
        font-size: 0.8125rem;
    }

    .menu-vertical .menu-sub .menu-link.active {
        background-color: rgba(12, 186, 184, 0.08) !important;
        color: var(--dash-primary) !important;
        font-weight: 600;
    }

    .menu-vertical .menu-title {
        margin-top: 18px;
        margin-bottom: 8px;
        padding-left: 12px;
    }

    .menu-vertical .menu-title .menu-title-text {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: var(--dash-faint) !important;
        text-transform: uppercase;
    }

    /* Collapsed Sidebar overrides */
    [sidebar-data-theme=sidebar-hide] .sidebar-area {
        width: 80px !important;
    }

    [sidebar-data-theme=sidebar-hide] .sidebar-area .sidebar-brand.logo {
        min-height: 60px;
        height: 60px;
        padding: 10px 14px;
    }

    [sidebar-data-theme=sidebar-hide] .sidebar-brand-logo {
        max-height: 28px;
    }

    [sidebar-data-theme=sidebar-hide] .sidebar-area .sidebar-user {
        padding: 12px;
        text-align: center;
    }

    [sidebar-data-theme=sidebar-hide] .sidebar-area .sidebar-user .admin-info {
        display: none;
    }

    [sidebar-data-theme=sidebar-hide] .sidebar-user-avatar {
        width: 36px;
        height: 36px;
        margin: 0 auto;
    }

    [sidebar-data-theme=sidebar-hide] .menu-vertical .menu-title {
        display: none;
    }

    /* Arabic / RTL overrides */
    html[dir="rtl"] body,
    html.rtl body {
        font-family: "IBM Plex Sans Arabic", "Segoe UI", Tahoma, sans-serif !important;
    }

    html[dir="rtl"] .sidebar-area {
        border-right: 0 !important;
        border-left: 1px solid var(--sidebar-border) !important;
    }

    html[dir="rtl"] .menu-vertical .menu-sub {
        padding-left: 0;
        padding-right: 14px;
        border-left: 0;
        border-right: 1px dashed var(--dash-border);
    }

    html[dir="rtl"] .menu-vertical .menu-item .menu-link .menu-icon {
        margin-right: 0;
        margin-left: 8px;
    }

    html[dir="rtl"] .menu-vertical .menu-item .menu-toggle::after {
        transform: rotate(180deg);
    }

    html[dir="rtl"] .menu-vertical .menu-item.open > .menu-link.menu-toggle::after {
        transform: rotate(90deg);
    }

    html[dir="rtl"] .dash-hero-pills {
        justify-content: flex-start;
    }
</style>

@stack('styles')
