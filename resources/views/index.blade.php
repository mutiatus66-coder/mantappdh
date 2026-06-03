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
  <link rel="shortcut icon" href="{{ asset('template.demo6/demo6/assets/media/logos/mgt.png') }}" />

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
  <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" />
  <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
  <link href="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
  <link href="{{ asset('template.demo6/demo6/assets/css/style.bundle.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @push('styles')
  <link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">
  @endpush

  <script>if (window.top !== window.self) { window.top.location.replace(window.self.location.href); }</script>
  @stack('styles')
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed">

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

      <!-- sidebar -->
      <div id="kt_aside"
        class="aside pb-5 pt-5 pt-lg-0"
        data-kt-drawer="true"
        data-kt-drawer-name="aside"
        data-kt-drawer-activate="{default: true, lg: false}"
        data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'260px', '300px': '260px'}"
        data-kt-drawer-direction="start"
        data-kt-drawer-toggle="#kt_aside_mobile_toggle">

        <!-- logo -->
        <div class="aside-logo" id="kt_aside_logo">
          <a href="/" class="d-flex align-items-center gap-3">
            <img alt="Logo" src="{{ asset('img/bulb.png') }}" class="h-60px logo" />
            <div class="aside-logo-text">
              <div class="aside-logo-title">Rumah Inovasi</div>
              <div class="aside-logo-subtitle">Magetan</div>
            </div>
          </a>
        </div>

        <!--menu  scroll -->
        <div class="aside-menu flex-column-fluid" id="kt_aside_menu">
          <nav id="ri-sidebar-nav">

            <a class="ri-menu-item" href="/">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <rect x="3" y="3" width="7" height="7" rx="1"/>
                  <rect x="14" y="3" width="7" height="7" rx="1"/>
                  <rect x="3" y="14" width="7" height="7" rx="1"/>
                  <rect x="14" y="14" width="7" height="7" rx="1"/>
                </svg>
              </span>
              <span class="ri-menu-label">Dashboard</span>
            </a>

            <div class="ri-divider"></div>

            <span class="ri-section-label">Master</span>

            <a class="ri-menu-item" href="/event">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <rect x="3" y="4" width="18" height="18" rx="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </span>
              <span class="ri-menu-label">Event</span>
            </a>

            <a class="ri-menu-item" href="/sub-event">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <line x1="8" y1="6" x2="21" y2="6"/>
                  <line x1="8" y1="12" x2="21" y2="12"/>
                  <line x1="8" y1="18" x2="21" y2="18"/>
                  <line x1="3" y1="6" x2="3.01" y2="6"/>
                  <line x1="3" y1="12" x2="3.01" y2="12"/>
                  <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
              </span>
              <span class="ri-menu-label">Sub Event</span>
            </a>

            <a class="ri-menu-item" href="/bidang">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <rect x="3" y="3" width="18" height="18" rx="2"/>
                  <line x1="3" y1="9" x2="21" y2="9"/>
                  <line x1="3" y1="15" x2="21" y2="15"/>
                  <line x1="9" y1="3" x2="9" y2="21"/>
                </svg>
              </span>
              <span class="ri-menu-label">Bidang</span>
            </a>

            <a class="ri-menu-item" href="/user">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </span>
              <span class="ri-menu-label">User</span>
            </a>

            <a class="ri-menu-item" href="/penilai">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </span>
              <span class="ri-menu-label">Penilai</span>
            </a>

            <a class="ri-menu-item" href="/pengumuman">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z"/>
                  <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
              </span>
              <span class="ri-menu-label">Pengumuman</span>
            </a>

            <div class="ri-divider"></div>

            <span class="ri-section-label">Indikator</span>

            <a class="ri-menu-item" href="/indikator/tahap-1">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
              </span>
              <span class="ri-menu-label">Indikator Tahap 1</span>
            </a>

            <a class="ri-menu-item" href="/indikator/tahap-2">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <line x1="8" y1="6" x2="21" y2="6"/>
                  <line x1="8" y1="12" x2="21" y2="12"/>
                  <line x1="8" y1="18" x2="21" y2="18"/>
                  <line x1="3" y1="6" x2="3.01" y2="6"/>
                  <line x1="3" y1="12" x2="3.01" y2="12"/>
                  <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
              </span>
              <span class="ri-menu-label">Indikator Tahap 2</span>
            </a>

            <div class="ri-divider"></div>

            <span class="ri-section-label">Inovasi</span>

            <a class="ri-menu-item" href="/inovasi/riwayat">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <polyline points="1 4 1 10 7 10"/>
                  <path d="M3.51 15a9 9 0 1 0 .49-4.44"/>
                </svg>
              </span>
              <span class="ri-menu-label">Riwayat</span>
            </a>

            <a class="ri-menu-item" href="/inovasi/rekap-nilai">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <line x1="18" y1="20" x2="18" y2="10"/>
                  <line x1="12" y1="20" x2="12" y2="4"/>
                  <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
              </span>
              <span class="ri-menu-label">Rekap Nilai</span>
            </a>

            <div class="ri-divider"></div>

            <span class="ri-section-label">Penilaian</span>

            <a class="ri-menu-item" href="/penilaian/tahap-1">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </span>
              <span class="ri-menu-label">Penilaian Tahap 1</span>
            </a>

            <a class="ri-menu-item" href="/penilaian/tahap-2">
              <span class="ri-icon">
                <svg viewBox="0 0 24 24">
                  <line x1="18" y1="20" x2="18" y2="10"/>
                  <line x1="12" y1="20" x2="12" y2="4"/>
                  <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
              </span>
              <span class="ri-menu-label">Penilaian Tahap 2</span>
            </a>

          </nav>
        </div>

      </div>
      <!-- end sidebar -->

      <!-- wrapper main -->
      <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

        <!-- header -->
        <div id="kt_header" class="header align-items-stretch">
          <div class="container-fluid d-flex align-items-stretch justify-content-between">

            <div class="d-flex align-items-center d-lg-none ms-n1 me-2" title="Show aside menu">
              <div class="btn btn-icon btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-1"></i>
              </div>
            </div>

            <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
              <a href="/" class="d-lg-none">
                <img alt="Logo" src="{{ asset('template.demo6/demo6/assets/media/logos/rmh.png') }}" class="h-25px" />
              </a>
            </div>

            <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
              <div class="d-flex align-items-stretch" id="kt_header_nav"></div>
              <p class="mb-0 flex-grow-1 header-welcome-text">..... &nbsp;</p>
              <div class="d-flex align-items-stretch flex-shrink-0">

                <!-- ubah tema -->
                <div class="d-flex align-items-center ms-1 ms-lg-3">
                  <a href="#"
                    class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
                    data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                    data-kt-menu-attach="parent"
                    data-kt-menu-placement="bottom-end">
                    <i class="ki-outline ki-night-day theme-light-show fs-1"></i>
                    <i class="ki-outline ki-moon theme-dark-show fs-1"></i>
                  </a>
                  <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                      data-kt-menu="true" data-kt-element="theme-mode-menu">
                    <div class="menu-item px-3 my-0">
                      <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                        <span class="menu-icon" data-kt-element="icon"><i class="ki-outline ki-night-day fs-2"></i></span>
                        <span class="menu-title">Light</span>
                      </a>
                    </div>
                    <div class="menu-item px-3 my-0">
                      <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                        <span class="menu-icon" data-kt-element="icon"><i class="ki-outline ki-moon fs-2"></i></span>
                        <span class="menu-title">Dark</span>
                      </a>
                    </div>
                    <div class="menu-item px-3 my-0">
                      <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                        <span class="menu-icon" data-kt-element="icon"><i class="ki-outline ki-screen fs-2"></i></span>
                        <span class="menu-title">System</span>
                      </a>
                    </div>
                  </div>
                </div>

                <!-- menu user -->
                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                  <div class="cursor-pointer symbol symbol-30px symbol-md-40px"
                    data-kt-menu-trigger="click"
                    data-kt-menu-attach="parent"
                    data-kt-menu-placement="bottom-end">
                    <img alt="Avatar" src="{{ asset('template.demo6/demo6/assets/media/avatars/blank.png') }}" />
                  </div>
                  <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                    <div class="menu-item px-3">
                      <div class="menu-content d-flex align-items-center px-3">
                        <div class="symbol symbol-50px me-5">
                          <img alt="Avatar" src="{{ asset('template.demo6/demo6/assets/media/avatars/blank.png') }}" />
                        </div>
                        <div class="d-flex flex-column">
                          <div class="fw-bold d-flex align-items-center fs-5">Max Smith
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">Pro</span>
                          </div>
                          <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">max@kt.com</a>
                        </div>
                      </div>
                    </div>
                    <div class="separator my-2"></div>
                    <div class="menu-item px-5">
                      <a href="#" class="menu-link px-5">My Profile</a>
                    </div>
                    <div class="separator my-2"></div>
                    <div class="menu-item px-5 my-1">
                      <a href="#" class="menu-link px-5">Account Settings</a>
                    </div>
                    <div class="menu-item px-5">
                      <a href="/" class="menu-link px-5">Sign Out</a>
                    </div>
                  </div>
                </div>

                <div class="d-flex align-items-center d-lg-none ms-2 me-n2" title="Show header menu">
                  <div class="btn btn-icon btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
                    <i class="ki-outline ki-burger-menu-2 fs-1"></i>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
        <!-- end header -->

        <!-- toolbar -->
        <div class="toolbar py-2" id="kt_toolbar">
          <div id="kt_toolbar_container" class="container-fluid d-flex align-items-center">
            <div class="flex-grow-1 flex-shrink-0 me-5">
              <div data-kt-swapper="true"
                data-kt-swapper-mode="prepend"
                data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
              </div>
            </div>
          </div>
        </div>
        <!-- end toolbar -->

        <!-- content -->
        <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
          <div id="kt_content_container" class="container-fluid">
              @if(!isset($dummy))
                  <div class="p-6">
                      <h2 class="fw-bold">Selamat Datang, {{ auth()->user()->nama }}</h2>
                      <p>Panel Admin Rumah Inovasi Magetan</p>
                  </div>
              @else
                  @yield('content')
              @endif
          </div>
        </div>
        <!-- end content -->

        <!-- footer -->
        <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
          <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="text-gray-900 order-2 order-md-1">
              <span class="text-muted fw-semibold me-1">2026&copy;</span>
              <a href="#" target="_blank" class="text-gray-800 text-hover-primary">KOMINFO</a>
            </div>
            <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
              <li class="menu-item"><a href="#" class="menu-link px-2">About</a></li>
              <li class="menu-item"><a href="#" class="menu-link px-2">Support</a></li>
            </ul>
          </div>
        </div>
        <!-- end footer -->

      </div>
      <!-- end main wrapper -->

    </div>
  </div>

  <!-- drawer -->
  <div id="kt_activities" class="bg-body"
    data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true"
    data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'lg': '900px'}"
    data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activities_toggle"
    data-kt-drawer-close="#kt_activities_close">
    <div class="card shadow-none border-0 rounded-0">
      <div class="card-header" id="kt_activities_header">
        <h3 class="card-title fw-bold text-gray-900">Activity Logs</h3>
        <div class="card-toolbar">
          <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
            <i class="ki-outline ki-cross fs-1"></i>
          </button>
        </div>
      </div>
      <div class="card-body position-relative" id="kt_activities_body">
        <div id="kt_activities_scroll" class="position-relative scroll-y me-n5 pe-5"
          data-kt-scroll="true" data-kt-scroll-height="auto"
          data-kt-scroll-wrappers="#kt_activities_body"
          data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
          data-kt-scroll-offset="5px"></div>
      </div>
      <div class="card-footer py-5 text-center" id="kt_activities_footer">
        <a href="#" class="btn btn-bg-body text-primary">
          View All Activities <i class="ki-outline ki-arrow-right fs-3 text-primary"></i>
        </a>
      </div>
    </div>
  </div>

  <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
  </div>

  <!-- JS bundles -->
  <script>var hostUrl = "{{ asset('template.demo6/demo6/assets/') }}";</script>
  <script src="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/js/scripts.bundle.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
  <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
  <script src="{{ asset('template.demo6/demo6/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/js/widgets.bundle.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/js/custom/widgets.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/js/custom/apps/chat/chat.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/js/custom/utilities/modals/create-campaign.js') }}"></script>
  <script src="{{ asset('template.demo6/demo6/assets/js/custom/utilities/modals/users-search.js') }}"></script>

  <script>
    (function () {
  function updateThemeMenuHighlight() {
    var stored = localStorage.getItem("data-bs-theme") || "light";
    document.querySelectorAll('[data-kt-element="mode"]').forEach(function (el) {
      var val = el.getAttribute("data-kt-value");
      el.classList.toggle("active", val === stored);
    });
  }
  document.addEventListener("DOMContentLoaded", updateThemeMenuHighlight);
  document.addEventListener("click", function (e) {
    var el = e.target.closest('[data-kt-element="mode"]');
    if (!el) return;
    setTimeout(updateThemeMenuHighlight, 50);
  });
})();
    document.addEventListener('DOMContentLoaded', function () {
      var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
      document.querySelectorAll('#ri-sidebar-nav a.ri-menu-item').forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href || href === '#') return;
        var linkPath = href.replace(/\/+$/, '') || '/';
        var isActive = linkPath === '/'
          ? currentPath === '/'
          : currentPath === linkPath || currentPath.startsWith(linkPath + '/');
        link.classList.toggle('active', isActive);
      });
    });

    (function () {
      var lightBg = '#F9FBFF';
      var darkBg  = '#1C2333';
      function applyBg() {
        var theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        var bg    = theme === 'dark' ? darkBg : lightBg;
        ['kt_wrapper', 'kt_content', 'kt_toolbar'].forEach(function (id) {
          var el = document.getElementById(id);
          if (el) el.style.backgroundColor = bg;
        });
      }
      applyBg();
      new MutationObserver(applyBg).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
      });
    })();
  </script>

  @stack('scripts')
</body>
</html>