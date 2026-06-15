<div id="kt_aside"
  class="aside pb-5 pt-5 pt-lg-0"
  data-kt-drawer="true"
  data-kt-drawer-name="aside"
  data-kt-drawer-activate="{default: true, lg: false}"
  data-kt-drawer-overlay="true"
  data-kt-drawer-width="{default:'260px', '300px': '260px'}"
  data-kt-drawer-direction="start"
  data-kt-drawer-toggle="#kt_aside_mobile_toggle">

  {{-- Logo --}}
  <div class="aside-logo" id="kt_aside_logo">
    <a href="/" class="d-flex align-items-center gap-3">
      <img alt="Logo" src="{{ asset('img/bulb.png') }}" class="h-60px logo" />
      <div class="aside-logo-text">
        <div class="aside-logo-title">Rumah Inovasi</div>
        <div class="aside-logo-subtitle">Magetan</div>
      </div>
    </a>
  </div>

  {{-- Menu --}}
  <div class="aside-menu flex-column-fluid" id="kt_aside_menu">
    <nav id="ri-sidebar-nav">

      @auth
        @php $user = auth()->user(); @endphp

        {{-- ── Dashboard: semua role ── --}}
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

        {{-- ── Master: hanya Admin Bapperida ── --}}
        @if($user->isAdminBapperida())
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
        @endif

        {{-- ── Indikator: hanya Admin Bapperida ── --}}
        @if($user->isAdminBapperida())
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
        @endif

        {{-- ── Inovasi: semua role yang login ── --}}
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

        {{-- Rekap Nilai: Admin Bapperida & Penilai --}}
        @if($user->hasRole(['admin_bapperida', 'penilai']))
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
        @endif

        {{-- ── Penilaian: Admin Bapperida & Penilai ── --}}
        @if($user->hasRole(['admin_bapperida', 'penilai']))
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
        @endif

      @endauth

    </nav>
  </div>

</div>