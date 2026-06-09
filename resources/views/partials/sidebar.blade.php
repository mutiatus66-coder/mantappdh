<!-- ==================== SIDEBAR ==================== -->
<div id="kt_aside"
     class="aside overflow-visible pb-5 pt-5 pt-lg-0"
     style="background-color: #1e2937 !important;"
     data-kt-drawer="true"
     data-kt-drawer-name="aside"
     data-kt-drawer-activate="{default: true, lg: false}"
     data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'260px', '300px': '260px'}"
     data-kt-drawer-direction="start"
     data-kt-drawer-toggle="#kt_aside_mobile_toggle">

    <!-- Logo -->
    <div class="aside-logo py-8" id="kt_aside_logo" 
         style="background: linear-gradient(90deg, #1b84ff, #0d6efd) !important;">
        <a href="/" class="d-flex align-items-center gap-3 px-6 text-white">
            <img alt="Logo" 
                 src="{{ asset('template.demo6/demo6/assets/media/logos/rmh.png') }}" 
                 class="h-55px logo" />
            <div>
                <div class="fw-bold fs-3">Rumah Inovasi</div>
                <div class="fs-7 opacity-90">Magetan</div>
            </div>
        </a>
    </div>

    <!-- Menu -->
    <div class="aside-menu flex-column-fluid" id="kt_aside_menu">
        <div class="hover-scroll-y my-2 my-lg-5 scroll-ms px-3"
             id="kt_aside_menu_wrapper"
             data-kt-scroll="true"
             data-kt-scroll-height="auto"
             data-kt-scroll-dependencies="#kt_aside_logo"
             data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu"
             data-kt-scroll-offset="5px">

            <div class="menu menu-column menu-title-gray-400 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary fw-semibold" data-kt-menu="true">

                @auth
                    @php $user = auth()->user(); @endphp

                    <!-- Beranda -->
                    <div class="menu-item py-2">
                        <a href="/" class="menu-link menu-center active">
                            <span class="menu-icon me-0">
                                <i class="ki-outline ki-home-2 fs-2x"></i>
                            </span>
                            <span class="menu-title fw-bold">Beranda</span>
                        </a>
                    </div>

                    <!-- Master -->
                    @if($user->isAdminBapperida())
                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item py-2">
                        <span class="menu-link menu-center">
                            <span class="menu-icon me-0">
                                <i class="ki-outline ki-notification-status fs-2x"></i>
                            </span>
                            <span class="menu-title">Master</span>
                        </span>
                        <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                            <a href="/event" class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Event</span>
                            </a>
                            <a href="/sub-event" class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Sub Event</span>
                            </a>
                            <a href="/bidang" class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Bidang</span>
                            </a>
                            <a href="/user" class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">User</span>
                            </a>
                            <a href="/penilai" class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Penilai</span>
                            </a>
                            <a href="/pengumuman" class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Pengumuman</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Indikator -->
                    @if($user->isAdminBapperida())
                        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item py-2">
                            <span class="menu-link menu-center">
                                <span class="menu-icon me-0">
                                    <i class="ki-outline ki-abstract-35 fs-2x"></i>
                                </span>
                                <span class="menu-title">Indikator</span>
                            </span>
                            <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                                <div class="menu-item">
                                    <div class="menu-content">
                                        <span class="menu-section fs-5 fw-bolder ps-1 py-1">Indikator</span>
                                    </div>
                                </div>
                                <a href="/indikator/tahap-1" class="menu-link">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Indikator Tahap 1</span>
                                </a>
                                <a href="/indikator/tahap-2" class="menu-link">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Indikator Tahap 2</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Inovasi -->
                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item py-2">
                        <span class="menu-link menu-center">
                            <span class="menu-icon me-0">
                                <i class="ki-outline ki-abstract-26 fs-2x"></i>
                            </span>
                            <span class="menu-title">Inovasi</span>
                        </span>
                        <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                            <div class="menu-item">
                                <div class="menu-content">
                                    <span class="menu-section fs-5 fw-bolder ps-1 py-1">Inovasi</span>
                                </div>
                            </div>
                            <a href="/inovasi/riwayat" class="menu-link">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Riwayat</span>
                            </a>
                            @if($user->hasRole(['admin_bapperida', 'penilai']))
                                <a href="/inovasi/rekap-nilai" class="menu-link">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Rekap Nilai</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Penilaian -->
                    @if($user->hasRole(['admin_bapperida', 'penilai']))
                        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item py-2">
                            <span class="menu-link menu-center">
                                <span class="menu-icon me-0">
                                    <i class="ki-outline ki-briefcase fs-2x"></i>
                                </span>
                                <span class="menu-title">Penilaian</span>
                            </span>
                            <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
                                <div class="menu-item">
                                    <div class="menu-content">
                                        <span class="menu-section fs-5 fw-bolder ps-1 py-1">Penilaian</span>
                                    </div>
                                </div>
                                <a href="/penilaian/tahap-1" class="menu-link">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Penilaian Tahap 1</span>
                                </a>
                                <a href="/penilaian/tahap-2" class="menu-link">
                                    <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                    <span class="menu-title">Penilaian Tahap 2</span>
                                </a>
                            </div>
                        </div>
                    @endif

                @endauth
            </div>
        </div>
    </div>
</div>