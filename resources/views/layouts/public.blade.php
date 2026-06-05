<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rumah Inovasi Magetan - @yield('title')</title>
    <meta charset="utf-8" />
    <meta name="description" content="Rumah Inovasi Magetan - Wadah Kreasi, Inovasi, dan Prestasi untuk Magetan Lebih Maju" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="/template.demo6/demo6/assets/media/logos/mgt.png" />
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" />
    <!-- Global Stylesheets Bundle -->
    <link href="/template.demo6/demo6/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="/template.demo6/demo6/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: #ffffff;
            overflow-x: hidden;
        }

        .modern-header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .menu-link.nav-link {
            position: relative;
            font-weight: 600;
            color: #1e293b !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.6rem 1.2rem !important;
            margin: 0 2px;
            border-radius: 0px !important;
        }

        .menu-link.nav-link:hover {
            color: #0f172a !important;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transform: translateY(-2px);
        }

        .menu-link.nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #334155, #1e293b);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .menu-link.nav-link:hover::after {
            width: 40%;
        }

        .btn-login {
            background: #2563eb;
            border: 2px solid #3b82f6;
            color: white;
            border-radius: 0px;
            padding: 0.5rem 1.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #dbeafe;
            border-color: #2563eb;
            color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59,130,246,0.2);
        }

        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 0;
            text-align: center;
            margin-top: 2rem;
        }

        .footer p {
            color: #64748b;
            font-size: 0.875rem;
            margin: 0;
        }

        .footer strong {
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.3px;
        }

        @media (max-width: 991.98px) {
            .hero-logo {
                max-width: 240px;
            }
        }

        @media (max-width: 575.98px) {
            .footer p {
                font-size: 0.75rem;
            }
        }
    </style>
    <script>if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
</head>
<body id="kt_body" style="background: #ffffff;">
    <script>var defaultThemeMode = "light"; var themeMode = "light"; document.documentElement.setAttribute("data-bs-theme", "light");</script>

    <div class="d-flex flex-column flex-root">
        <!-- Header -->
        <div class="modern-header" id="home">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between py-2 py-lg-3">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-icon btn-active-light me-3 d-flex d-lg-none border-0 bg-transparent" id="kt_landing_menu_toggle">
                            <i class="ki-outline ki-abstract-14 fs-1"></i>
                        </button>
                        <h1 style="font-size: 2rem; font-weight: 800; color: #2563eb; line-height: 1.2; margin: 0;">
                            RUMAHINOVASI
                        </h1>
                    </div>

                    <div class="d-none d-lg-block" id="kt_header_nav_wrapper">
                        <div class="menu menu-lg-row fw-semibold fs-6" id="kt_landing_menu">
                            <div class="menu-item"><a class="menu-link nav-link" href="achievements">Kekayaan Intelektual</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="team">KKN Award</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="portfolio">Jurnal Inovasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="dokumentasi">Dokumentasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="pengumuman-luar">Pengumuman</a></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="/sign-in" class="btn btn-login">Login</a>

                        <div class="d-lg-none">
                            <div data-kt-drawer="true" data-kt-drawer-name="landing-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="280px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_landing_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}">
                                <div class="menu menu-column p-4">
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="/">Beranda</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="{{ route('public.pengumuman.index') }}">Pengumuman</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KONTEN UTAMA -->
        @yield('content')

        <!-- Footer -->
        <div class="footer">
            <div class="container">
                <p>&copy; Copyright <strong>Dinas Kominfo.</strong> All rights reserved.</p>
                <p> Develop by Dinas Kominfo Magetan Development</p>
            </div>
        </div>
    </div>

    <script>var hostUrl = "/template.demo6/demo6/assets/";</script>
    <script src="/template.demo6/demo6/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/template.demo6/demo6/assets/js/scripts.bundle.js"></script>
    <script src="/template.demo6/demo6/assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
    <script src="/template.demo6/demo6/assets/js/custom/landing.js"></script>

    <script>
        if (typeof KTComponents !== 'undefined') KTComponents.init();
        setTimeout(() => {
            if (typeof KTDrawer !== 'undefined') {
                const drawerElement = document.querySelector('[data-kt-drawer="true"]');
                if (drawerElement && !drawerElement.getAttribute('data-kt-drawer-init')) new KTDrawer(drawerElement);
            }
        }, 500);
    </script>
    @stack('scripts')
</body>
</html>
