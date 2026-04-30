<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic
Product Version: 8.2.3
Style: Modern + Menarik, Tombol Kotak, Latar Putih
-->
<html lang="id">
<head>
    <title>Rumah Inovasi Magetan</title>
    <meta charset="utf-8" />
    <meta name="description" content="Rumah Inovasi Magetan - Wadah Kreasi, Inovasi, dan Prestasi untuk Magetan Lebih Maju" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="template.demo6/demo6/assets/media/logos/mgt.png" />
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" />
    <!-- Global Stylesheets Bundle -->
    <link href="template.demo6/demo6/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="template.demo6/demo6/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        /* ============================================
           GAYA MODERN & MENARIK
           Latar putih bersih, tombol kotak, animasi halus
        ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            overflow-x: hidden;
        }

        /* Header dengan efek shadow halus dan glassmorphism ringan */
        .modern-header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Menu navigasi - efek modern */
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

        /* Underline animasi pada menu */
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

        .btn-register {
            background: #2563eb;
            color: white;
            border-radius: 0px;
            padding: 0.75rem 2rem;
            font-weight: 700;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(37,99,235,0.2);
        }

        .btn-register:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37,99,235,0.3);
        }
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            padding: 4rem 0 5rem 0;
            position: relative;
            overflow: hidden;
        }

        /* Background pattern halus */
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
            pointer-events: none;
        }

        /* Animasi floating untuk logo */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .hero-logo {
            animation: float 5s ease-in-out infinite;
            max-width: 320px;
            width: 100%;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.05));
            transition: all 0.3s ease;
        }

        .hero-logo:hover {
            filter: drop-shadow(0 15px 30px rgba(0,0,0,0.1));
            transform: scale(1.02);
        }

        /* Efek card untuk menu drawer mobile */
        [data-kt-drawer="true"] {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            box-shadow: -5px 0 30px rgba(0,0,0,0.05);
        }

        /* Animasi fade in untuk konten */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content {
            animation: fadeInUp 0.7s ease-out;
            position: relative;
            z-index: 2;
        }

        /* Decorative floating elements */
        .floating-dot {
            position: absolute;
            background: linear-gradient(135deg, #cbd5e1, #e2e8f0);
            border-radius: 50%;
            opacity: 0.4;
            pointer-events: none;
            animation: float 8s ease-in-out infinite;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Logo header hover effect */
        .logo-sticky {
            transition: all 0.3s ease;
        }
        .logo-sticky:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.1));
        }

        /* Footer */
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
        .footer a {
            color: #2563eb;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .hero-logo {
                max-width: 240px;
            }
            .hero-section {
                padding: 2rem 0 3rem 0;
            }
        }

        @media (max-width: 575.98px) {
            .hero-subtitle {
                font-size: 0.95rem;
                padding: 0 1rem;
            }
            .btn-register {
                padding: 0.7rem 1.8rem;
                font-size: 0.9rem;
            }
            .hero-logo {
                max-width: 180px;
            }
            .footer p {
                font-size: 0.75rem;
            }
        }

        .logo-text {
            text-decoration: none;
            font-weight: 800;
            line-height: 1.2;
        }
        .logo-text .line1 {
            font-size: 1.2rem;
            color: #0f172a;
            display: block;
        }
        .logo-text .line2 {
            font-size: 1.2rem;
            color: #2563eb;
            display: block;
        }

    </style>
    <script>if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
</head>
<body id="kt_body" style="background: #ffffff;">
    <!-- Theme mode light -->
    <script>var defaultThemeMode = "light"; var themeMode = "light"; document.documentElement.setAttribute("data-bs-theme", "light");</script>

    <div class="d-flex flex-column flex-root">
        <!-- Header Modern -->
        <div class="modern-header" id="home">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between py-2 py-lg-3">
                    <!-- Logo dan toggle mobile -->
                    <div class="d-flex align-items-center">
                        <button class="btn btn-icon btn-active-light me-3 d-flex d-lg-none border-0 bg-transparent" id="kt_landing_menu_toggle">
                            <i class="ki-outline ki-abstract-14 fs-1"></i>
                        </button>
                        <h1 style="font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1.2; margin: 0;">
                            RUMAHINOVASI
                        </h1>
                    </div>

                    <!-- Menu desktop -->
                    <div class="d-none d-lg-block" id="kt_header_nav_wrapper">
                        <div class="menu menu-lg-row fw-semibold fs-6" id="kt_landing_menu">
                            <div class="menu-item"><a class="menu-link nav-link" href="#achievements">Kekayaan Intelektual</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#team">KKN Award</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#portfolio">Jurnal Inovasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#dokumentasi">Dokumentasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#pengumuman">Pengumuman</a></div>
                        </div>
                    </div>

                    <!-- Login dan mobile drawer -->
                    <div class="d-flex align-items-center gap-3">
                        <a href="/sign-in" class="btn btn-login">Login</a>

                        <!-- Mobile drawer -->
                        <div class="d-lg-none">
                            <div data-kt-drawer="true" data-kt-drawer-name="landing-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="280px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_landing_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}">
                                <div class="menu menu-column p-4">
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">Kekayaan Intelektual</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">KKN Award</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">Jurnal Inovasi</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">Dokumentasi</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">Pengumuman</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section - Menarik dengan animasi -->
        <div class="hero-section">
            <!-- Floating decorative dots -->
            <div class="floating-dot" style="width: 80px; height: 80px; top: 10%; left: -30px; animation-delay: 0s;"></div>
            <div class="floating-dot" style="width: 50px; height: 50px; bottom: 15%; right: 10%; animation-delay: 2s;"></div>
            <div class="floating-dot" style="width: 30px; height: 30px; top: 30%; right: 20%; animation-delay: 1s;"></div>
            <div class="floating-dot" style="width: 60px; height: 60px; bottom: 5%; left: 15%; animation-delay: 3s;"></div>

            <div class="container">
                <div class="hero-content text-center py-4 py-md-6">
                    <!-- Logo dengan animasi float -->
                    <img src="template.demo6/demo6/assets/media/logos/rmh.png" alt="Rumah Inovasi Magetan" class="hero-logo" />

                    <!-- Tombol Pendaftaran KOTAK dengan efek hover modern -->
                    <div class="mt-4">
                        <a href="index" class="btn btn-register px-5 py-3 fs-5 fw-bold">
                            PENDAFTARAN →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER dengan COPYRIGHT -->
        <div class="footer">
            <div class="container">
                <p>&copy; <span id="currentYear"></span> <strong>Dinas Kominfo.</strong> All rights reserved.</p>
                <p> Develop by Dinas Kominfo Magetan Development</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>var hostUrl = "assets/";</script>
    <script src="template.demo6/demo6/assets/plugins/global/plugins.bundle.js"></script>
    <script src="template.demo6/demo6/assets/js/scripts.bundle.js"></script>
    <script src="template.demo6/demo6/assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
    <script src="template.demo6/demo6/assets/js/custom/landing.js"></script>

    <script>
        // Set current year untuk copyright
        document.getElementById('currentYear').innerText = new Date().getFullYear();

        // Inisialisasi drawer untuk mobile
        if (typeof KTComponents !== 'undefined') {
            KTComponents.init();
        }

        setTimeout(() => {
            if (typeof KTDrawer !== 'undefined') {
                const drawerElement = document.querySelector('[data-kt-drawer="true"]');
                if (drawerElement && !drawerElement.getAttribute('data-kt-drawer-init')) {
                    new KTDrawer(drawerElement);
                }
            }
        }, 500);
    </script>
</body>
</html>
