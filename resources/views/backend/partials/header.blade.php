<!-- Start Header Area -->
<header class="header-area" id="header-area">
    <div class="row align-items-center">
        <div class="col-lg-4 col-sm-6">
            <div class="left-header-content">
                <ul
                    class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
                    <li>
                        <button class="header-burger-menu bg-transparent p-0 border-0"
                            id="header-burger-menu">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                    </li>
                    
                </ul>
            </div>
        </div>

        <div class="col-lg-8 col-sm-6">
            <div class="right-header-content mt-2 mt-sm-0">
                <ul
                    class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
                    <li class="header-right-item">
                        <div class="dropdown lang-switcher">
                            <button class="btn btn-secondary border-0 p-0 bg-transparent d-flex align-items-center gap-1"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                title="{{ __('admin.language') }}">
                                <span class="material-symbols-outlined text-body">language</span>
                                <span class="d-none d-md-inline fs-13 fw-medium text-body">
                                    {{ ($htmlLocale ?? 'en') === 'ar' ? __('admin.arabic') : 'EN' }}
                                </span>
                            </button>
                            <ul class="dropdown-menu border-0 bg-white p-2 shadow-sm min-w-auto {{ ($htmlDir ?? 'ltr') === 'rtl' ? 'dropdown-menu-start' : 'dropdown-menu-end' }}">
                                <li>
                                    <form action="{{ route('admin.locale.update', 'en') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item rounded d-flex align-items-center justify-content-between {{ ($htmlLocale ?? 'en') === 'en' ? 'active fw-semibold' : '' }}">
                                            <span>{{ __('admin.english') }}</span>
                                            @if(($htmlLocale ?? 'en') === 'en')
                                                <i class="material-symbols-outlined fs-18 text-primary">check</i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.locale.update', 'ar') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item rounded d-flex align-items-center justify-content-between {{ ($htmlLocale ?? 'en') === 'ar' ? 'active fw-semibold' : '' }}">
                                            <span>{{ __('admin.arabic') }}</span>
                                            @if(($htmlLocale ?? 'en') === 'ar')
                                                <i class="material-symbols-outlined fs-18 text-primary">check</i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="header-right-item">
                        <div class="light-dark">
                            <button class="switch-toggle settings-btn dark-btn p-0 bg-transparent"
                                id="switch-toggle">
                                <span class="dark"><i
                                        class="material-symbols-outlined">light_mode</i></span>
                                <span class="light"><i
                                        class="material-symbols-outlined">dark_mode</i></span>
                            </button>
                        </div>
                    </li>

                    <li class="header-right-item">
                        <button class="fullscreen-btn bg-transparent p-0 border-0" id="fullscreen-button">
                            <i class="material-symbols-outlined text-body">fullscreen</i>
                        </button>
                    </li>
                    <li class="header-right-item">
                        <div class="dropdown notifications noti">
                            <button class="btn btn-secondary border-0 p-0 position-relative"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="markAllRead()">
                                
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count">
                                    {{ Auth::user()?->notifications()->whereNull('read_at')->count() }}
                                </span>
                                
                                <span class="material-symbols-outlined">notifications</span>
                            </button>
                            <div class="dropdown-menu dropdown-lg p-0 border-0 p-0 dropdown-menu-end">
                                <div class="d-flex justify-content-between align-items-center title">
                                    <span class="fw-semibold fs-15 text-secondary">{{ __('admin.notifications') }} <span
                                            class="fw-normal text-body fs-14">({{ Auth::user()?->notifications()->whereNull('read_at')->count() }})</span></span>
                                    <button class="p-0 m-0 bg-transparent border-0 fs-14 text-primary"  onclick="deleteAllNotification()">{{ __('admin.delete_all') }}</button>
                                </div>

                                <div class="max-h-217" data-simplebar id="notification-list">
                                    @if (Auth::user()?->notifications)
                                    @foreach (Auth::user()?->notifications as $notification)
                                    <div class="notification-menu {{ $notification->read_at ? '' : 'unseen' }}"  id="notification_{{ $notification->id }}">
                                        <a href="{{ $notification->data['url'] ?? '' }}" class="dropdown-item" >
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded bg-light" style="width: 40px; height: 40px; overflow: hidden;">
                                                            <img src="{{ $notification->data['thumbnail'] ?? asset('notification-icon/cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0.png') }}" 
                                                                 alt="Notification Thumbnail" 
                                                                 class="img-fluid rounded">
                                                        </div>
                                                    </div>
                                                    {{-- <i
                                                        class="material-symbols-outlined text-primary">sms</i> --}}
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p>{{ $notification->data['type'] ?? 'Untitled Notification' }}</p>
                                                    <span class="fs-13">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="header-right-item">
                        <div class="dropdown admin-profile">
                            <div class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <div class="flex-shrink-0">
                                    <img class="rounded-circle wh-40 administrator"
                                        src="{{ asset(Auth::user()->avatar ?? 'backend/admin/assets/images/avatar_defult.png') }}"
                                        alt="admin">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-none d-xxl-block">
                                            <div class="d-flex align-content-center">
                                                <h3>{{ Auth::user()?->name ?? 'Mr. John Doe' }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                                <div class="d-flex align-items-center info">
                                    <div class="flex-shrink-0">
                                        <img class="rounded-circle wh-30 administrator"
                                            src="{{ asset(Auth::user()?->avatar ?? 'backend/admin/assets/images/avatar_defult.png') }}"
                                            alt="admin">
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h3 class="fw-medium">{{ Auth::user()?->name ?? 'Mr. John Doe' }}</h3>
                                        <span class="fs-12">{{ Auth::user()?->role ?? 'Marketing Manager' }}</span>
                                    </div>
                                </div>
                                <ul class="admin-link ps-0 mb-0 list-unstyled">
                                    <li>
                                        <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                            href="{{route('profile_settings.index')}}">
                                            <i class="material-symbols-outlined">account_circle</i>
                                            <span class="ms-2">{{ __('admin.my_profile') }}</span>
                                        </a>
                                    </li>
                                </ul>
                                <ul class="admin-link ps-0 mb-0 list-unstyled">
                                    <li>
                                        <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                            href="{{route('system_settings.index')}}">
                                            <i class="material-symbols-outlined">settings </i>
                                            <span class="ms-2">{{ __('admin.settings') }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item admin-item-link d-flex align-items-center text-body"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="material-symbols-outlined">logout</i>
                                            <span class="ms-2">{{ __('admin.logout') }}</span>
                                        </a>
                                    </li>
                                </ul>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                  @csrf
                              </form>
                            </div>
                        </div>
                    </li>
                    <li class="header-right-item">
                        <button class="theme-settings-btn p-0 border-0 bg-transparent" type="button"
                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling"
                            aria-controls="offcanvasScrolling">
                            <i class="material-symbols-outlined" data-bs-toggle="tooltip"
                                data-bs-placement="left"
                                data-bs-title="Click On Theme Settings">settings</i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
<!-- End Header Area -->