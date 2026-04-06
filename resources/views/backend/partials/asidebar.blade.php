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
            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">MAIN</span>
            </li>
            <!-- Dashboard Menu Item -->
            <li class="menu-item open">
                <a href="{{ route('admin.dashboard') }}"
                    class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-outlined menu-icon">dashboard</span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li class="menu-item open">
                <a href="{{ route('admin.research_ideas.index') }}"
                    class="menu-link {{ request()->routeIs('admin.research_ideas.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined menu-icon">biotech</span>
                    <span class="title">Research Ideas</span>
                </a>
            </li>

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">APPS</span>
            </li>

            <!-- Users Menu Item -->
            <li class="menu-item {{ request()->routeIs('user_management.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">group</span>
                    <span class="title">Users</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('user_management.mentor.index') }}"
                            class="menu-link {{ request()->routeIs('user_management.mentor.*') ? 'active' : '' }}">
                            Mentor
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="menu-item open">
                <a href="{{ route('admin_contact_us.index') }}"
                    class="menu-link {{ request()->routeIs('admin_contact_us.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined menu-icon">content_paste</span>
                    <span class="title">Contact Message</span>
                </a>
            </li>
            

            
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
            <li class="menu-item {{ request()->routeIs('faqs.*', 'faq_categories.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">help_outline</span>
                    <span class="title">FAQ's</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('faqs.index') }}"
                            class="menu-link {{ request()->routeIs('faqs.*') ? 'active' : '' }}">
                            FAQ List
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('faq_categories.index') }}"
                            class="menu-link {{ request()->routeIs('faq_categories.*') ? 'active' : '' }}">
                            FAQ Categories
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item open">
                <a href="{{ route('social_link.index') }}"
                    class="menu-link {{ request()->routeIs('social_link.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined menu-icon">link</span>
                    <span class="title">Social Link</span>
                </a>
            </li> 
            <!-- menu title -->
            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">OTHERS</span>
            </li>

            <!-- Settings Menu Item -->
            <li class="menu-item {{ request()->routeIs('profile_settings.*', 'system_settings.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">settings</span>
                    <span class="title">Settings</span>
                </a>
                <ul class="menu-sub">
                    <!-- Account Settings Submenu -->
                    <li class="menu-item ">
                        <a href="{{ route('profile_settings.index') }}"
                            class="menu-link {{ request()->routeIs('profile_settings.index') ? 'active' : '' }} ">
                            Profile Settings
                        </a>
                    </li>
                    <!-- Change Password Submenu -->
                    <li class="menu-item">
                        <a href="{{ route('profile_settings.password_change') }}"
                            class="menu-link {{ request()->routeIs('profile_settings.password_change') ? 'active' : '' }}">
                            Change Password
                        </a>
                    </li>
                    <!-- System Settings Configaration Submenu -->
                    <li class="menu-item">
                        <a href="{{ route('system_settings.index') }}"
                            class="menu-link {{ request()->routeIs('system_settings.*') ? 'active' : '' }}">
                            System Configaration
                        </a>
                    </li>

                </ul>
            </li>
            <!-- dynamic page Menu Item -->
            <li class="menu-item {{ request()->routeIs('dynamic_page.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">clarify</span>
                    <span class="title">Dynamic page</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('dynamic_page.index') }}"
                            class="menu-link {{ request()->routeIs('dynamic_page.index') ? 'active' : '' }}">
                            Pages
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('dynamic_page.create') }}"
                            class="menu-link {{ request()->routeIs('dynamic_page.create') ? 'active' : '' }}">
                            Add New
                        </a>
                    </li>

                </ul>
            </li>

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">CMS</span>
            </li>

            <!-- CMS Home Page Menu Item -->
            <li class="menu-item {{ request()->routeIs('cms.home_page.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">home</span>
                    <span class="title">Home Page</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.hero_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.hero_section.*') ? 'active' : '' }}">
                            Hero Section
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.counter_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.counter_section.*') ? 'active' : '' }}">
                            Counter Section
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.why_researchers_choose_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.why_researchers_choose_section.*') ? 'active' : '' }}">
                            Why Researchers Choose
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.featured_research_opportunities_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.featured_research_opportunities_section.*') ? 'active' : '' }}">
                            Featured Research Opportunities
                        </a>
                    </li>
                    
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.meta_academy_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.meta_academy_section.*') ? 'active' : '' }}">
                            Meta Academy
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.research_journey_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.research_journey_section.*') ? 'active' : '' }}">
                            Research Journey
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.faq_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.faq_section.*') ? 'active' : '' }}">
                            FAQ Section
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.home_page.review_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.home_page.review_section.*') ? 'active' : '' }}">
                            Review Section
                        </a>
                    </li>
                </ul>
            </li>
           



            <!-- CMS FAQ Page Menu Item -->
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

            <!-- CMS Research Page Menu Item -->
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

            <!-- CMS Service Page Menu Item -->
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

            <!-- CMS Meta Academy Page Menu Item -->
            <li class="menu-item {{ request()->routeIs('cms.meta_academy_page.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">school</span>
                    <span class="title">Meta Academy Page</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('cms.meta_academy_page.hero_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.meta_academy_page.hero_section.*') ? 'active' : '' }}">
                            Hero Section
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.meta_academy_page.counter_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.meta_academy_page.counter_section.*') ? 'active' : '' }}">
                            Counter Section
                        </a>
                    </li>
                </ul>
            </li>

            <!-- CMS Partnership Page Menu Item -->
            <li class="menu-item {{ request()->routeIs('cms.partnership_page.*') ? 'open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle active">
                    <span class="material-symbols-outlined menu-icon">handshake</span>
                    <span class="title">Partnership Page</span>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.hero_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.hero_section.*') ? 'active' : '' }}">Hero</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.our_mission_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.our_mission_section.*') ? 'active' : '' }}">Our Mission</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.about_us_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.about_us_section.*') ? 'active' : '' }}">About Us</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.partnership_value_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.partnership_value_section.*') ? 'active' : '' }}">Partnership Value</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.core_research_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.core_research_section.*') ? 'active' : '' }}">Core Research</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.systematic_review_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.systematic_review_section.*') ? 'active' : '' }}">Systematic Review</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.manuscript_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.manuscript_section.*') ? 'active' : '' }}">Manuscript</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.partnership_model_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.partnership_model_section.*') ? 'active' : '' }}">Partnership Model</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.reviewer_comment_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.reviewer_comment_section.*') ? 'active' : '' }}">Reviewer Comment</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.journal_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.journal_section.*') ? 'active' : '' }}">Journal</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.research_opportunitie_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.research_opportunitie_section.*') ? 'active' : '' }}">Research Opportunities</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.why_partner_section.create') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.why_partner_section.*') ? 'active' : '' }}">Why Partner</a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('cms.partnership_page.partner_model_section.index') }}"
                            class="menu-link {{ request()->routeIs('cms.partnership_page.partner_model_section.*') ? 'active' : '' }}">Partner Model</a>
                    </li>
                </ul>
            </li>


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
