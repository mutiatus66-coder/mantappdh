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

    {{-- Inisialisasi tema sebelum render --}}
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

            {{-- ===== SIDEBAR ===== --}}
            @include('partials.sidebar')

            {{-- ===== MAIN WRAPPER ===== --}}
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

                {{-- Header --}}
                @include('partials.header')

                {{-- Toolbar --}}
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

                {{-- Content --}}
                <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
                    <div id="kt_content_container" class="container-fluid">
                        @yield('content')
                    </div>
                </div>

                {{-- Footer --}}
                @include('partials.footer')

            </div>
        </div>
    </div>

    {{-- Scroll-to-top --}}
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <i class="ki-outline ki-arrow-up"></i>
    </div>

    {{-- Scripts --}}
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
                const isActive = linkPath === '/'
                    ? currentPath === '/'
                    : currentPath.startsWith(linkPath);
                link.classList.toggle('active', isActive);
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
        (function () {
    const CONTENT_ID = 'kt_content_container';

    function navigate(url, push) {
        // Tampilkan spinner ringan
        const el = document.getElementById(CONTENT_ID);
        el.style.opacity = '0.4';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            // Parse HTML respons, ambil #kt_content_container-nya saja
            const parser  = new DOMParser();
            const doc     = parser.parseFromString(html, 'text/html');
            const newEl   = doc.getElementById(CONTENT_ID);

            if (newEl) {
                el.innerHTML    = newEl.innerHTML;
                el.style.opacity = '1';
            } else {
                // Fallback: full reload kalau struktur beda
                window.location.href = url;
                return;
            }

            if (push) history.pushState({ url }, '', url);

            // Re-init DataTables / Metronic widgets di konten baru
            reinitWidgets();

            // Update active menu
            highlightMenu(url);

            // Update judul tab
            const title = doc.querySelector('title');
            if (title) document.title = title.textContent;
        })
        .catch(() => { window.location.href = url; });
    }

    function reinitWidgets() {
        // Reinit KTComponents jika tersedia
        if (window.KTComponents) KTComponents.init();
        // Reinit DataTables
        if (window.KTDatatablesServerSide) KTDatatablesServerSide.init?.();
        // Re-run scripts yang di-push via @stack('scripts')
        // (lihat catatan di bawah)
    }

    function highlightMenu(url) {
        const currentPath = new URL(url, location.origin).pathname.replace(/\/+$/, '') || '/';
        document.querySelectorAll('#ri-sidebar-nav .ri-menu-item').forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;
            const linkPath = href.replace(/\/+$/, '') || '/';
            const isActive = linkPath === '/'
                ? currentPath === '/'
                : currentPath.startsWith(linkPath);
            link.classList.toggle('active', isActive);
        });
    }

    // Intercept semua klik link internal
    document.addEventListener('click', function (e) {
        const a = e.target.closest('a[href]');
        if (!a) return;

        const href = a.getAttribute('href');

        // Skip: external, hash, target blank, file download
        if (!href
            || href.startsWith('http') && !href.startsWith(location.origin)
            || href.startsWith('#')
            || href.startsWith('javascript')
            || a.target === '_blank'
            || a.hasAttribute('download')
        ) return;

        e.preventDefault();
        navigate(href, true);
    });

    // Handle back/forward
    window.addEventListener('popstate', function (e) {
        navigate(e.state?.url || location.href, false);
    });

    // Simpan state halaman pertama
    history.replaceState({ url: location.href }, '', location.href);
})();
window.ajaxForm = function (formEl, modalEl) {
    formEl.addEventListener('submit', function (e) {
        e.preventDefault();

        const form    = e.target;
        const method  = (form.querySelector('[name="_method"]')?.value || form.method).toUpperCase();
        const url     = form.action;

        // Kumpulkan data (support file upload)
        let body;
        const hasFile = [...form.querySelectorAll('input[type=file]')]
                        .some(f => f.files && f.files.length > 0);
        if (hasFile) {
            body = new FormData(form);
        } else {
            body = new FormData(form);
        }

        // Disable tombol submit
        const submitBtn = form.querySelector('[type=submit]');
        const origText  = submitBtn?.innerHTML;
        if (submitBtn) {
            submitBtn.disabled  = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
        }

        fetch(url, {
            method : 'POST',        // Laravel butuh POST, _method di-spoof
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body   : body,
        })
        .then(function (res) {
            // Laravel redirect → ikuti redirect, ambil HTML halaman tujuan
            return fetch(res.url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
        })
        .then(r => r.text())
        .then(function (html) {
            // Tutup modal
            if (modalEl) {
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
            }

            // Swap konten
            const parser = new DOMParser();
            const doc    = parser.parseFromString(html, 'text/html');
            const newEl  = doc.getElementById('kt_content_container');
            const curEl  = document.getElementById('kt_content_container');

            if (newEl && curEl) {
                curEl.style.opacity = '0.4';
                curEl.innerHTML     = newEl.innerHTML;
                curEl.style.opacity = '1';

                // Re-eksekusi <script> di konten baru
                curEl.querySelectorAll('script').forEach(function (old) {
                    const s = document.createElement('script');
                    [...old.attributes].forEach(a => s.setAttribute(a.name, a.value));
                    s.textContent = old.textContent;
                    old.replaceWith(s);
                });

                // Update URL di address bar
                history.pushState({ url: res.url }, '', res.url);
            }
        })
        .catch(function () {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        })
        .finally(function () {
            if (submitBtn) {
                submitBtn.disabled  = false;
                submitBtn.innerHTML = origText;
            }
        });
    });
};
    </script>

    @stack('scripts')
</body>
</html>