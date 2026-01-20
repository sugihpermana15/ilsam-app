<!-- Begin Header -->
<header class="app-header" id="appHeader">
    @php
        $headerHomeUrl = route('home');
        if (auth()->check()) {
            $isUserRole = ((auth()->user()->role?->role_name ?? null) === 'Users') || ((int) auth()->user()->role_id === 3);
            $headerHomeUrl = $isUserRole ? route('user.dashboard') : route('admin.dashboard');
        }
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
                        <i class="bi bi-arrow-bar-left header-icon"></i>
                    </button>
                    <button type="button"
                        class="horizontal-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill d-none"
                        id="toggleHorizontal">
                        <i class="ri-menu-2-line header-icon"></i>
                    </button>

                    <div class="d-none d-lg-flex flex-column text-start header-greeting">
                        <span class="fw-semibold" id="navbarSalam">Selamat Datang</span>
                        <span class="small text-muted" id="navbarDateTime">-</span>
                    </div>
                </div>
            </div>
            <div class="shrink-0 d-flex align-items-center gap-2">
                <button type="button" class="btn header-btn d-none d-md-block" data-bs-toggle="modal"
                    data-bs-target="#exampleModal" data-bs-whatever="@mdo">
                    <i class="bi bi-search"></i>
                </button>
                {{-- <button class="btn header-btn d-none d-md-block" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="bi bi-gear"></i>
                </button> --}}
                <div class="dark-mode-btn" id="toggleMode">
                    <button class="btn header-btn active" id="lightModeBtn">
                        <i class="bi bi-brightness-high"></i>
                    </button>
                    <button class="btn header-btn" id="darkModeBtn">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="btn header-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                    </button>
                    <div class="dropdown-menu dropdown-mega-md header-dropdown-menu pe-noti-dropdown-menu p-0">
                        <div class="p-3 border-bottom">
                            <h6 class="d-flex align-items-center mb-0">Notification <span
                                    class="badge bg-secondary rounded-circle align-middle ms-1">0</span></h6>
                        </div>
                        <div class="p-3">
                            <div class="text-center text-muted py-4">
                                Belum ada notifikasi.
                            </div>
                        </div>
                    </div>
                </div>
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
                            @if(auth()->check())
                                <img src="{{ Avatar::create(auth()->user()->name ?? auth()->user()->username ?? 'User')->toBase64() }}"
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
                            @if(auth()->check())
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
                            @if(auth()->check())
                                <img src="{{ Avatar::create(auth()->user()->name ?? auth()->user()->username ?? 'User')->toBase64() }}"
                                    alt="Avatar Image" class="avatar-md">
                                <div>
                                    <a href="javascript:void(0)">
                                        <h6 class="mb-0 lh-base">{{ auth()->user()->name ?? auth()->user()->username }}</h6>
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
                            <li><a class="dropdown-item" href="javascript:void(0)"><i class="bi bi-person me-1"></i>
                                    View Profile</a></li>
                        </ul> --}}
                        <ul class="list-unstyled mb-0">
                            @if(auth()->check())
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i
                                                class="bi bi-box-arrow-right me-1"></i> Sign Out</button>
                                    </form>
                                </li>
                            @else
                                <li><a class="dropdown-item" href=""><i class="bi bi-box-arrow-right me-1"></i> Sign Out</a>
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
    (function () {
        const salamEl = document.getElementById('navbarSalam');
        const dateTimeEl = document.getElementById('navbarDateTime');
        if (!salamEl || !dateTimeEl) return;

        const userName = @json(auth()->check() ? (auth()->user()->name ?? auth()->user()->username ?? null) : null);

        function getSalam(hour) {
            // Sesuai request: pagi, siang, sore.
            if (hour >= 5 && hour < 11) return 'Selamat Pagi';
            if (hour >= 11 && hour < 15) return 'Selamat Siang';
            return 'Selamat Sore';
        }

        const dateFmt = new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
        const timeFmt = new Intl.DateTimeFormat('id-ID', {
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
                    <i class="bi bi-search me-2"></i>
                    <input class="d-flex w-full py-3 bg-transparent border-0 focus-ring" placeholder="Search Here.."
                        autocomplete="off" autocorrect="off" spellcheck="false" aria-autocomplete="list" role="combobox"
                        aria-expanded="true" type="text">
                </div>
                <button type="button" class="btn-close pe-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body mt-4">
                <p class="font-normal mb-0 text-muted">
                    Ketik kata kunci untuk mencari.
                </p>
            </div>
        </div>
    </div>
</div>