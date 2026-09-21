<!-- Begin Header -->
<header class="app-header" id="appHeader">
    @php
        $headerHomeUrl = route('home');
        if (auth()->check()) {
            $isUserRole = (auth()->user()->role?->role_name ?? null) === 'Users' || (int) auth()->user()->role_id === 3;
            $headerHomeUrl = $isUserRole ? route('user.dashboard') : route('admin.dashboard');
        }

        $currentLocale = app()->getLocale();
        $languageOptions = [
            'en' => __('settings.languages.en'),
            'id' => __('settings.languages.id'),
            'ko' => __('settings.languages.ko'),
        ];

        $greetings = [
            'morning' => __('common.greeting.morning'),
            'afternoon' => __('common.greeting.afternoon'),
            'evening' => __('common.greeting.evening'),
        ];

        $intlLocale = match ($currentLocale) {
            'id' => 'id-ID',
            'ko' => 'ko-KR',
            default => 'en-US',
        };
    @endphp

    <style>
        .header-greeting {
            min-width: 0;
            max-width: 360px;
            line-height: 1.15;
        }

        .header-greeting #navbarSalam,
        .header-greeting #navbarDateTime {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Biar tidak mengganggu layout saat sidebar mode icon/minimize */
        [data-sidebar="icon"] .header-greeting {
            display: none !important;
        }

        .stock-notification-button {
            position: relative;
        }

        .stock-notification-badge {
            position: absolute;
            top: -.25rem;
            right: -.25rem;
            display: none;
            min-width: 1.125rem;
            height: 1.125rem;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            border-radius: 50%;
            background: #f04438;
            color: #fff;
            font-size: .625rem;
            font-weight: 700;
            line-height: 1;
        }

        .stock-notification-badge.is-visible {
            display: inline-flex;
        }

        .stock-notification-button.has-unread .fa-bell {
            color: #f26b21;
            animation: stock-notification-pulse 1.8s ease-in-out infinite;
        }

        @keyframes stock-notification-pulse {
            50% {
                transform: scale(1.16);
            }
        }

        .stock-notification-toasts {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 1080;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            width: min(23rem, calc(100vw - 2rem));
            max-height: calc(100dvh - 2rem);
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: thin;
        }

        .stock-notification-toast {
            flex: 0 0 auto;
            border: 1px solid #d0d5dd;
            border-left: 4px solid #f26b21;
            border-radius: .375rem;
            background: #fff;
            box-shadow: 0 .75rem 1.5rem rgba(16, 24, 40, .16);
            padding: .875rem 1rem;
        }

        .stock-notification-toast.is-low-stock {
            border-left-color: #f79009;
        }

        /* Responsive: keep header/toggle accessible above sidebar overlay */
        @media (max-width: 991.98px) {
            .app-header {
                z-index: 1010;
            }

            #toggleSidebar {
                position: relative;
                z-index: 1011;
            }
        }
    </style>
    <div class="container-fluid w-100">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <div class="d-inline-flex align-items-center gap-5" style="min-width: 0;">
                    <a href="{{ $headerHomeUrl }}" class="fs-18 fw-semibold">
                        <img height="30" class="header-sidebar-logo-default d-none" alt="Logo"
                            src="{{ asset('assets/img/logo.svg') }}">
                        <img height="30" class="header-sidebar-logo-light d-none" alt="Logo"
                            src="{{ asset('assets/img/logo_wh.svg') }}">
                        <img height="30" class="header-sidebar-logo-small d-none" alt="Logo"
                            src="{{ asset('assets/img/logo-min.svg') }}">
                        <img height="30" class="header-sidebar-logo-small-light d-none" alt="Logo"
                            src="{{ asset('assets/img/logo-min.svg') }}">
                    </a>
                    <button type="button"
                        class="vertical-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill"
                        id="toggleSidebar">
                        <i class="fas fa-bars header-icon"></i>
                    </button>
                    <button type="button"
                        class="horizontal-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill d-none"
                        id="toggleHorizontal">
                        <i class="ri-menu-2-line header-icon"></i>
                    </button>

                    <div class="d-none d-lg-flex flex-column text-start header-greeting">
                        <span class="fw-semibold" id="navbarSalam">{{ __('common.welcome') }}</span>
                        <span class="small text-muted" id="navbarDateTime">{{ __('common.loading') }}</span>
                    </div>
                </div>
            </div>
            <div class="shrink-0 d-flex align-items-center gap-2">
                <button type="button" class="btn header-btn d-none d-md-block" data-bs-toggle="modal"
                    data-bs-target="#exampleModal" data-bs-whatever="@mdo">
                    <i class="fas fa-search"></i>
                </button>
                {{-- <button class="btn header-btn d-none d-md-block" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="fas fa-cog"></i>
                </button> --}}
                <div class="dark-mode-btn" id="toggleMode">
                    <button class="btn header-btn active" id="lightModeBtn">
                        <i class="fas fa-sun"></i>
                    </button>
                    <button class="btn header-btn" id="darkModeBtn">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>

                <!-- Mobile: combine Profile + Language into one dropdown (md down) -->
                <div class="dropdown d-md-none">
                    <button class="header-profile-btn btn gap-1 text-start" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false" aria-label="Profile">
                        <span class="header-btn btn position-relative">
                            @if (auth()->check())
                                <img src="{{ Avatar::create(auth()->user()->name ?? (auth()->user()->username ?? 'User'))->toBase64() }}"
                                    alt="Avatar Image" class="img-fluid rounded-circle">
                                <span
                                    class="position-absolute translate-middle badge border border-light rounded-circle bg-success"><span
                                        class="visually-hidden">online</span></span>
                            @else
                                <img src="{{ asset('assets/img/users/avatar-10.jpg') }}" alt="Avatar Image"
                                    class="img-fluid rounded-circle">
                                <span
                                    class="position-absolute translate-middle badge border border-light rounded-circle bg-success"><span
                                        class="visually-hidden">online</span></span>
                            @endif
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end header-dropdown-menu p-0" style="min-width: 260px;">
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="shrink-0">
                                    @if (auth()->check())
                                        <img src="{{ Avatar::create(auth()->user()->name ?? (auth()->user()->username ?? 'User'))->toBase64() }}"
                                            alt="Avatar Image" class="avatar-md">
                                    @else
                                        <img src="{{ asset('assets/img/avatar/avatar-10.jpg') }}" alt="Avatar Image"
                                            class="avatar-md">
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    @if (auth()->check())
                                        <div class="fw-semibold text-truncate">
                                            {{ auth()->user()->name ?? auth()->user()->username }}</div>
                                        <div class="small text-muted text-truncate">{{ auth()->user()->email }}</div>
                                    @else
                                        <div class="fw-semibold">Guest</div>
                                        <div class="small text-muted">Guest</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="p-2 border-bottom">
                            @if (auth()->check())
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-1"></i> {{ __('auth.logout') }}
                                    </button>
                                </form>
                            @else
                                <a class="dropdown-item" href="{{ route('auth') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    {{ __('auth.login') === 'auth.login' ? 'Login' : __('auth.login') }}
                                </a>
                            @endif
                        </div>

                        <div class="p-3 border-bottom">
                            <h6 class="mb-0">{{ __('settings.choose_language') }}</h6>
                        </div>
                        <div class="p-2">
                            @foreach ($languageOptions as $code => $label)
                                <form action="{{ route('language.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $code }}">
                                    <button type="submit"
                                        class="dropdown-item d-flex align-items-center justify-content-between">
                                        <span>{{ $label }}</span>
                                        @if ($currentLocale === $code)
                                            <i class="fas fa-check"></i>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="btn header-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-language"></i>
                    </button>
                    <div class="dropdown-menu dropdown-mega-md header-dropdown-menu p-0" style="min-width: 220px;">
                        <div class="p-3 border-bottom">
                            <h6 class="mb-0">{{ __('settings.choose_language') }}</h6>
                        </div>
                        <div class="p-2">
                            @foreach ($languageOptions as $code => $label)
                                <form action="{{ route('language.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="locale" value="{{ $code }}">
                                    <button type="submit"
                                        class="dropdown-item d-flex align-items-center justify-content-between">
                                        <span>{{ $label }}</span>
                                        @if ($currentLocale === $code)
                                            <i class="fas fa-check"></i>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button id="stockNotificationButton" class="stock-notification-button btn header-btn" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifikasi stok">
                        <i class="fas fa-bell"></i>
                        <span id="stockNotificationBadge" class="stock-notification-badge">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-mega-md header-dropdown-menu pe-noti-dropdown-menu p-0">
                        <div class="p-3 border-bottom">
                            <h6 class="d-flex align-items-center mb-0">{{ __('common.notification') }} <span
                                    id="stockNotificationCount"
                                    class="badge bg-secondary rounded-circle align-middle ms-1">0</span></h6>
                        </div>
                        <div id="stockNotificationList" class="p-3">
                            <div class="text-center text-muted py-4">
                                {{ __('common.no_notifications') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div id="stockNotificationToasts" class="stock-notification-toasts" aria-live="polite"></div>
                @if (auth()->check())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const notificationsUrl = @json(route('admin.stock.notifications'));
                            const readUrl = @json(url('/admin/stock-notifications'));
                            const count = document.getElementById('stockNotificationCount');
                            const badge = document.getElementById('stockNotificationBadge');
                            const button = document.getElementById('stockNotificationButton');
                            const list = document.getElementById('stockNotificationList');
                            const toasts = document.getElementById('stockNotificationToasts');
                            const seenStorageKey = 'stockNotificationLastSeenId';
                            let initialized = sessionStorage.getItem(seenStorageKey) !== null;

                            const escapeHtml = value => String(value ?? '').replace(/[&<>'"]/g, character => ({
                                '&': '&amp;',
                                '<': '&lt;',
                                '>': '&gt;',
                                "'": '&#39;',
                                '"': '&quot;'
                            })[character]);

                            const updateBadge = unread => {
                                const label = unread > 99 ? '99+' : String(unread);
                                if (count) count.textContent = label;
                                if (badge) {
                                    badge.textContent = label;
                                    badge.classList.toggle('is-visible', unread > 0);
                                }
                                button?.classList.toggle('has-unread', unread > 0);
                            };

                            const showToast = notification => {
                                if (!toasts) return;
                                const toast = document.createElement('div');
                                toast.className = `stock-notification-toast${notification.type === 'low_stock' ? ' is-low-stock' : ''}`;
                                toast.innerHTML = `<div class="d-flex gap-2"><i class="fas ${notification.type === 'low_stock' ? 'fa-triangle-exclamation text-warning' : 'fa-truck-fast text-primary'} mt-1"></i><div><strong>${escapeHtml(notification.title)}</strong><div class="small text-muted mt-1">${escapeHtml(notification.message)}</div></div></div>`;
                                toasts.appendChild(toast);
                                toasts.scrollTop = toasts.scrollHeight;
                                window.setTimeout(() => toast.remove(), 7000);
                            };

                            const renderNotifications = payload => {
                                updateBadge(payload.unread || 0);
                                if (!list) return;
                                if (!payload.data?.length) {
                                    list.innerHTML = '<div class="text-center text-muted py-4">{{ __('common.no_notifications') }}</div>';
                                    return;
                                }
                                list.innerHTML = payload.data.map(notification =>
                                    `<a href="${escapeHtml(notification.context_url || '#')}" class="dropdown-item text-wrap border-bottom py-2${notification.read_at ? ' text-muted' : ''}${notification.context_url ? '' : ' disabled'}" data-notification-id="${notification.id}" data-unread="${notification.read_at ? '0' : '1'}" data-context-url="${escapeHtml(notification.context_url || '')}"><strong>${escapeHtml(notification.title)}</strong><br><small>${escapeHtml(notification.message)}</small></a>`
                                ).join('');
                                list.querySelectorAll('[data-notification-id]').forEach(element => element.addEventListener('click', function(event) {
                                    const contextUrl = this.dataset.contextUrl;
                                    if (!contextUrl) return;
                                    event.preventDefault();
                                    const markRead = this.dataset.unread !== '1' ? Promise.resolve() : fetch(`${readUrl}/${this.dataset.notificationId}/read`, {
                                        method: 'PATCH',
                                        headers: {
                                            'X-CSRF-TOKEN': @json(csrf_token()),
                                            'Accept': 'application/json'
                                        }
                                    }).then(response => {
                                        if (!response.ok) return;
                                        this.dataset.unread = '0';
                                        this.classList.add('text-muted');
                                        updateBadge(Math.max(0, Number(count?.textContent || 0) - 1));
                                    }).catch(() => {});
                                    markRead.finally(() => window.location.assign(contextUrl));
                                }));
                            };

                            const loadNotifications = () => fetch(notificationsUrl, {
                                headers: {
                                    'Accept': 'application/json'
                                },
                                cache: 'no-store'
                            }).then(response => response.ok ? response.json() : null).then(payload => {
                                if (!payload) return;
                                const lastSeenId = Number(sessionStorage.getItem(seenStorageKey) || 0);
                                const newNotifications = initialized ? payload.data.filter(notification => Number(notification.id) > lastSeenId) : [];
                                const newestNotificationId = Math.max(lastSeenId, ...payload.data.map(notification => Number(notification.id)));
                                sessionStorage.setItem(seenStorageKey, String(newestNotificationId));
                                renderNotifications(payload);
                                newNotifications.filter(notification => ['low_stock', 'transfer_prepared', 'transfer_shipped', 'transfer_received'].includes(notification.type))
                                    .forEach(showToast);
                                initialized = true;
                            }).catch(() => {});

                            loadNotifications();
                            window.setInterval(loadNotifications, 30000);
                        });
                    </script>
                @endif
                {{-- <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="btn btn-icon header-btn p-1" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16"
                            class="object-fit-cover rounded">
                    </button>
                    <ul class="dropdown-menu header-dropdown-menu">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16"
                                    class="object-fit-cover rounded">
                                English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/es.svg" alt="Flag Image" height="16" width="16"
                                    class="object-fit-cover rounded">
                                Spanish
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/ru.svg" alt="Flag Image" height="16" width="16"
                                    class="object-fit-cover rounded">
                                French
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16"
                                    class="object-fit-cover rounded">
                                Russian
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/de.svg" alt="Flag Image" height="16" width="16"
                                    class="object-fit-cover rounded">
                                German
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/cn.svg" alt="Flag Image" height="16" width="16"
                                    class="object-fit-cover rounded">
                                Chinese
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/sa.svg" alt="Flag Image" height="16" width="16"
                                    class="object-fit-cover rounded">
                                Arabic
                            </a>
                        </li>
                    </ul>
                </div> --}}
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="header-profile-btn btn gap-1 text-start" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="header-btn btn position-relative">
                            @if (auth()->check())
                                <img src="{{ Avatar::create(auth()->user()->name ?? (auth()->user()->username ?? 'User'))->toBase64() }}"
                                    alt="Avatar Image" class="img-fluid rounded-circle">
                                <span
                                    class="position-absolute translate-middle badge border border-light rounded-circle bg-success"><span
                                        class="visually-hidden">unread messages</span></span>
                            @else
                                <img src="{{ asset('assets/img/users/avatar-10.jpg') }}" alt="Avatar Image"
                                    class="img-fluid rounded-circle">
                                <span
                                    class="position-absolute translate-middle badge border border-light rounded-circle bg-success"><span
                                        class="visually-hidden">unread messages</span></span>
                            @endif
                        </span>
                        <div class="d-none d-lg-block pe-2">
                            @if (auth()->check())
                                <span
                                    class="d-block mb-0 fs-13 fw-semibold">{{ auth()->user()->name ?? auth()->user()->username }}</span>
                                <span class="d-block mb-0 fs-12 text-muted">{{ auth()->user()->email }}</span>
                            @else
                                <span class="d-block mb-0 fs-13 fw-semibold">Guest</span>
                                <span class="d-block mb-0 fs-12 text-muted">Guest</span>
                            @endif
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-mega-sm header-dropdown-menu p-3">
                        <div class="border-bottom pb-2 mb-2 d-flex align-items-center gap-2">
                            @if (auth()->check())
                                <img src="{{ Avatar::create(auth()->user()->name ?? (auth()->user()->username ?? 'User'))->toBase64() }}"
                                    alt="Avatar Image" class="avatar-md">
                                <div>
                                    <a href="javascript:void(0)">
                                        <h6 class="mb-0 lh-base">
                                            {{ auth()->user()->name ?? auth()->user()->username }}</h6>
                                    </a>
                                    <p class="mb-0 fs-13 text-muted">{{ auth()->user()->email }}</p>
                                </div>
                            @else
                                <img src="{{ asset('assets/img/avatar/avatar-10.jpg') }}" alt="Avatar Image"
                                    class="avatar-md">
                                <div>
                                    <a href="javascript:void(0)">
                                        <h6 class="mb-0 lh-base">Guest</h6>
                                    </a>
                                    <p class="mb-0 fs-13 text-muted">Guest</p>
                                </div>
                            @endif
                        </div>
                        {{-- <ul class="list-unstyled mb-1 border-bottom pb-1">
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="fas fa-user me-1"></i>
                                    View Profile</a></li>
                        </ul> --}}
                        <ul class="list-unstyled mb-0">
                            @if (auth()->check())
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i
                                                class="fas fa-sign-out-alt me-1"></i> {{ __('auth.logout') }}</button>
                                    </form>
                                </li>
                            @else
                                <li><a class="dropdown-item" href=""><i class="fas fa-sign-out-alt me-1"></i>
                                        {{ __('auth.logout') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END Header -->

<script>
    (function() {
        const salamEl = document.getElementById('navbarSalam');
        const dateTimeEl = document.getElementById('navbarDateTime');
        if (!salamEl || !dateTimeEl) return;

        const userName = @json(auth()->check() ? auth()->user()->name ?? (auth()->user()->username ?? null) : null);

        const intlLocale = @json($intlLocale);
        const greetings = @json($greetings);

        function getSalam(hour) {
            // Sesuai request: pagi, siang, sore.
            if (hour >= 5 && hour < 11) return greetings.morning;
            if (hour >= 11 && hour < 15) return greetings.afternoon;
            return greetings.evening;
        }

        const dateFmt = new Intl.DateTimeFormat(intlLocale, {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
        const timeFmt = new Intl.DateTimeFormat(intlLocale, {
            hour: '2-digit',
            minute: '2-digit'
        });

        function tick() {
            const now = new Date();
            const salam = getSalam(now.getHours());
            salamEl.textContent = userName ? `${salam}, ${userName}` : salam;
            dateTimeEl.textContent = `${dateFmt.format(now)} • ${timeFmt.format(now)}`;
        }

        tick();
        setInterval(tick, 30 * 1000);
    })();
</script>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 bg-transparent">
            <div class="d-flex justify-content-between align-items-center bg-body">
                <div class="d-flex align-items-center border-0 px-3">
                    <i class="fas fa-search me-2"></i>
                    <input class="d-flex w-full py-3 bg-transparent border-0 focus-ring"
                        placeholder="{{ __('common.search_here') }}" autocomplete="off" autocorrect="off"
                        spellcheck="false" aria-autocomplete="list" role="combobox" aria-expanded="true"
                        type="text">
                </div>
                <button type="button" class="btn-close pe-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body mt-4">
                <p class="font-normal mb-0 text-muted">
                    {{ __('common.type_to_search') }}
                </p>
            </div>
        </div>
    </div>
</div>
