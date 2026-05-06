<!DOCTYPE html>
<html lang="en">

<head>
  <title>Rumah Inovasi Magetan - Panel Admin | Penilai</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="Rumah Inovasi Magetan - Panel Admin" />
  <meta property="og:url" content="https://rumahinovasi.com/admin" />
  <meta property="og:site_name" content="Rumah Inovasi Magetan" />
  <link rel="shortcut icon" href="template.demo6/demo6/assets/media/logos/mgt.png" />

  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

  <!-- Vendor Stylesheets -->
  <link href="template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" />
  <link href="template.demo6/demo6/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" />

  <!-- Global Stylesheets -->
  <link href="template.demo6/demo6/assets/plugins/global/plugins.bundle.css" rel="stylesheet" />
  <link href="template.demo6/demo6/assets/css/style.bundle.css" rel="stylesheet" />

  <style>
    /* ========== SIDEBAR SCROLLABLE ========== */
    #kt_aside,
    .aside {
      width: 260px !important;
      min-width: 260px !important;
      max-width: 260px !important;
      background-color: #1b84ff !important;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      flex-shrink: 0;
    }

    #kt_aside_logo {
      background-color: #1b84ff !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      min-height: 80px;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      flex-shrink: 0;
    }

    .aside-logo-title {
      color: #ffffff;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
    }

    .aside-logo-subtitle {
      color: rgba(255, 255, 255, 0.70);
      font-size: 11px;
    }

    #kt_aside_menu {
      background-color: #1b84ff;
      flex: 1 1 auto;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
    }

    #kt_aside_menu::-webkit-scrollbar {
      width: 4px;
    }
    #kt_aside_menu::-webkit-scrollbar-thumb {
      background-color: rgba(255, 255, 255, 0.25);
      border-radius: 4px;
    }

    #kt_wrapper {
      padding-left: 260px !important;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    @media (max-width: 991.98px) {
      #kt_wrapper { padding-left: 0 !important; }
    }

    /* Sidebar menu items */
    #ri-sidebar-nav {
      padding: 10px 0 30px;
    }
    #ri-sidebar-nav a.ri-menu-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 22px;
      color: rgba(255, 255, 255, 0.88);
      font-size: 13.5px;
      text-decoration: none;
      transition: 0.15s;
      white-space: nowrap;
    }
    #ri-sidebar-nav a.ri-menu-item:hover,
    #ri-sidebar-nav a.ri-menu-item.active {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }
    #ri-sidebar-nav a.ri-menu-item.active {
      border-left: 3px solid #ffffff;
      padding-left: 19px;
      font-weight: 500;
    }
    #ri-sidebar-nav .ri-section-label {
      font-size: 10px;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.5);
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 16px 22px 6px;
      display: block;
    }
    #ri-sidebar-nav .ri-divider {
      height: 1px;
      background: rgba(255, 255, 255, 0.12);
      margin: 8px 16px;
    }
    .ri-icon svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      stroke-width: 1.8;
      fill: none;
    }

    /* ========== PENILAI – FULL WIDTH TABLE ========== */
    .penilai-wrapper {
      width: 100%;
      margin: 0;
      background: transparent;
    }
    .penilai-table {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow-x: auto;
      width: 100%;
    }
    .penilai-table table {
      width: 100%;
      font-size: 0.95rem;
    }
    .penilai-table thead th {
      background-color: #f8fafc;
      font-weight: 600;
      padding: 14px 16px;
    }
    .penilai-table tbody td {
      padding: 12px 16px;
      vertical-align: middle;
    }
    .btn-penilai-hapus {
      color: #dc3545;
      text-decoration: none;
      font-weight: 500;
    }
    .btn-penilai-hapus:hover {
      text-decoration: underline;
      color: #a71d2a;
    }
    .card-tambah-penilai {
      border-radius: 16px;
      border: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
  </style>
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

      <!-- ========== SIDEBAR ========== -->
      <div id="kt_aside" class="aside pb-5 pt-5 pt-lg-0"
        data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}"
        data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'260px', '300px': '260px'}"
        data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">

        <div class="aside-logo" id="kt_aside_logo">
          <a href="/" class="d-flex align-items-center gap-3">
            <img alt="Logo" src="template.demo6/demo6/assets/media/logos/rmh.png" class="h-90px logo" />
            <div class="aside-logo-text">
              <div class="aside-logo-title">Rumah Inovasi</div>
              <div class="aside-logo-subtitle">Magetan</div>
            </div>
          </a>
        </div>

        <div class="aside-menu flex-column-fluid" id="kt_aside_menu">
          <nav id="ri-sidebar-nav">
            <a class="ri-menu-item" href="/index"><span class="ri-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></span><span class="ri-menu-label">Dashboard</span></a>
            <div class="ri-divider"></div>
            <span class="ri-section-label">Master</span>
            <a class="ri-menu-item" href="/event"><span class="ri-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span><span class="ri-menu-label">Event</span></a>
            <a class="ri-menu-item" href="/sub-event"><span class="ri-icon"><svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></span><span class="ri-menu-label">Sub Event</span></a>
            <a class="ri-menu-item" href="/bidang"><span class="ri-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg></span><span class="ri-menu-label">Bidang</span></a>
            <a class="ri-menu-item" href="/user"><span class="ri-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span><span class="ri-menu-label">User</span></a>
            <a class="ri-menu-item active" href="/penilai"><span class="ri-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><span class="ri-menu-label">Penilai</span></a>
            <a class="ri-menu-item" href="/pengumuman"><span class="ri-icon"><svg viewBox="0 0 24 24"><path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg></span><span class="ri-menu-label">Pengumuman</span></a>
            <div class="ri-divider"></div>
            <span class="ri-section-label">Indikator</span>
            <a class="ri-menu-item" href="/indikator/tahap-1"><span class="ri-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span><span class="ri-menu-label">Indikator Tahap 1</span></a>
            <a class="ri-menu-item" href="/indikator/tahap-2"><span class="ri-icon"><svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></span><span class="ri-menu-label">Indikator Tahap 2</span></a>
            <div class="ri-divider"></div>
            <span class="ri-section-label">Inovasi</span>
            <a class="ri-menu-item" href="/inovasi/riwayat"><span class="ri-icon"><svg viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.44"/></svg></span><span class="ri-menu-label">Riwayat</span></a>
            <a class="ri-menu-item" href="/inovasi/rekap-nilai"><span class="ri-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span><span class="ri-menu-label">Rekap Nilai</span></a>
            <div class="ri-divider"></div>
            <span class="ri-section-label">Penilaian</span>
            <a class="ri-menu-item" href="/penilaian/tahap-1"><span class="ri-icon"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span><span class="ri-menu-label">Penilaian Tahap 1</span></a>
            <a class="ri-menu-item" href="/penilaian/tahap-2"><span class="ri-icon"><svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span><span class="ri-menu-label">Penilaian Tahap 2</span></a>
          </nav>
        </div>
      </div>

      <!-- ========== MAIN WRAPPER ========== -->
      <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

        <!-- HEADER -->
        <div id="kt_header" class="header align-items-stretch">
          <div class="container-fluid d-flex align-items-stretch justify-content-between">
            <div class="d-flex align-items-center d-lg-none ms-n1 me-2" id="kt_aside_mobile_toggle">
              <div class="btn btn-icon btn-active-color-primary w-30px h-30px w-md-40px h-md-40px">
                <i class="ki-outline ki-abstract-14 fs-1"></i>
              </div>
            </div>
            <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
              <a href="index.html" class="d-lg-none">
                <img alt="Logo" src="template.demo6/demo6/assets/media/logos/rmh.png" class="h-25px" />
              </a>
            </div>
          </div>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar py-2" id="kt_toolbar">
          <div id="kt_toolbar_container" class="container-fluid d-flex align-items-center">
            <div class="flex-grow-1 flex-shrink-0 me-5">
              <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0"></div>
            </div>
          </div>
        </div>

        <!-- CONTENT AREA (isi halaman) -->
        <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
          <div id="kt_content_container" class="container-fluid">
            <!-- ========== PENILAI – FULL WIDTH CONTENT ========== -->
            <div class="penilai-wrapper">
              <!-- LIST Penilai -->
              <div id="penilai-view-list" class="penilai-page">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                  <div>
                    <h5 class="fw-bold mb-1">Sub Event : LOMBA INOTEK 2022</h5>
                    <p class="text-muted small mb-0">Daftar Penilai</p>
                  </div>
                  <button class="btn btn-primary btn-sm" onclick="penilaiShow('tambah')">+ Tambah Penilai</button>
                </div>
                <div class="penilai-table">
                  <!-- TABEL SUDAH DISESUAIKAN DENGAN SCREENSHOT -->
                  <table class="table table-hover align-middle mb-0">
                    <thead>
                      <tr><th width="5%">No</th><th width="35%">Nama Penilai</th><th width="45%">Email</th><th width="15%">Aksi</th></tr>
                    </thead>
                    <tbody>
                      <tr><td>1</td><td>muhammad noor majid</td><td>m.noormajid12@gmail.com</td><td><a href="#" class="btn-penilai-hapus">🔗 Hapus</a></td></tr>
                      <tr><td>2</td><td>Moch Nurrudin</td><td>moch.nurudin72@gmail.com</td><td><a href="#" class="btn-penilai-hapus">🔗 Hapus</a></td></tr>
                      <tr><td>3</td><td>Mujiono</td><td>mujiono.aldifa@gmail.com</td><td><a href="#" class="btn-penilai-hapus">🔗 Hapus</a></td></tr>
                      <tr><td>4</td><td>Alam Surya</td><td>alam.endriharto@gmail.com</td><td><a href="#" class="btn-penilai-hapus">🔗 Hapus</a></td></tr>
                      <tr><td>5</td><td>Eko Adri</td><td>remingtonsteel320@yahoo.com</td><td><a href="#" class="btn-penilai-hapus">🔗 Hapus</a></td></tr>
                      <tr><td>6</td><td>Jatmiko</td><td>okimfh99@gmail.com</td><td><a href="#" class="btn-penilai-hapus">🔗 Hapus</a></td></tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- TAMBAH Penilai (simulasi) - TIDAK DIUBAH -->
              <div id="penilai-view-tambah" class="penilai-page" style="display: none;">
                <h5 class="fw-bold mb-1">Tambah Penilai</h5>
                <p class="text-muted small">Sub Event: LOMBA INOTEK 2022</p>
                <div class="row">
                  <div class="col-md-8 col-lg-7">
                    <div class="card card-tambah-penilai">
                      <div class="card-body">
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Penilai</label>
                          <input type="text" class="form-control" id="penilaiNamaInput" placeholder="Nama penilai" />
                        </div>
                        <button class="btn btn-primary btn-sm mb-3" id="penilaiBtnTambah">Tambah ke Daftar</button>
                        <hr />
                        <h6 class="fw-bold">Daftar yang akan ditambahkan</h6>
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered align-middle" id="penilaiTabelTambah">
                            <thead><tr><th>No</th><th>Sub Event</th><th>Simpan</th><th>Batal</th></tr></thead>
                            <tbody>
                              <tr><td>1</td><td>Moch Nurrudin</td><td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td><td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td></tr>
                              <tr><td>2</td><td>Mujiono</td><td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td><td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td></tr>
                              <tr><td>3</td><td>Alam Surya</td><td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td><td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td></tr>
                              <tr><td>4</td><td>Eko Adri</td><td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td><td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td></tr>
                              <tr><td>5</td><td>Jatmiko</td><td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td><td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td></tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="mt-3">
                          <button class="btn btn-secondary btn-sm me-2" onclick="penilaiShow('list')">Kembali</button>
                          <button class="btn btn-primary btn-sm" id="btnSimpanSemua">Simpan Semua</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- FOOTER -->
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

      </div>
    </div>
  </div>

  <!-- ========== JAVASCRIPT (tidak diubah) ========== -->
  <script>var hostUrl = "assets/";</script>
  <script src="template.demo6/demo6/assets/plugins/global/plugins.bundle.js"></script>
  <script src="template.demo6/demo6/assets/js/scripts.bundle.js"></script>
  <script src="template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
  <script src="template.demo6/demo6/assets/plugins/custom/datatables/datatables.bundle.js"></script>
  <script src="template.demo6/demo6/assets/js/widgets.bundle.js"></script>
  <script src="template.demo6/demo6/assets/js/custom/widgets.js"></script>
  <script src="template.demo6/demo6/assets/js/custom/apps/chat/chat.js"></script>

  <script>
    function penilaiShow(view) {
      document.getElementById('penilai-view-list').style.display = (view === 'list') ? 'block' : 'none';
      document.getElementById('penilai-view-tambah').style.display = (view === 'tambah') ? 'block' : 'none';
    }
    // Tombol tambah baris baru
    document.getElementById('penilaiBtnTambah')?.addEventListener('click', function() {
      const input = document.getElementById('penilaiNamaInput');
      const nama = input.value.trim();
      if (!nama) return alert('Nama penilai harus diisi.');
      const tbody = document.querySelector('#penilaiTabelTambah tbody');
      const rowCount = tbody.rows.length;
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${rowCount+1}</td><td>${nama}</td><td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td><td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td>`;
      tbody.appendChild(tr);
      attachRowEvents(tr);
      input.value = '';
      input.focus();
    });

    function attachRowEvents(tr) {
      tr.querySelector('.simpan-teks')?.addEventListener('click', function() {
        alert('Data penilai "' + tr.cells[1].textContent + '" akan disimpan (simulasi).');
      });
      tr.querySelector('.batal-teks')?.addEventListener('click', function() { tr.remove(); updateNomor(); });
    }
    function updateNomor() {
      const rows = document.querySelectorAll('#penilaiTabelTambah tbody tr');
      rows.forEach((r, idx) => r.cells[0].textContent = idx+1);
    }
    document.querySelectorAll('#penilaiTabelTambah tbody tr').forEach(r => attachRowEvents(r));
    document.getElementById('btnSimpanSemua')?.addEventListener('click', function() {
      const rows = document.querySelectorAll('#penilaiTabelTambah tbody tr');
      if (!rows.length) return alert('Tidak ada data untuk disimpan.');
      const daftar = [...rows].map(r => r.cells[1].textContent);
      alert('Simpan semua penilai:\n' + daftar.join('\n') + '\n\n(Simulasi berhasil)');
    });
  </script>
</body>
</html>
