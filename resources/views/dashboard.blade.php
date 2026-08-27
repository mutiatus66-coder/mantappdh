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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" />
    <!-- Global Stylesheets Bundle -->
    <link href="template.demo6/demo6/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="template.demo6/demo6/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="template.demo6/demo6/assets/css/CostumeStyle.css" rel="stylesheet" type="text/css" />
    <script>if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
</head>
<body id="kt_body" style="background: #ffffff;">
    <script>var defaultThemeMode = "light"; var themeMode = "light"; document.documentElement.setAttribute("data-bs-theme", "light");</script>

    <div class="d-flex flex-column flex-root">
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
                            <div class="menu-item"><a class="menu-link nav-link" href="#">Kekayaan Intelektual</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#">KKN Award</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#">Jurnal Inovasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="#">Dokumentasi</a></div>
                            <div class="menu-item"><a class="menu-link nav-link" href="/buletin">Pengumuman</a></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="/sign-in" class="btn btn-login">Login</a>

                        <div class="d-lg-none">
                            <div data-kt-drawer="true" data-kt-drawer-name="landing-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="280px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_landing_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}">
                                <div class="menu menu-column p-4">
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">Kekayaan Intelektual</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">KKN Award</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">Jurnal Inovasi</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="#">Dokumentasi</a></div>
                                    <div class="menu-item"><a class="menu-link nav-link py-3" href="/pengumuman">Pengumuman</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-section">
            <!-- Floating decorative dots -->
            <div class="floating-dot" style="width: 80px; height: 80px; top: 10%; left: -30px; animation-delay: 0s;"></div>
            <div class="floating-dot" style="width: 50px; height: 50px; bottom: 15%; right: 10%; animation-delay: 2s;"></div>
            <div class="floating-dot" style="width: 30px; height: 30px; top: 30%; right: 20%; animation-delay: 1s;"></div>
            <div class="floating-dot" style="width: 60px; height: 60px; bottom: 5%; left: 15%; animation-delay: 3s;"></div>

            <div class="container">
                <div class="hero-content text-center py-4 py-md-6">
                    <img src="template.demo6/demo6/assets/media/logos/rmh.png" alt="Rumah Inovasi Magetan" class="hero-logo" />

                    <div class="mt-4">
                        <a href="sign-up" class="btn btn-register px-5 py-3 fs-5 fw-bold">
                            PENDAFTARAN →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="container">
                <p>&copy; Copyright <strong>Dinas Kominfo.</strong> All rights reserved.</p>
                <p> Develop by Dinas Kominfo Magetan Development</p>
            </div>
        </div>
    </div>

    <script>var hostUrl = "assets/";</script>
    <script src="template.demo6/demo6/assets/plugins/global/plugins.bundle.js"></script>
    <script src="template.demo6/demo6/assets/js/scripts.bundle.js"></script>
    <script src="template.demo6/demo6/assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
    <script src="template.demo6/demo6/assets/js/custom/landing.js"></script>

    <script>
        document.getElementById('currentYear').innerText = new Date().getFullYear();

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
