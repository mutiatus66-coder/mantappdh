<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rumah Inovasi Magetan - Panel Admin</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:locale"    content="id_ID" />
    <meta property="og:type"      content="website" />
    <meta property="og:title"     content="Rumah Inovasi Magetan - Panel Admin" />
    <meta property="og:site_name" content="Rumah Inovasi Magetan" />

    <link rel="shortcut icon" href="{{ asset('template.demo6/demo6/assets/media/logos/mgt.png') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    {{-- Metronic --}}
    <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('template.demo6/demo6/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('template.demo6/demo6/assets/css/style.bundle.css') }}" rel="stylesheet" />

    {{-- Tambahan --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet" />

    {{-- Cegah iframe --}}
    <script>if (window.top !== window.self) { window.top.location.replace(window.self.location.href); }</script>

    @stack('styles')
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed">

    <script>
        (function () {
            var stored = localStorage.getItem('data-bs-theme') || 'light';
            var mode   = stored === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : stored;
            document.documentElement.setAttribute('data-bs-theme', mode);
        })();
    </script>

    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">

            @include('partials.sidebar')

            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

                @include('partials.header')

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

                <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
                    <div id="kt_content_container" class="container-fluid">
                        @yield('content')
                    </div>
                </div>

                @include('partials.footer')

            </div>
        </div>
    </div>

    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-outline ki-arrow-up"></i>
    </div>

    <script>var hostUrl = "{{ asset('template.demo6/demo6/assets/') }}";</script>
    <script src="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('template.demo6/demo6/assets/js/scripts.bundle.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Active menu highlight ──
            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            document.querySelectorAll('#ri-sidebar-nav .ri-menu-item').forEach(function (link) {
                const href = link.getAttribute('href');
                if (!href) return;
                const linkPath = href.replace(/\/+$/, '') || '/';
                link.classList.toggle('active',
                    linkPath === '/' ? currentPath === '/' : currentPath.startsWith(linkPath)
                );
            });

            // ── Sync content background dengan tema ──
            function applyThemeBg() {
                const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                const bg    = theme === 'dark' ? '#1C2333' : '#F9FBFF';
                ['kt_wrapper', 'kt_content', 'kt_toolbar'].forEach(function (id) {
                    const el = document.getElementById(id);
                    if (el) el.style.backgroundColor = bg;
                });
            }
            applyThemeBg();
            new MutationObserver(applyThemeBg).observe(
                document.documentElement,
                { attributes: true, attributeFilter: ['data-bs-theme'] }
            );

        });
    </script>

    @stack('scripts')
</body>
</html>