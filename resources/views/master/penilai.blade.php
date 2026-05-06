<!DOCTYPE html>
<html lang="en">

<!-- ============================================================
     Rumah Inovasi Magetan - Panel Admin
     Template: Metronic 8.2.3 (Keenthemes)
     Modified: Sidebar scrollable, code cleaned up
     ============================================================ -->

<head>
  <title>Rumah Inovasi Magetan - Panel Admin</title>
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

  <!-- Frame-busting: prevent clickjacking -->
  <script>if (window.top !== window.self) { window.top.location.replace(window.self.location.href); }</script>

  <style>
    /* ============================================================
       SIDEBAR (Aside) — Layout & Scrollable Menu
       ============================================================ */

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

    /* Logo area — fixed, never scrolls */
    #kt_aside_logo {
      background-color: #1b84ff; !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      min-height: 80px;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      flex-shrink: 0;
    }

    #kt_aside_logo a {
      display: flex;
      align-items: center;
      text-decoration: none;
      background: transparent !important;
    }

    .aside-logo-text {
      line-height: 1.3;
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
      font-weight: 400;
      letter-spacing: 0.3px;
    }

    /* Menu container — scrollable */
    #kt_aside_menu {
      background-color: #1b84ff;
      flex: 1 1 auto;
      overflow-y: auto;
      overflow-x: hidden;
      /* Custom scrollbar */
      scrollbar-width: thin;
      scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
    }

    #kt_aside_menu::-webkit-scrollbar {
      width: 4px;
    }

    #kt_aside_menu::-webkit-scrollbar-track {
      background: transparent;
    }

    #kt_aside_menu::-webkit-scrollbar-thumb {
      background-color: rgba(255, 255, 255, 0.25);
      border-radius: 4px;
    }

    #kt_aside_menu::-webkit-scrollbar-thumb:hover {
      background-color: rgba(255, 255, 255, 0.45);
    }

    /* Wrapper offset for sidebar width */
    #kt_wrapper {
      padding-left: 260px !important;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Content area pushes footer to bottom */
    #kt_content {
      flex: 1 1 auto;
    }

    @media (max-width: 991.98px) {
      #kt_wrapper {
        padding-left: 0 !important;
      }
    }

    /* ============================================================
       SIDEBAR NAV — Menu Items
       ============================================================ */

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
      font-family: inherit;
      font-weight: 400;
      text-decoration: none;
      cursor: pointer;
      transition: background 0.15s ease, color 0.15s ease;
      white-space: nowrap;
      overflow: hidden;
    }

    #ri-sidebar-nav a.ri-menu-item:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
    }

    #ri-sidebar-nav a.ri-menu-item.active {
      background: rgba(255, 255, 255, 0.20);
      color: #ffffff;
      font-weight: 500;
      border-left: 3px solid #ffffff;
      padding-left: 19px;
    }

    #ri-sidebar-nav .ri-menu-label {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex: 1;
      min-width: 0;
    }

    #ri-sidebar-nav .ri-section-label {
      font-size: 10px;
      font-weight: 700;
      color: rgba(255, 255, 255, 0.50);
      letter-spacing: 1.5px;
      text-transform: uppercase;
      padding: 16px 22px 6px;
      display: block;
      white-space: nowrap;
    }

    #ri-sidebar-nav .ri-divider {
      height: 1px;
      background: rgba(255, 255, 255, 0.12);
      margin: 8px 16px;
    }

    #ri-sidebar-nav .ri-icon {
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      opacity: 0.90;
    }

    #ri-sidebar-nav .ri-icon svg {
      width: 16px;
      height: 16px;
      fill: none;
      stroke: currentColor;
      stroke-width: 1.8;
      stroke-linecap: round;
      stroke-linejoin: round;
    }
  </style>
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed">

  <!-- Theme mode init -->
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

      <!-- ============================================================
           SIDEBAR (ASIDE)
           ============================================================ -->
      <div id="kt_aside"
        class="aside pb-5 pt-5 pt-lg-0"
        data-kt-drawer="true"
        data-kt-drawer-name="aside"
        data-kt-drawer-activate="{default: true, lg: false}"
        data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'260px', '300px': '260px'}"
        data-kt-drawer-direction="start"
        data-kt-drawer-toggle="#kt_aside_mobile_toggle">

        <!-- Logo -->
        <div class="aside-logo" id="kt_aside_logo">
          <a href="/" class="d-flex align-items-center gap-3">
            <img alt="Logo" src="template.demo6/demo6/assets/media/logos/rmh.png" class="h-90px logo" />
            <div class="aside-logo-text">
              <div class="aside-logo-title">Rumah Inovasi</div>
              <div class="aside-logo-subtitle">Magetan</div>
            </div>
          </a>
        </div>

        <!-- Scrollable Menu -->
        <div class="aside-menu flex-column-fluid" id="kt_aside_menu">
          <nav id="ri-sidebar-nav">

            <!-- Dashboard -->
            <a class="ri-menu-item" href="/index">
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

            <!-- MASTER -->
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

            <a class="ri-menu-item active" href="/penilai">
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

            <!-- INDIKATOR -->
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

            <!-- INOVASI -->
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

            <!-- PENILAIAN -->
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
        <!-- /Scrollable Menu -->

      </div>
      <!-- /SIDEBAR -->

      <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

        <!-- HEADER -->
        <div id="kt_header" class="header align-items-stretch">
          <div class="container-fluid d-flex align-items-stretch justify-content-between">

            <!-- Mobile: toggle aside -->
            <div class="d-flex align-items-center d-lg-none ms-n1 me-2" title="Show aside menu">
              <div class="btn btn-icon btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-1"></i>
              </div>
            </div>

            <!-- Mobile logo -->
            <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
              <a href="index.html" class="d-lg-none">
                <img alt="Logo" src="template.demo6/demo6/assets/media/logos/rmh.png" class="h-25px" />
              </a>
            </div>
            </div>
          </div>
        </div>
        <!-- /HEADER -->

        <!-- TOOLBAR -->
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
        <!-- /TOOLBAR -->

        <!-- CONTENT AREA (flex spacer — isi halaman masuk di sini) -->
        <div id="kt_content" class="content d-flex flex-column flex-column-fluid">
          <div id="kt_content_container" class="container-fluid"></div>
        </div>
        <!-- /CONTENT AREA -->
        <!-- ============================================================
     PENILAI - Content Only (Letakkan di #kt_content_container)
     ============================================================ -->

<style>
  /* penilai custom styles (scope dalam konten saja) */
  .penilai-wrapper {
    max-width: 1000px;
    margin: 0 auto;
  }

  .penilai-table {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    width: 100%;
  }

  .penilai-table thead {
    background-color: #f1f5f9;
    color: #2c3e50;
    font-size: 14px;
  }

  .btn-penilai-hapus {
    color: #dc3545;
    text-decoration: none;
  }

  .btn-penilai-hapus:hover {
    text-decoration: underline;
    color: #a71d2a;
  }
</style>

<div class="penilai-wrapper">
  <!-- Halaman Daftar Sub Event -->
  <div id="penilai-view-list" class="penilai-page">
    <h5 class="fw-bold mb-1">Sub Event : LOMBA INOTEK 2022</h5>
    <p class="text-muted small mb-3">Daftar Penilai</p>
    <button class="btn btn-outline-primary btn-sm mb-3" onclick="penilaiShow('tambah')">
      + Tambah Penilai
    </button>
    <div class="penilai-table table-responsive">
      <table class="table table-hover mb-0">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Penilai</th>
            <th>Email</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>1</td><td>muhammad noor majid</td><td>m.noormajid12@gmail.com</td><td><a href="#" class="btn-penilai-hapus"><i class="ki-outline ki-trash"></i> Hapus</a></td></tr>
          <tr><td>2</td><td>Moch Nurrudin</td><td>moch.nurudin72@gmail.com</td><td><a href="#" class="btn-penilai-hapus"><i class="ki-outline ki-trash"></i> Hapus</a></td></tr>
          <tr><td>3</td><td>Mujiono</td><td>mujiono.aldifa@gmail.com</td><td><a href="#" class="btn-penilai-hapus"><i class="ki-outline ki-trash"></i> Hapus</a></td></tr>
          <tr><td>4</td><td>Alam Surya</td><td>alam.endriharto@gmail.com</td><td><a href="#" class="btn-penilai-hapus"><i class="ki-outline ki-trash"></i> Hapus</a></td></tr>
          <tr><td>5</td><td>Eko Adri</td><td>remingtonsteel320@yahoo.com</td><td><a href="#" class="btn-penilai-hapus"><i class="ki-outline ki-trash"></i> Hapus</a></td></tr>
          <tr><td>6</td><td>Jatmiko</td><td>okimfh99@gmail.com</td><td><a href="#" class="btn-penilai-hapus"><i class="ki-outline ki-trash"></i> Hapus</a></td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Halaman Tambah Penilai -->
  <div id="penilai-view-tambah" class="penilai-page" style="display: none;">
    <h5 class="fw-bold mb-1">Tambah Penilai</h5>
    <p class="text-muted small">Sub Event: LOMBA INOTEK 2022</p>
    <div class="row">
      <div class="col-md-8 col-lg-6">
        <div class="card">
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Penilai</label>
              <input type="text" class="form-control" id="penilaiNamaInput" value="muhammad noor majid" placeholder="Nama penilai" />
            </div>
            <button class="btn btn-primary btn-sm" id="penilaiBtnTambah">Tambah ke Daftar</button>
            <hr />
            <h6 class="fw-bold">Daftar yang akan ditambahkan</h6>
            <div class="table-responsive">
              <table class="table table-sm table-bordered align-middle" id="penilaiTabelTambah">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Sub Event</th>
                    <th>Simpan</th>
                    <th>Batal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>Moch Nurrudin</td>
                    <td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td>
                    <td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Mujiono</td>
                    <td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td>
                    <td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>Alam Surya</td>
                    <td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td>
                    <td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td>
                  </tr>
                  <tr>
                    <td>4</td>
                    <td>Eko Adri</td>
                    <td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td>
                    <td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td>
                  </tr>
                  <tr>
                    <td>5</td>
                    <td>Jatmiko</td>
                    <td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td>
                    <td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td>
                  </tr>
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

<script>
  // Navigasi antar halaman penilai
  function penilaiShow(view) {
    document.getElementById('penilai-view-list').style.display = (view === 'list') ? 'block' : 'none';
    document.getElementById('penilai-view-tambah').style.display = (view === 'tambah') ? 'block' : 'none';
    // Reset input jika kembali ke list
    if (view === 'list') {
      document.getElementById('penilaiNamaInput').value = '';
    }
  }

  // Tambah baris ke daftar sementara
  document.getElementById('penilaiBtnTambah')?.addEventListener('click', function() {
    const input = document.getElementById('penilaiNamaInput');
    const nama = input.value.trim();
    if (!nama) return alert('Nama penilai harus diisi.');

    const tbody = document.querySelector('#penilaiTabelTambah tbody');
    const rowCount = tbody.rows.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${rowCount + 1}</td>
      <td>${nama}</td>
      <td><span class="simpan-teks" style="cursor:pointer; color:#198754;">Simpan</span></td>
      <td><span class="batal-teks" style="cursor:pointer; color:#dc3545;">Batal</span></td>
    `;
    tbody.appendChild(tr);
    input.value = '';
    input.focus();

    // Event untuk tombol Simpan & Batal pada baris baru
    attachRowEvents(tr);
  });

  // Fungsi memasang event pada tombol Simpan & Batal
  function attachRowEvents(tr) {
    // Tombol Simpan
    tr.querySelector('.simpan-teks').addEventListener('click', function() {
      const nama = this.closest('tr').querySelector('td:nth-child(2)').textContent;
      alert('Data penilai "' + nama + '" akan disimpan (simulasi).');
      // Di sini bisa kirim data ke server
    });

    // Tombol Batal
    tr.querySelector('.batal-teks').addEventListener('click', function() {
      this.closest('tr').remove();
      updatePenilaiNomor();
    });
  }

  // Pasang event untuk semua baris yang sudah ada
  document.querySelectorAll('#penilaiTabelTambah tbody tr').forEach(row => {
    attachRowEvents(row);
  });

  // Update nomor urut setelah penghapusan
  function updatePenilaiNomor() {
    const tbody = document.querySelector('#penilaiTabelTambah tbody');
    for (let i = 0; i < tbody.rows.length; i++) {
      tbody.rows[i].cells[0].textContent = i + 1;
    }
  }

  // Tombol Simpan Semua
  document.getElementById('btnSimpanSemua').addEventListener('click', function() {
    const tbody = document.querySelector('#penilaiTabelTambah tbody');
    const rows = tbody.querySelectorAll('tr');
    if (rows.length === 0) {
      alert('Tidak ada data untuk disimpan.');
      return;
    }
    let daftar = [];
    rows.forEach(row => {
      daftar.push(row.cells[1].textContent);
    });
    alert('Simpan semua penilai:\n' + daftar.join('\n') + '\n\n(Simulasi berhasil)');
    // Di sini bisa lakukan penyimpanan massal ke server
  });

  // Update tahun footer secara dinamis
  (function() {
    const footerSpan = document.querySelector('#kt_footer .text-muted');
    if (footerSpan) {
      footerSpan.innerHTML = new Date().getFullYear() + '&copy;';
    }
  })();
</script>

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
        <!-- /FOOTER -->

      </div>
      <!-- /MAIN WRAPPER -->

    </div>
  </div>

  <!-- Activity Logs Drawer -->
  <div id="kt_activities" class="bg-body"
    data-kt-drawer="true"
    data-kt-drawer-name="activities"
    data-kt-drawer-activate="true"
    data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'300px', 'lg': '900px'}"
    data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_activities_toggle"
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
          data-kt-scroll="true"
          data-kt-scroll-height="auto"
          data-kt-scroll-wrappers="#kt_activities_body"
          data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
          data-kt-scroll-offset="5px">
          <!-- Timeline items omitted for brevity — keep original content here -->
        </div>
      </div>
      <div class="card-footer py-5 text-center" id="kt_activities_footer">
        <a href="pages/user-profile/activity.html" class="btn btn-bg-body text-primary">
          View All Activities <i class="ki-outline ki-arrow-right fs-3 text-primary"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Chat Drawer -->
  <div id="kt_drawer_chat" class="bg-body"
    data-kt-drawer="true"
    data-kt-drawer-name="chat"
    data-kt-drawer-activate="true"
    data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'300px', 'md': '500px'}"
    data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_drawer_chat_toggle"
    data-kt-drawer-close="#kt_drawer_chat_close">
    <div class="card w-100 border-0 rounded-0" id="kt_drawer_chat_messenger">
      <div class="card-header pe-5" id="kt_drawer_chat_messenger_header">
        <div class="card-title">
          <div class="d-flex justify-content-center flex-column me-3">
            <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 mb-2 lh-1">Brian Cox</a>
            <div class="mb-0 lh-1">
              <span class="badge badge-success badge-circle w-10px h-10px me-1"></span>
              <span class="fs-7 fw-semibold text-muted">Active</span>
            </div>
          </div>
        </div>
        <div class="card-toolbar">
          <div class="btn btn-sm btn-icon btn-active-color-primary" id="kt_drawer_chat_close">
            <i class="ki-outline ki-cross-square fs-2"></i>
          </div>
        </div>
      </div>
      <div class="card-body" id="kt_drawer_chat_messenger_body">
        <div class="scroll-y me-n5 pe-5"
          data-kt-element="messages"
          data-kt-scroll="true"
          data-kt-scroll-activate="true"
          data-kt-scroll-height="auto"
          data-kt-scroll-dependencies="#kt_drawer_chat_messenger_header, #kt_drawer_chat_messenger_footer"
          data-kt-scroll-wrappers="#kt_drawer_chat_messenger_body"
          data-kt-scroll-offset="0px">
          <!-- Chat messages omitted for brevity — keep original content here -->
        </div>
      </div>
      <div class="card-footer pt-4" id="kt_drawer_chat_messenger_footer">
        <textarea class="form-control form-control-flush mb-3" rows="1" data-kt-element="input" placeholder="Type a message"></textarea>
        <div class="d-flex flex-stack">
          <div class="d-flex align-items-center me-2">
            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="Coming soon">
              <i class="ki-outline ki-paper-clip fs-3"></i>
            </button>
            <button class="btn btn-sm btn-icon btn-active-light-primary me-1" type="button" data-bs-toggle="tooltip" title="Coming soon">
              <i class="ki-outline ki-cloud-add fs-3"></i>
            </button>
          </div>
          <button class="btn btn-primary" type="button" data-kt-element="send">Send</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       SCROLL TO TOP
       ============================================================ -->
  <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-outline ki-arrow-up"></i>
  </div>

  <!-- ============================================================
       JAVASCRIPT BUNDLES
       ============================================================ -->

  <!-- Core -->
  <script>var hostUrl = "assets/";</script>
  <script src="template.demo6/demo6/assets/plugins/global/plugins.bundle.js"></script>
  <script src="template.demo6/demo6/assets/js/scripts.bundle.js"></script>

  <!-- Vendor (page-specific) -->
  <script src="template.demo6/demo6/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
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
  <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>

  <!-- Custom (page-specific) -->
  <script src="template.demo6/demo6/assets/js/widgets.bundle.js"></script>
  <script src="template.demo6/demo6/assets/js/custom/widgets.js"></script>
  <script src="template.demo6/demo6/assets/js/custom/apps/chat/chat.js"></script>
  <script src="template.demo6/demo6/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
  <script src="template.demo6/demo6/assets/js/custom/utilities/modals/create-campaign.js"></script>
  <script src="template.demo6/demo6/assets/js/custom/utilities/modals/users-search.js"></script>

</body>
</html>
