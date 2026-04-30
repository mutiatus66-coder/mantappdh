<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic
Product Version: 8.2.3
Style: Latar putih bersih, modern, mengacu ke rumahinovasi.magetan.go.id
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
           GAYA MODERN DENGAN LATAR PUTIH
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

        /* Header dengan efek shadow halus */
        .modern-header {
            background: #ffffff;
            border-bottom: 1px solid #eef2f6;
            box-shadow: 0 2px 12px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Menu navigasi - modern dengan hover efek */
        .menu-link.nav-link {
            position: relative;
            font-weight: 600;
            color: #1e293b !important;
            transition: all 0.3s ease;
            padding: 0.6rem 1.2rem !important;
            border-radius: 40px;
            margin: 0 2px;
        }

        .menu-link.nav-link:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
            color: #0f3b5f !important;
        }

        /* Indikator hover underline */
        .menu-link.nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: #1e293b;
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 3px;
        }

        .menu-link.nav-link:hover::after {
            width: 50%;
        }

        /* Tombol Login - outline abu-abu */
        .btn-modern-outline {
            background: transparent;
            border: 1.5px solid #cbd5e1;
            color: #1e293b;
            border-radius: 40px;
            padding: 0.5rem 1.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-modern-outline:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            transform: translateY(-2px);
        }

        /* Tombol Pendaftaran - solid gelap (seperti referensi) */
        .btn-modern-primary {
            background: #1e293b;
            color: white;
            border-radius: 50px;
            padding: 0.9rem 2.5rem;
            font-weight: 700;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            letter-spacing: 0.5px;
        }

        .btn-modern-primary:hover {
            background: #0f172a;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        /* Hero Section - latar putih bersih */
        .hero-section {
            background: #ffffff;
            padding: 3rem 0 5rem 0;
            text-align: center;
        }

        /* Animasi floating untuk logo */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        .hero-logo {
            animation: float 4s ease-in-out infinite;
            max-width: 320px;
            width: 100%;
        }

        /* Teks RUMAH INOVASI - style bold seperti referensi */
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
            line-height: 1.2;
        }

        .hero-subtitle {
            color: #475569;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 1rem auto;
        }

        /* Efek card untuk menu pada mobile drawer */
        [data-kt-drawer="true"] {
            background: #ffffff !important;
            box-shadow: 0 0 30px rgba(0,0,0,0.05);
        }

        /* Animasi fade in untuk hero content */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 2.8rem;
            }
            .hero-logo {
                max-width: 240px;
            }
            .menu-link.nav-link {
                padding: 0.5rem 1rem !important;
            }
        }

        @media (max-width: 575.98px) {
            .hero-title {
                font-size: 2.2rem;
            }
            .hero-subtitle {
                font-size: 1rem;
                padding: 0 1rem;
            }
            .btn-modern-primary {
                padding: 0.7rem 1.8rem;
                font-size: 0.9rem;
            }
            .btn-modern-outline {
                padding: 0.4rem 1.2rem;
                font-size: 0.85rem;
            }
            .hero-logo {
                max-width: 180px;
            }
        }

        /* Efek hover pada logo */
        .logo-sticky {
            transition: transform 0.3s ease;
        }
        .logo-sticky:hover {
            transform: scale(1.03);
        }

        /* Spacer */
        .spacer {
            height: 2rem;
        }
    </style>
    <script>if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
</head>
<body id="kt_body" style="background: #ffffff;">
    <!-- Theme mode setup (dipaksa light) -->
    <script>var defaultThemeMode = "light"; var themeMode = "light"; document.documentElement.setAttribute("data-bs-theme", "light");</script>

    <div class="d-flex flex-column flex-root">
        <!-- Header dengan latar putih -->
        <div class="modern-header" id="home">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between py-2 py-lg-3">
                    <!-- Logo dan toggle mobile -->
                    <div class="d-flex align-items-center">
                        <button class="btn btn-icon btn-active-light me-3 d-flex d-lg-none" id="kt_landing_menu_toggle">
                            <i class="ki-outline ki-abstract-14 fs-1"></i>
                        </button>
                        <a href="sign-up">
                            <img alt="Logo Rumah Inovasi" src="template.demo6/demo6/assets/media/logos/low.png" style="height: 22px;" class="logo-sticky" />
                        </a>
                    </div>

                    <!-- Menu desktop -->
                    <div class="d-none d-lg-block" id="kt_header_nav_wrapper">
                        <div class="menu menu-lg-row menu-rounded fw-semibold fs-6" id="kt_landing_menu">
                            <div class="menu-item"><a class="menu-link nav-link" href="#achievements">Kekayaan Intelektual</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#team">KKN Award</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#portfolio">Jurnal Inovasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#dokumentasi">Dokumentasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#pengumuman">Pengumuman</a></div>
                        </div>
                    </div>

                    <!-- Login dan mobile menu -->
                    <div class="d-flex align-items-center gap-3">
                        <a href="/sign-in" class="btn btn-modern-outline d-none d-sm-inline-block">Login</a>
                        <a href="/sign-in" class="btn btn-sm btn-outline-secondary d-inline-block d-sm-none">Login</a>

                        <!-- Mobile drawer -->
                        <div class="d-lg-none">
                            <div data-kt-drawer="true" data-kt-drawer-name="landing-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="280px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_landing_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}">
                                <div class="menu menu-column p-4">
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#achievements">Kekayaan Intelektual</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#team">KKN Award</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#portfolio">Jurnal Inovasi</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#dokumentasi">Dokumentasi</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#pengumuman">Pengumuman</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Section - Latar Putih Bersih -->
        <div class="hero-section">
            <div class="container">
                <div class="hero-content text-center py-4 py-md-6">
                    <!-- Logo besar dengan animasi float -->
                    <img src="template.demo6/demo6/assets/media/logos/rmh.png" alt="Rumah Inovasi Magetan" class="hero-logo img-fluid" />

                    <!-- Subtitle -->
                    <!-- Tombol Pendaftaran -->
                    <div class="mt-4">
                        <a href="index.html" class="btn btn-modern-primary px-5 py-3 fs-5 fw-bold">
                            PENDAFTARAN <i class="ki-outline ki-black-right ms-2 fs-3"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spacer tipis untuk konten selanjutnya -->
        <div class="spacer"></div>
    </div>

    <!-- Scripts -->
    <script>var hostUrl = "assets/";</script>
    <script src="template.demo6/demo6/assets/plugins/global/plugins.bundle.js"></script>
    <script src="template.demo6/demo6/assets/js/scripts.bundle.js"></script>
    <script src="template.demo6/demo6/assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
    <script src="template.demo6/demo6/assets/js/custom/landing.js"></script>

    <!-- Inisialisasi drawer untuk mobile -->
    <script>
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
