<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rumah Inovasi Magetan - Panel Admin</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Rumah Inovasi Magetan - Panel Admin" />
    <meta property="og:url" content="https://rumahinovasi.com/admin" />
    <meta property="og:site_name" content="Rumah Inovasi Magetan" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('template.demo6/demo6/assets/media/logos/mgt.png') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <!-- Metronic CSS -->
    <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('template.demo6/demo6/assets/css/style.bundle.css') }}" rel="stylesheet" />

    <!-- Tambahan Anda -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="template.demo6/demo6/assets/media/logos/mgt.png" />
    
    <script>
        if (window.top !== window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>
    @stack('styles')
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed">
    <!-- Theme Mode -->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                themeMode = localStorage.getItem("data-bs-theme") || defaultThemeMode;
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">

            <!-- ==================== SIDEBAR ==================== -->
            <div id="kt_aside"
                 class="aside overflow-visible pb-5 pt-5 pt-lg-0"
                 style="background-color: #333c45 !important; color: #ffffff;"
                 data-kt-drawer="true"
                 data-kt-drawer-name="aside"
                 data-kt-drawer-activate="{default: true, lg: false}"
                 data-kt-drawer-overlay="true"
                 data-kt-drawer-width="{default:'260px', '300px': '260px'}"
                 data-kt-drawer-direction="start"
                 data-kt-drawer-toggle="#kt_aside_mobile_toggle">

                <!-- Logo -->
                <div class="aside-logo py-8" id="kt_aside_logo" style="background-color: #1b84ff;">
                    <a href="/" class="d-flex align-items-center gap-3 px-6">
                        <img alt="Logo" src="{{ asset('img/bulb.png') }}" class="h-60px logo" />
                        <div>
                            <div class="aside-logo-title fw-bold fs-3 text-white">Rumah Inovasi</div>
                            <div class="aside-logo-subtitle text-white opacity-75">Magetan</div>
                        </div>
                    </a>
                </div>

                <!-- Menu -->
                <div class="aside-menu flex-column-fluid" id="kt_aside_menu" style="background-color: #1b84ff;">
                    <nav id="ri-sidebar-nav" class="menu menu-column menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500 fw-semibold px-3">
                        @auth
                            @php $user = auth()->user(); @endphp

                            <!-- Dashboard -->
                            <a class="ri-menu-item menu-link py-3" href="/">
                                <span class="menu-icon me-3">
                                    <i class="ki-outline ki-home-2 fs-2x"></i>
                                </span>
                                <span class="menu-title">Dashboard</span>
                            </a>

                            <!-- Master (Admin Bapperida) -->
                            @if($user->isAdminBapperida())
                                <div class="menu-item pt-5">
                                    <span class="menu-section fs-5 fw-bolder ps-1 py-2">Master</span>
                                </div>
                                @include('partials.sidebar')
                            @endif

                            <!-- Indikator -->
                            @if($user->isAdminBapperida())
                                <div class="menu-item pt-5">
                                    <span class="menu-section fs-5 fw-bolder ps-1 py-2">Indikator</span>
                                </div>
                                <a class="ri-menu-item menu-link py-3" href="/indikator/tahap-1">
                                    <span class="menu-icon me-3"><i class="ki-outline ki-abstract-35 fs-2x"></i></span>
                                    <span class="menu-title">Indikator Tahap 1</span>
                                </a>
                                <a class="ri-menu-item menu-link py-3" href="/indikator/tahap-2">
                                    <span class="menu-icon me-3"><i class="ki-outline ki-abstract-35 fs-2x"></i></span>
                                    <span class="menu-title">Indikator Tahap 2</span>
                                </a>
                            @endif

                            <!-- Inovasi -->
                            <div class="menu-item pt-5">
                                <span class="menu-section fs-5 fw-bolder ps-1 py-2">Inovasi</span>
                            </div>
                            <a class="ri-menu-item menu-link py-3" href="/inovasi/riwayat">
                                <span class="menu-icon me-3"><i class="ki-outline ki-abstract-26 fs-2x"></i></span>
                                <span class="menu-title">Riwayat</span>
                            </a>
                            @if($user->hasRole(['admin_bapperida', 'penilai']))
                                <a class="ri-menu-item menu-link py-3" href="/inovasi/rekap-nilai">
                                    <span class="menu-icon me-3"><i class="ki-outline ki-abstract-26 fs-2x"></i></span>
                                    <span class="menu-title">Rekap Nilai</span>
                                </a>
                            @endif

                            <!-- Penilaian -->
                            @if($user->hasRole(['admin_bapperida', 'penilai']))
                                <div class="menu-item pt-5">
                                    <span class="menu-section fs-5 fw-bolder ps-1 py-2">Penilaian</span>
                                </div>
                                <a class="ri-menu-item menu-link py-3" href="/penilaian/tahap-1">
                                    <span class="menu-icon me-3"><i class="ki-outline ki-briefcase fs-2x"></i></span>
                                    <span class="menu-title">Penilaian Tahap 1</span>
                                </a>
                                <a class="ri-menu-item menu-link py-3" href="/penilaian/tahap-2">
                                    <span class="menu-icon me-3"><i class="ki-outline ki-briefcase fs-2x"></i></span>
                                    <span class="menu-title">Penilaian Tahap 2</span>
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>
            </div>
            <!-- End Sidebar -->

            <!-- ==================== MAIN CONTENT ==================== -->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!-- Header -->
                @include('partials.header')

                <!-- Toolbar -->
                <div class="toolbar py-2" id="kt_toolbar">
                    <div id="kt_toolbar_container" class="container-fluid d-flex align-items-center">
                        <div class="flex-grow-1 flex-shrink-0 me-5">
                            <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                                 data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                                 class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                <!-- Breadcrumb / Title akan diisi child view -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
                    <div id="kt_content_container" class="container-fluid">
                        @yield('content')
                    </div>
                </div>

                <!-- Footer -->
                @include('partials.footer')
            </div>
        </div>
    </div>

    <!-- Scrolltop -->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-outline ki-arrow-up"></i>
    </div>

    <!-- Scripts -->
    <script>var hostUrl = "{{ asset('template.demo6/demo6/assets/') }}";</script>
    <script src="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('template.demo6/demo6/assets/js/scripts.bundle.js') }}"></script>

    <!-- Custom JS Anda -->
    <script>
        // Active menu
        document.addEventListener('DOMContentLoaded', function () {
            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            document.querySelectorAll('#ri-sidebar-nav .ri-menu-item').forEach(link => {
                const href = link.getAttribute('href');
                if (!href) return;
                const linkPath = href.replace(/\/+$/, '') || '/';
                const isActive = linkPath === '/' ? currentPath === '/' : currentPath.startsWith(linkPath);
                link.classList.toggle('active', isActive);
            });
        });

        // Background sesuai tema
        function applyThemeBg() {
            const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const bg = theme === 'dark' ? '#1C2333' : '#F9FBFF';
            ['kt_wrapper', 'kt_content', 'kt_toolbar'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.backgroundColor = bg;
            });
        }
        document.addEventListener('DOMContentLoaded', applyThemeBg);
        new MutationObserver(applyThemeBg).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
    </script>

    @stack('scripts')
</body>
</html>