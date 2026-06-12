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

            // â”€â”€ Active menu highlight â”€â”€
            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            document.querySelectorAll('#ri-sidebar-nav .ri-menu-item').forEach(function (link) {
                const href = link.getAttribute('href');
                if (!href) return;
                const linkPath = href.replace(/\/+$/, '') || '/';
                link.classList.toggle('active',
                    linkPath === '/' ? currentPath === '/' : currentPath.startsWith(linkPath)
                );
            });

            // â”€â”€ Sync content background dengan tema â”€â”€
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


{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     HISTORY PANEL â€” Sidebar kanan riwayat halaman
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}

{{-- Overlay --}}
<div id="historyOverlay"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:1040;"
     onclick="closeHistoryPanel()"></div>

{{-- Panel --}}
<div id="historyPanel"
     style="position:fixed; top:0; right:-380px; width:360px; height:100vh;
            background:var(--ri-card-bg, #fff); z-index:1050;
            box-shadow:-4px 0 24px rgba(0,0,0,0.15);
            transition:right .28s cubic-bezier(.4,0,.2,1);
            display:flex; flex-direction:column; border-radius:12px 0 0 12px;">

    {{-- Header panel --}}
    <div style="padding:20px 20px 14px; border-bottom:1px solid var(--ri-table-border,#e5e7eb);
                display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h5 style="margin:0; font-weight:700; font-size:1rem; color:var(--ri-text-primary);">
                <i class="bi bi-clock-history me-2" style="color:#2563eb;"></i>Riwayat Halaman
            </h5>
            <p style="margin:0; font-size:0.75rem; color:var(--ri-text-muted);">
                Halaman yang baru-baru ini Anda kunjungi
            </p>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <button onclick="clearHistory()"
                    title="Hapus semua riwayat"
                    style="background:none; border:1px solid #e5e7eb; border-radius:6px;
                           padding:4px 8px; cursor:pointer; font-size:0.75rem;
                           color:var(--ri-text-muted);">
                <i class="bi bi-trash3"></i>
            </button>
            <button onclick="closeHistoryPanel()"
                    style="background:none; border:none; cursor:pointer; font-size:1.2rem;
                           color:var(--ri-text-muted); line-height:1; padding:4px;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    {{-- List riwayat --}}
    <div id="historyList"
         style="flex:1; overflow-y:auto; padding:12px 16px;">
        <p id="historyEmpty"
           style="text-align:center; color:var(--ri-text-muted); padding:40px 0; font-size:0.875rem;">
            <i class="bi bi-inbox d-block mb-2" style="font-size:1.5rem;"></i>
            Belum ada riwayat halaman
        </p>
    </div>

</div>


    @stack('scripts')

<script>
(function () {

    const MAX_HISTORY = 30;
    const KEY = 'ri_page_history';

    // â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function getHistory() {
        try { return JSON.parse(localStorage.getItem(KEY)) || []; }
        catch { return []; }
    }

    function saveHistory(arr) {
        localStorage.setItem(KEY, JSON.stringify(arr));
    }

    function getPageTitle() {
        // Ambil dari <h3> atau <h2> pertama di konten, fallback ke document.title
        const heading = document.querySelector('#kt_content h3, #kt_content h2, #kt_content .ec-title, #kt_content .rv-page-title');
        if (heading) return heading.innerText.trim();
        return document.title.replace(' - Panel Admin', '').replace('Rumah Inovasi Magetan', '').trim() || 'Halaman';
    }

    function getPageIcon(path) {
        if (path === '/' || path.includes('dashboard')) return 'bi-house';
        if (path.includes('indikator'))  return 'bi-bar-chart-steps';
        if (path.includes('penilaian')) return 'bi-clipboard2-check';
        if (path.includes('inovasi'))   return 'bi-lightbulb';
        if (path.includes('sub-event') || path.includes('event')) return 'bi-calendar-event';
        if (path.includes('penilai'))   return 'bi-people';
        if (path.includes('user'))      return 'bi-person-gear';
        if (path.includes('pengumuman'))return 'bi-megaphone';
        if (path.includes('bidang'))    return 'bi-grid';
        return 'bi-file-earmark';
    }

    function timeAgo(ts) {
        const diff = Math.floor((Date.now() - ts) / 1000);
        if (diff < 60)   return 'Baru saja';
        if (diff < 3600) return Math.floor(diff/60) + ' menit lalu';
        if (diff < 86400)return Math.floor(diff/3600) + ' jam lalu';
        return Math.floor(diff/86400) + ' hari lalu';
    }

    // â”€â”€ Rekam halaman saat ini â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function recordPage() {
        const url   = window.location.href;
        const path  = window.location.pathname;
        const title = getPageTitle();
        const now   = Date.now();

        let hist = getHistory();

        // Hapus entri lama dengan URL yang sama
        hist = hist.filter(h => h.url !== url);

        // Tambahkan di depan
        hist.unshift({ url, path, title, time: now, icon: getPageIcon(path) });

        // Batasi jumlah
        if (hist.length > MAX_HISTORY) hist = hist.slice(0, MAX_HISTORY);

        saveHistory(hist);
        renderHistory();
    }

    // â”€â”€ Render list riwayat â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    window.renderHistory = function () {
        const hist    = getHistory();
        const list    = document.getElementById('historyList');
        const empty   = document.getElementById('historyEmpty');
        const badge   = document.getElementById('historyBadge');
        const current = window.location.href;

        if (!list) return;

        if (hist.length === 0) {
            if (empty) empty.style.display = 'block';
            list.querySelectorAll('.history-item').forEach(e => e.remove());
            if (badge) badge.style.display = 'none';
            return;
        }

        if (empty) empty.style.display = 'none';
        if (badge) { badge.style.display = 'flex'; badge.textContent = hist.length; }

        // Clear lalu render ulang
        list.querySelectorAll('.history-item').forEach(e => e.remove());

        hist.forEach(function (h) {
            const isCurrent = h.url === current;
            const a = document.createElement('a');
            a.href = h.url;
            a.className = 'history-item' + (isCurrent ? ' current' : '');
            a.innerHTML = `
                <div class="history-icon"><i class="bi ${h.icon || 'bi-file-earmark'}"></i></div>
                <div style="flex:1; min-width:0;">
                    <div class="history-title">${h.title}</div>
                    <div class="history-url">${h.path}</div>
                </div>
                <div class="history-time">${timeAgo(h.time)}</div>
            `;
            list.appendChild(a);
        });
    };

    // â”€â”€ Toggle panel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    window.toggleHistoryPanel = function () {
        const panel   = document.getElementById('historyPanel');
        const overlay = document.getElementById('historyOverlay');
        const isOpen  = panel.style.right === '0px';
        if (isOpen) {
            closeHistoryPanel();
        } else {
            renderHistory();
            panel.style.right   = '0px';
            overlay.style.display = 'block';
        }
    };

    window.closeHistoryPanel = function () {
        const panel   = document.getElementById('historyPanel');
        const overlay = document.getElementById('historyOverlay');
        if (panel)   panel.style.right    = '-380px';
        if (overlay) overlay.style.display = 'none';
    };

    window.clearHistory = function () {
        localStorage.removeItem(KEY);
        renderHistory();
    };

    // Tutup panel dengan Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeHistoryPanel();
    });

    // â”€â”€ Jalankan setelah halaman selesai dimuat â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    window.addEventListener('load', function () {
        // Tunggu sedikit agar judul konten sudah di-render
        setTimeout(recordPage, 300);
        renderHistory();
    });

})();

</script>
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

            // â”€â”€ Active menu highlight â”€â”€
            const currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
            document.querySelectorAll('#ri-sidebar-nav .ri-menu-item').forEach(function (link) {
                const href = link.getAttribute('href');
                if (!href) return;
                const linkPath = href.replace(/\/+$/, '') || '/';
                link.classList.toggle('active',
                    linkPath === '/' ? currentPath === '/' : currentPath.startsWith(linkPath)
                );
            });

            // â”€â”€ Sync content background dengan tema â”€â”€
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
