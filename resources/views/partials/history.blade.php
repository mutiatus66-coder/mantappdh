{{-- ══════════════════════════════════════════════════
     HISTORY PANEL — Sidebar kanan riwayat halaman
══════════════════════════════════════════════════ --}}

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

<style>
  .history-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 2px;
  }
  .history-action {
    font-size: 0.65rem;
    font-weight: 600;
    padding: 1px 8px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: .02em;
    line-height: 1.5;
    white-space: nowrap;
  }
</style>

<script>
(function () {

    const MAX_HISTORY = 30;
    const KEY = 'ri_page_history';

    // Label & warna badge per jenis aksi
    const ACTIONS = {
        index : { label: 'Index',  bg: '#e5e7eb', fg: '#374151' },
        tambah: { label: 'Tambah', bg: '#dcfce7', fg: '#15803d' },
        edit  : { label: 'Edit',   bg: '#fef3c7', fg: '#b45309' },
        hapus : { label: 'Hapus',  bg: '#fee2e2', fg: '#b91c1c' },
        detail: { label: 'Detail', bg: '#dbeafe', fg: '#1d4ed8' },
    };

    // ── Helpers ──────────────────────────────────────────────────────────────
    function getHistory() {
        try { return JSON.parse(localStorage.getItem(KEY)) || []; }
        catch { return []; }
    }

    function saveHistory(arr) {
        localStorage.setItem(KEY, JSON.stringify(arr));
    }

    function getActionMeta(action) {
        return ACTIONS[action] || ACTIONS.index;
    }

    function getPageTitle(path) {
        if (path === '/' || path === '') return 'Dashboard';

        const heading = document.querySelector('#kt_content h3, #kt_content h2, #kt_content .ec-title, #kt_content .rv-page-title');
        let title = heading ? heading.innerText.trim() : document.title;

        // Bersihkan kata-kata generik & nama aplikasi agar judul tetap singkat
        title = title
            .replace(/Rumah Inovasi Magetan/gi, '')
            .replace(/Panel Admin/gi, '')
            .replace(/\b(halaman|index|daftar|data)\b/gi, '')
            .replace(/[-|/]+/g, ' ')
            .replace(/\s{2,}/g, ' ')
            .trim();

        return title || 'Halaman';
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

    // ── Rekam entri riwayat ──────────────────────────────────────────────────
    function recordEntry(action, customTitle) {
        const url   = window.location.href;
        const path  = window.location.pathname;
        const title = customTitle || getPageTitle(path);
        const now   = Date.now();

        let hist = getHistory();

        if (action === 'index') {
            // Ganti entri "Index" lama untuk URL yang sama, tapi pertahankan
            // entri Tambah/Edit/Hapus yang sudah tercatat sebelumnya
            hist = hist.filter(h => !(h.url === url && (h.action || 'index') === 'index'));
        }

        hist.unshift({ url, path, title, action: action || 'index', time: now, icon: getPageIcon(path) });

        if (hist.length > MAX_HISTORY) hist = hist.slice(0, MAX_HISTORY);

        saveHistory(hist);
        renderHistory();
    }

    function recordPage() {
        recordEntry('index');
    }

    // Dipanggil dari JS controller lain saat user Tambah / Edit / Hapus data.
    // Contoh: logHistory('tambah', 'Sub Event'), logHistory('edit', 'User'),
    //         logHistory('hapus', 'Penilai')
    window.logHistory = function (action, label) {
        recordEntry(action, label);
    };

    // ── Render list riwayat ──────────────────────────────────────────────────
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
            const meta      = getActionMeta(h.action);

            const a = document.createElement('a');
            a.href = h.url;
            a.className = 'history-item' + (isCurrent ? ' current' : '');
            a.innerHTML = `
                <div class="history-icon"><i class="bi ${h.icon || 'bi-file-earmark'}"></i></div>
                <div style="flex:1; min-width:0;">
                    <div class="history-title">${h.title}</div>
                    <div class="history-meta">
                        <span class="history-action" style="background:${meta.bg}; color:${meta.fg};">${meta.label}</span>
                        <span class="history-time">${timeAgo(h.time)}</span>
                    </div>
                </div>
            `;
            list.appendChild(a);
        });
    };

    // ── Toggle panel ─────────────────────────────────────────────────────────
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

    // ── Jalankan setelah halaman selesai dimuat ─────────────────────────────
    window.addEventListener('load', function () {
        // Tunggu sedikit agar judul konten sudah di-render
        setTimeout(recordPage, 300);
        renderHistory();
    });

})();
</script>