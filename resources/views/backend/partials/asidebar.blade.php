{{-- @php
$systemSetting = App\Models\SystemSetting::first();
@endphp --}}
<!-- Start Sidebar Area -->
<div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative">
        <a href="{{ route('admin.dashboard') }}" class="d-block text-decoration-none position-relative">
            <img src="{{ asset($systemSetting->logo ?? 'backend/admin/assets/logo.png') }}" alt="logo-icon"
                style="height: 100px">
        </a>
        <button
            class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
            id="sidebar-burger-menu">
            <i data-feather="x"></i>
        </button>
    </div>

    <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar
        style="height: calc(100vh - 100px)">
        <ul class="menu-inner">
            @php
                $canMainSection =
                    auth()->user()?->can('admin.dashboard') ||
                    auth()->user()?->can('admin.research_ideas.index') ||
                    auth()->user()?->can('admin.publications.index') ||
                    auth()->user()?->can('admin.partnership_requests.index') ||
                    auth()->user()?->can('admin.services.index') ||
                    auth()->user()?->can('admin.opportunities.index') ||
                    auth()->user()?->can('admin.opportunity_applications.index') ||
                    auth()->user()?->can('admin.opportunity_chats.index') ||
                    auth()->user()?->can('admin.courses.index') ||
                    auth()->user()?->can('admin.modules.index') ||
                    auth()->user()?->can('admin.lessons.index') ||
                    auth()->user()?->can('admin.course_access.index') ||
                    auth()->user()?->can('admin.payments.index');
            @endphp
            @if($canMainSection)
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">MAIN</span>
                </li>
            @endif
            <!-- Dashboard Menu Item -->
            @can('admin.dashboard')
                <li class="menu-item open">
                    <a href="{{ route('admin.dashboard') }}"
                        class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
            @endcan

            @can('admin.research_ideas.index')
                <li class="menu-item open">
                    <a href="{{ route('admin.research_ideas.index') }}"
                        class="menu-link {{ request()->routeIs('admin.research_ideas.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">biotech</span>
                        <span class="title">Research Ideas</span>
                    </a>
                </li>
            @endcan

            @can('admin.publications.index')
                <li class="menu-item open">
                    <a href="{{ route('admin.publications.index') }}"
                        class="menu-link {{ request()->routeIs('admin.publications.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">library_books</span>
                        <span class="title">Publications</span>
                    </a>
                </li>
            @endcan

            @can('admin.partnership_requests.index')
                <li class="menu-item open">
                    <a href="{{ route('admin.partnership_requests.index') }}"
                        class="menu-link {{ request()->routeIs('admin.partnership_requests.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">handshake</span>
                        <span class="title">Partnership Requests</span>
                    </a>
                </li>
            @endcan

            @can('admin.services.index')
                <li class="menu-item open">
                    <a href="{{ route('admin.services.index') }}"
                        class="menu-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">design_services</span>
                        <span class="title">Services</span>
                    </a>
                </li>
            @endcan

            @php
                $canOpportunitiesMenu =
                    auth()->user()?->can('admin.opportunities.index') ||
                    auth()->user()?->can('admin.opportunity_applications.index') ||
                    auth()->user()?->can('admin.opportunity_chats.index');
            @endphp
            @if($canOpportunitiesMenu)
                <li class="menu-item {{ request()->routeIs('admin.opportunities.*', 'admin.opportunity_applications.*', 'admin.opportunity_chats.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">work</span>
                        <span class="title">Opportunities</span>
                    </a>
                    <ul class="menu-sub">
                        @can('admin.opportunities.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.opportunities.index') }}"
                                    class="menu-link {{ request()->routeIs('admin.opportunities.*') ? 'active' : '' }}">
                                    Opportunity List
                                </a>
                            </li>
                        @endcan
                        @can('admin.opportunity_applications.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.opportunity_applications.index') }}"
                                    class="menu-link {{ request()->routeIs('admin.opportunity_applications.*') ? 'active' : '' }}">
                                    Applications
                                </a>
                            </li>
                        @endcan
                        @can('admin.opportunity_chats.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.opportunity_chats.index') }}"
                                    class="menu-link {{ request()->routeIs('admin.opportunity_chats.*') ? 'active' : '' }}">
                                    Chats
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @php
                $canCoursesMenu =
                    auth()->user()?->can('admin.courses.index') ||
                    auth()->user()?->can('admin.modules.index') ||
                    auth()->user()?->can('admin.lessons.index') ||
                    auth()->user()?->can('admin.course_access.index') ||
                    auth()->user()?->can('admin.payments.index');
            @endphp
            @if($canCoursesMenu)
                <li class="menu-item {{ request()->routeIs('admin.courses.*', 'admin.modules.*', 'admin.lessons.*', 'admin.course_access.*', 'admin.payments.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">school</span>
                        <span class="title">Courses</span>
                    </a>
                    <ul class="menu-sub">
                        @can('admin.courses.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.courses.index') }}"
                                   class="menu-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                                    Course List
                                </a>
                            </li>
                        @endcan
                        @can('admin.modules.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.modules.index') }}"
                                   class="menu-link {{ request()->routeIs('admin.modules.*') ? 'active' : '' }}">
                                    Modules
                                </a>
                            </li>
                        @endcan
                        @can('admin.lessons.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.lessons.index') }}"
                                   class="menu-link {{ request()->routeIs('admin.lessons.*') ? 'active' : '' }}">
                                    Lessons
                                </a>
                            </li>
                        @endcan
                        @can('admin.course_access.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.course_access.index') }}"
                                   class="menu-link {{ request()->routeIs('admin.course_access.*') ? 'active' : '' }}">
                                    Course Access
                                </a>
                            </li>
                        @endcan
                        @can('admin.payments.index')
                            <li class="menu-item">
                                <a href="{{ route('admin.payments.index') }}"
                                   class="menu-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                    Payments
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @php
                $canAppsSection =
                    auth()->user()?->can('user_management.mentor.index') ||
                    auth()->user()?->can('user_management.researcher.index') ||
                    auth()->user()?->can('user_management.coordinator.index') ||
                    auth()->user()?->can('admin_contact_us.index') ||
                    auth()->user()?->can('access_control.roles.view') ||
                    auth()->user()?->can('access_control.permissions.view') ||
                    auth()->user()?->can('faqs.index') ||
                    auth()->user()?->can('faq_categories.index') ||
                    auth()->user()?->can('social_link.index');
            @endphp
            @if($canAppsSection)
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">APPS</span>
                </li>
            @endif

            <!-- Users Menu Item -->
            @php
                $canUsersMenu =
                    auth()->user()?->can('user_management.mentor.index') ||
                    auth()->user()?->can('user_management.researcher.index') ||
                    auth()->user()?->can('user_management.coordinator.index');
            @endphp
            @if($canUsersMenu)
                <li class="menu-item {{ request()->routeIs('user_management.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">group</span>
                        <span class="title">Users</span>
                    </a>
                    <ul class="menu-sub">
                        @can('user_management.mentor.index')
                            <li class="menu-item">
                                <a href="{{ route('user_management.mentor.index') }}"
                                    class="menu-link {{ request()->routeIs('user_management.mentor.*') ? 'active' : '' }}">
                                    Mentor
                                </a>
                            </li>
                        @endcan
                        @can('user_management.researcher.index')
                            <li class="menu-item">
                                <a href="{{ route('user_management.researcher.index') }}"
                                    class="menu-link {{ request()->routeIs('user_management.researcher.*') ? 'active' : '' }}">
                                    Researcher
                                </a>
                            </li>
                        @endcan
                        @can('user_management.coordinator.index')
                            <li class="menu-item">
                                <a href="{{ route('user_management.coordinator.index') }}"
                                    class="menu-link {{ request()->routeIs('user_management.coordinator.*') ? 'active' : '' }}">
                                    Coordinator
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            
            @can('admin_contact_us.index')
                <li class="menu-item open">
                    <a href="{{ route('admin_contact_us.index') }}"
                        class="menu-link {{ request()->routeIs('admin_contact_us.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">content_paste</span>
                        <span class="title">Contact Message</span>
                    </a>
                </li>
            @endcan

            @can('access_control.roles.view')
                <li class="menu-item {{ request()->routeIs('admin.access_control.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">admin_panel_settings</span>
                        <span class="title">Access Control</span>
                    </a>
                    <ul class="menu-sub">
                        @can('access_control.roles.view')
                            <li class="menu-item">
                                <a href="{{ route('admin.access_control.roles.index') }}"
                                    class="menu-link {{ request()->routeIs('admin.access_control.roles.*') ? 'active' : '' }}">
                                    Roles
                                </a>
                            </li>
                        @endcan
                        @can('access_control.permissions.view')
                            <li class="menu-item">
                                <a href="{{ route('admin.access_control.permissions.index') }}"
                                    class="menu-link {{ request()->routeIs('admin.access_control.permissions.*') ? 'active' : '' }}">
                                    Permissions
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            

            
            {{-- <li class="menu-item open">
                <a href="{{ route('admin.newsletters.index') }}"
                    class="menu-link {{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined menu-icon">mail</span>
                    <span class="title">Newsletter</span>
                </a>
            </li> --}}
            {{-- <li class="menu-item open">
                <a href="{{ route('blogs.index') }}"
                    class="menu-link {{ request()->routeIs('blogs.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined menu-icon">book</span>
                    <span class="title">Blogs</span>
                </a>
            </li>--}}
            @php
                $canFaqMenu =
                    auth()->user()?->can('faqs.index') ||
                    auth()->user()?->can('faq_categories.index');
            @endphp
            @if($canFaqMenu)
                <li class="menu-item {{ request()->routeIs('faqs.*', 'faq_categories.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">help_outline</span>
                        <span class="title">FAQ's</span>
                    </a>
                    <ul class="menu-sub">
                        @can('faqs.index')
                            <li class="menu-item">
                                <a href="{{ route('faqs.index') }}"
                                    class="menu-link {{ request()->routeIs('faqs.*') ? 'active' : '' }}">
                                    FAQ List
                                </a>
                            </li>
                        @endcan
                        @can('faq_categories.index')
                            <li class="menu-item">
                                <a href="{{ route('faq_categories.index') }}"
                                    class="menu-link {{ request()->routeIs('faq_categories.*') ? 'active' : '' }}">
                                    FAQ Categories
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @can('social_link.index')
                <li class="menu-item open">
                    <a href="{{ route('social_link.index') }}"
                        class="menu-link {{ request()->routeIs('social_link.*') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">link</span>
                        <span class="title">Social Link</span>
                    </a>
                </li>
            @endcan
            @php
                $canOthersSection =
                    auth()->user()?->can('profile_settings.index') ||
                    auth()->user()?->can('profile_settings.password_change') ||
                    auth()->user()?->can('system_settings.index') ||
                    auth()->user()?->can('dynamic_page.index') ||
                    auth()->user()?->can('dynamic_page.create');
            @endphp
            @if($canOthersSection)
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">OTHERS</span>
                </li>
            @endif

            <!-- Settings Menu Item -->
            @php
                $canSettingsMenu =
                    auth()->user()?->can('profile_settings.index') ||
                    auth()->user()?->can('profile_settings.password_change') ||
                    auth()->user()?->can('system_settings.index');
            @endphp
            @if($canSettingsMenu)
                <li class="menu-item {{ request()->routeIs('profile_settings.*', 'system_settings.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">settings</span>
                        <span class="title">Settings</span>
                    </a>
                    <ul class="menu-sub">
                        @can('profile_settings.index')
                            <li class="menu-item ">
                                <a href="{{ route('profile_settings.index') }}"
                                    class="menu-link {{ request()->routeIs('profile_settings.index') ? 'active' : '' }} ">
                                    Profile Settings
                                </a>
                            </li>
                        @endcan
                        @can('profile_settings.password_change')
                            <li class="menu-item">
                                <a href="{{ route('profile_settings.password_change') }}"
                                    class="menu-link {{ request()->routeIs('profile_settings.password_change') ? 'active' : '' }}">
                                    Change Password
                                </a>
                            </li>
                        @endcan
                        @can('system_settings.index')
                            <li class="menu-item">
                                <a href="{{ route('system_settings.index') }}"
                                    class="menu-link {{ request()->routeIs('system_settings.*') ? 'active' : '' }}">
                                    System Configaration
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            <!-- dynamic page Menu Item -->
            @php
                $canDynamicPageMenu =
                    auth()->user()?->can('dynamic_page.index') ||
                    auth()->user()?->can('dynamic_page.create');
            @endphp
            @if($canDynamicPageMenu)
                <li class="menu-item {{ request()->routeIs('dynamic_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">clarify</span>
                        <span class="title">Dynamic page</span>
                    </a>
                    <ul class="menu-sub">
                        @can('dynamic_page.index')
                            <li class="menu-item">
                                <a href="{{ route('dynamic_page.index') }}"
                                    class="menu-link {{ request()->routeIs('dynamic_page.index') ? 'active' : '' }}">
                                    Pages
                                </a>
                            </li>
                        @endcan
                        @can('dynamic_page.create')
                            <li class="menu-item">
                                <a href="{{ route('dynamic_page.create') }}"
                                    class="menu-link {{ request()->routeIs('dynamic_page.create') ? 'active' : '' }}">
                                    Add New
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            @php
                $canCmsSection =
                    auth()->user()?->can('cms.home_page.hero_section.create') ||
                    auth()->user()?->can('cms.home_page.counter_section.index') ||
                    auth()->user()?->can('cms.home_page.why_researchers_choose_section.index') ||
                    auth()->user()?->can('cms.home_page.featured_research_opportunities_section.create') ||
                    auth()->user()?->can('cms.home_page.meta_academy_section.create') ||
                    auth()->user()?->can('cms.home_page.research_journey_section.create') ||
                    auth()->user()?->can('cms.home_page.faq_section.create') ||
                    auth()->user()?->can('cms.home_page.review_section.index') ||
                    auth()->user()?->can('cms.faq_page.hero_section.create') ||
                    auth()->user()?->can('cms.aboutus_page.hero_section.create') ||
                    auth()->user()?->can('cms.aboutus_page.our_mission_section.create') ||
                    auth()->user()?->can('cms.aboutus_page.our_mission_items.index') ||
                    auth()->user()?->can('cms.research_page.research_section.create') ||
                    auth()->user()?->can('cms.service_page.service_section.create') ||
                    auth()->user()?->can('cms.meta_academy_page.hero_section.create') ||
                    auth()->user()?->can('cms.meta_academy_page.counter_section.index') ||
                    auth()->user()?->can('cms.partnership_page.hero_section.create') ||
                    auth()->user()?->can('cms.partnership_page.our_mission_section.create') ||
                    auth()->user()?->can('cms.partnership_page.about_us_section.create') ||
                    auth()->user()?->can('cms.partnership_page.partnership_value_section.index') ||
                    auth()->user()?->can('cms.partnership_page.core_research_section.index') ||
                    auth()->user()?->can('cms.partnership_page.systematic_review_section.index') ||
                    auth()->user()?->can('cms.partnership_page.manuscript_section.create') ||
                    auth()->user()?->can('cms.partnership_page.partnership_model_section.index') ||
                    auth()->user()?->can('cms.partnership_page.reviewer_comment_section.create') ||
                    auth()->user()?->can('cms.partnership_page.journal_section.create') ||
                    auth()->user()?->can('cms.partnership_page.research_opportunitie_section.index') ||
                    auth()->user()?->can('cms.partnership_page.why_partner_section.create') ||
                    auth()->user()?->can('cms.partnership_page.partner_model_section.index');
            @endphp
            @if($canCmsSection)
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">CMS</span>
                </li>
            @endif

            <!-- CMS Home Page Menu Item -->
            @php
                $canCmsHomePageMenu =
                    auth()->user()?->can('cms.home_page.hero_section.create') ||
                    auth()->user()?->can('cms.home_page.counter_section.index') ||
                    auth()->user()?->can('cms.home_page.why_researchers_choose_section.index') ||
                    auth()->user()?->can('cms.home_page.featured_research_opportunities_section.create') ||
                    auth()->user()?->can('cms.home_page.meta_academy_section.create') ||
                    auth()->user()?->can('cms.home_page.research_journey_section.create') ||
                    auth()->user()?->can('cms.home_page.faq_section.create') ||
                    auth()->user()?->can('cms.home_page.review_section.index');
                    
            @endphp
            @if($canCmsHomePageMenu)
                <li class="menu-item {{ request()->routeIs('cms.home_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">home</span>
                        <span class="title">Home Page</span>
                    </a>
                    <ul class="menu-sub">
                        @can('cms.home_page.hero_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.hero_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.hero_section.*') ? 'active' : '' }}">
                                    Hero Section
                                </a>
                            </li>
                        @endcan
                        @can('cms.home_page.counter_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.counter_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.counter_section.*') ? 'active' : '' }}">
                                    Counter Section
                                </a>
                            </li>
                        @endcan
                        @can('cms.home_page.why_researchers_choose_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.why_researchers_choose_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.why_researchers_choose_section.*') ? 'active' : '' }}">
                                    Why Researchers Choose
                                </a>
                            </li>
                        @endcan
                        @can('cms.home_page.featured_research_opportunities_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.featured_research_opportunities_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.featured_research_opportunities_section.*') ? 'active' : '' }}">
                                    Featured Research Opportunities
                                </a>
                            </li>
                        @endcan
                        @can('cms.home_page.meta_academy_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.meta_academy_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.meta_academy_section.*') ? 'active' : '' }}">
                                    Meta Academy
                                </a>
                            </li>
                        @endcan
                        @can('cms.home_page.research_journey_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.research_journey_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.research_journey_section.*') ? 'active' : '' }}">
                                    Research Journey
                                </a>
                            </li>
                        @endcan
                        @can('cms.home_page.faq_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.faq_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.faq_section.*') ? 'active' : '' }}">
                                    FAQ Section
                                </a>
                            </li>
                        @endcan
                        @can('cms.home_page.review_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.home_page.review_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.home_page.review_section.*') ? 'active' : '' }}">
                                    Review Section
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
           



            <!-- CMS FAQ Page Menu Item -->
            @can('cms.faq_page.hero_section.create')
                <li class="menu-item {{ request()->routeIs('cms.faq_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">help_outline</span>
                        <span class="title">FAQ Page</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('cms.faq_page.hero_section.create') }}"
                                class="menu-link {{ request()->routeIs('cms.faq_page.hero_section.*') ? 'active' : '' }}">
                                Hero Section
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- CMS Aboutus Page Menu Item -->
            @php
                $canAboutusMenu =
                    auth()->user()?->can('cms.aboutus_page.hero_section.create') ||
                    auth()->user()?->can('cms.aboutus_page.our_mission_section.create') ||
                    auth()->user()?->can('cms.aboutus_page.our_mission_items.index');
            @endphp
            @if($canAboutusMenu)
                <li class="menu-item {{ request()->routeIs('cms.aboutus_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">info</span>
                        <span class="title">Aboutus Page</span>
                    </a>
                    <ul class="menu-sub">
                        @can('cms.aboutus_page.hero_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.aboutus_page.hero_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.aboutus_page.hero_section.*') ? 'active' : '' }}">
                                    Hero Section
                                </a>
                            </li>
                        @endcan
                        @can('cms.aboutus_page.our_mission_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.aboutus_page.our_mission_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.aboutus_page.our_mission_section.*') ? 'active' : '' }}">
                                    Our Mission Section
                                </a>
                            </li>
                        @endcan
                        @can('cms.aboutus_page.our_mission_items.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.aboutus_page.our_mission_items.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.aboutus_page.our_mission_items.*') ? 'active' : '' }}">
                                    Mission Items
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            <!-- CMS Research Page Menu Item -->
            @can('cms.research_page.research_section.create')
                <li class="menu-item {{ request()->routeIs('cms.research_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">science</span>
                        <span class="title">Research Page</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('cms.research_page.research_section.create') }}"
                                class="menu-link {{ request()->routeIs('cms.research_page.research_section.*') ? 'active' : '' }}">
                                Research Section
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- CMS Service Page Menu Item -->
            @can('cms.service_page.service_section.create')
                <li class="menu-item {{ request()->routeIs('cms.service_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">handyman</span>
                        <span class="title">Service Page</span>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('cms.service_page.service_section.create') }}"
                                class="menu-link {{ request()->routeIs('cms.service_page.service_section.*') ? 'active' : '' }}">
                                Service Section
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <!-- CMS Meta Academy Page Menu Item -->
            @php
                $canMetaAcademyMenu =
                    auth()->user()?->can('cms.meta_academy_page.hero_section.create') ||
                    auth()->user()?->can('cms.meta_academy_page.counter_section.index');
            @endphp
            @if($canMetaAcademyMenu)
                <li class="menu-item {{ request()->routeIs('cms.meta_academy_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">school</span>
                        <span class="title">Meta Academy Page</span>
                    </a>
                    <ul class="menu-sub">
                        @can('cms.meta_academy_page.hero_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.meta_academy_page.hero_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.meta_academy_page.hero_section.*') ? 'active' : '' }}">
                                    Hero Section
                                </a>
                            </li>
                        @endcan
                        @can('cms.meta_academy_page.counter_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.meta_academy_page.counter_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.meta_academy_page.counter_section.*') ? 'active' : '' }}">
                                    Counter Section
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif

            <!-- CMS Partnership Page Menu Item -->
            @php
                $canPartnershipCmsMenu =
                    auth()->user()?->can('cms.partnership_page.hero_section.create') ||
                    auth()->user()?->can('cms.partnership_page.our_mission_section.create') ||
                    auth()->user()?->can('cms.partnership_page.about_us_section.create') ||
                    auth()->user()?->can('cms.partnership_page.partnership_value_section.index') ||
                    auth()->user()?->can('cms.partnership_page.core_research_section.index') ||
                    auth()->user()?->can('cms.partnership_page.systematic_review_section.index') ||
                    auth()->user()?->can('cms.partnership_page.manuscript_section.create') ||
                    auth()->user()?->can('cms.partnership_page.partnership_model_section.index') ||
                    auth()->user()?->can('cms.partnership_page.reviewer_comment_section.create') ||
                    auth()->user()?->can('cms.partnership_page.journal_section.create') ||
                    auth()->user()?->can('cms.partnership_page.research_opportunitie_section.index') ||
                    auth()->user()?->can('cms.partnership_page.why_partner_section.create') ||
                    auth()->user()?->can('cms.partnership_page.partner_model_section.index');
            @endphp
            @if($canPartnershipCmsMenu)
                <li class="menu-item {{ request()->routeIs('cms.partnership_page.*') ? 'open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle active">
                        <span class="material-symbols-outlined menu-icon">handshake</span>
                        <span class="title">Partnership Page</span>
                    </a>
                    <ul class="menu-sub">
                        @can('cms.partnership_page.hero_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.hero_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.hero_section.*') ? 'active' : '' }}">Hero</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.our_mission_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.our_mission_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.our_mission_section.*') ? 'active' : '' }}">Our Mission</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.about_us_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.about_us_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.about_us_section.*') ? 'active' : '' }}">About Us</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.partnership_value_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.partnership_value_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.partnership_value_section.*') ? 'active' : '' }}">Partnership Value</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.core_research_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.core_research_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.core_research_section.*') ? 'active' : '' }}">Core Research</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.systematic_review_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.systematic_review_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.systematic_review_section.*') ? 'active' : '' }}">Systematic Review</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.manuscript_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.manuscript_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.manuscript_section.*') ? 'active' : '' }}">Manuscript</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.partnership_model_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.partnership_model_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.partnership_model_section.*') ? 'active' : '' }}">Partnership Model</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.reviewer_comment_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.reviewer_comment_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.reviewer_comment_section.*') ? 'active' : '' }}">Reviewer Comment</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.journal_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.journal_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.journal_section.*') ? 'active' : '' }}">Journal</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.research_opportunitie_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.research_opportunitie_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.research_opportunitie_section.*') ? 'active' : '' }}">Research Opportunities</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.why_partner_section.create')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.why_partner_section.create') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.why_partner_section.*') ? 'active' : '' }}">Why Partner</a>
                            </li>
                        @endcan
                        @can('cms.partnership_page.partner_model_section.index')
                            <li class="menu-item">
                                <a href="{{ route('cms.partnership_page.partner_model_section.index') }}"
                                    class="menu-link {{ request()->routeIs('cms.partnership_page.partner_model_section.*') ? 'active' : '' }}">Partner Model</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif


            {{-- <!-- CMS Contact Us Page Menu Item -->
            <li class="menu-item {{ request()->routeIs('cms.contact_us_page.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">contacts</span>
                    <span class="title">Contact Us</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('cms.contact_us_page.index') }}"
                            class="menu-link {{ request()->routeIs('cms.contact_us_page.index') ? 'active' : '' }}">
                            Contact Information
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.contact_us_page.form.create') }}"
                            class="menu-link {{ request()->routeIs('cms.contact_us_page.form.*') ? 'active' : '' }}">
                            Contact Form
                        </a>
                    </li>
                </ul>
            </li>
            <!-- CMS Auth Page Menu Item -->
            <li class="menu-item {{ request()->routeIs('cms.auth_page.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">fingerprint</span>
                    <span class="title">Auth</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('cms.auth_page.login.create') }}"
                            class="menu-link {{ request()->routeIs('cms.auth_page.login.*') ? 'active' : '' }}">
                            Login
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.auth_page.register.create') }}"
                            class="menu-link {{ request()->routeIs('cms.auth_page.register.*') ? 'active' : '' }}">
                            Register
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.auth_page.forgot_password.create') }}"
                            class="menu-link {{ request()->routeIs('cms.auth_page.forgot_password.*') ? 'active' : '' }}">
                            Forgot Password
                        </a>
                    </li>
                </ul>
            </li> --}}

            <!-- Logout Menu Item -->
            <li class="menu-item">
                <a class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form-asidebar').submit();">
                    <span class="material-symbols-outlined menu-icon">logout</span>
                    <span class="title">Logout</span>
                </a>
            </li>
        </ul>
        <form id="logout-form-asidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </aside>
</div>
<!-- End Sidebar Area -->
