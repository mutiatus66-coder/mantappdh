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

      <div class="d-flex align-items-stretch flex-shrink-0">

        {{-- Riwayat Halaman --}}
      <div class="d-flex align-items-center ms-1 ms-lg-3">
        <a href="#"
          id="historyToggleBtn"
          onclick="toggleHistoryPanel()"
          class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
          title="Riwayat Halaman">
          <i class="bi bi-clock-history fs-1"></i>
        </a>
      </div>

        {{-- Ubah tema --}}
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

        {{-- Menu user (avatar dropdown) --}}
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
                  @auth
                    <div class="fw-bold d-flex align-items-center fs-5">
                      {{ auth()->user()->nama }}
                      <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">
                        {{ auth()->user()->hak_akses }}
                      </span>
                    </div>
                    <span class="fw-semibold text-muted fs-7">{{ auth()->user()->email }}</span>
                  @endauth
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
              <a href="{{ route('login') }}" class="menu-link px-5"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Sign Out
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
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