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
  <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" />
  <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
  <link href="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
  <link href="{{ asset('template.demo6/demo6/assets/css/style.bundle.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet">

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

      {{-- ══════════════════════════════════════════════════
           SIDEBAR
      ══════════════════════════════════════════════════ --}}
      @include('partials.sidebar')
      {{-- end sidebar --}}

      {{-- ══════════════════════════════════════════════════
           MAIN WRAPPER
      ══════════════════════════════════════════════════ --}}
      <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

        {{-- ── Header ── --}}
        @include('partials.header')
        {{-- end header --}}

        {{-- ── Toolbar ── --}}
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
        {{-- end toolbar --}}

        {{-- ── Content ── --}}
        <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
          <div id="kt_content_container" class="container-fluid">

            @auth
              {{-- Tampilkan @yield jika child view punya section 'content', otherwise welcome --}}
              @if($__env->hasSection('content'))
              {{-- Banner Login As --}}
                @if(session()->has('admin_original_id'))
                <div class="d-flex align-items-center justify-content-between px-4 py-2 mb-3"
                    style="background:#fff3cd; border:1px solid #ffc107; border-radius:8px; color:#856404;">
                    <div>
                        <i class="bi bi-person-fill-gear me-2"></i>
                        Sedang login sebagai <strong>{{ Auth::user()->nama }}</strong>
                        ({{ Auth::user()->hak_akses }})
                    </div>
                    <a href="{{ route('user.login-back') }}" class="btn btn-sm btn-warning ms-3">
                        <i class="bi bi-arrow-return-left me-1"></i> Kembali ke Akun Admin
                    </a>
                </div>
                @endif
                @yield('content')
              @else
                <div class="p-6">
                  <h2 class="fw-bold">Selamat Datang, {{ auth()->user()->nama }}</h2>
                  <p class="text-muted">Panel Admin Rumah Inovasi Magetan</p>
                </div>
              @endif
            @else
              {{-- Fallback: harusnya tidak terjadi karena route sudah dilindungi auth --}}
              <div class="p-6">
                <p class="text-danger">Sesi Anda telah berakhir. <a href="{{ route('login') }}">Login kembali</a>.</p>
              </div>
            @endauth

          </div>
        </div>
        {{-- end content --}}

        {{-- ── Footer ── --}}
        @include('partials.footer')
        {{-- end footer --}}

      </div>
      {{-- end main wrapper --}}

    </div>
  </div>

  {{-- Drawer Activity Logs --}}
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

  @include('partials.history')

  <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
  </div>

  {{-- JS Bundles --}}
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
    // ── Theme switcher highlight ──────────────────────────────────────────────
    (function () {
      function updateThemeMenuHighlight() {
        var stored = localStorage.getItem("data-bs-theme") || "light";
        document.querySelectorAll('[data-kt-element="mode"]').forEach(function (el) {
          el.classList.toggle("active", el.getAttribute("data-kt-value") === stored);
        });
      }
      document.addEventListener("DOMContentLoaded", updateThemeMenuHighlight);
      document.addEventListener("click", function (e) {
        if (e.target.closest('[data-kt-element="mode"]')) setTimeout(updateThemeMenuHighlight, 50);
      });
    })();

    // ── Sidebar active state ──────────────────────────────────────────────────
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

    // ── Background warna per tema ─────────────────────────────────────────────
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
        attributes: true, attributeFilter: ['data-bs-theme']
      });
    })();
  </script>

  @stack('4 scripts')
</body>
</html>   