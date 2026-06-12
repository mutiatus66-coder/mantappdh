<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rumah Inovasi Magetan</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="{{ asset('template.demo6/demo6/assets/media/logos/mgt.png') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('template.demo6/demo6/assets/css/style.bundle.css') }}" rel="stylesheet" />
    <script>if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
</head>
<body id="kt_body" class="auth-bg">

    <script>
        var defaultThemeMode = "light"; var themeMode;
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
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-lg-500px p-10">

                        <table style="box-shadow: 0 4px 24px rgba(0,0,0,0.12); border-radius: 16px; border-collapse: separate; background: #fff; width: 100%;">
                            <tr>
                                <td style="padding: 32px;">

                                    {{-- ✅ FIX 1: method POST + action ke route login.post --}}
                                    <form method="POST" action="{{ route('login.post') }}" class="form w-100" id="kt_sign_in_form">
                                        {{-- ✅ FIX 2: CSRF token wajib ada --}}
                                        @csrf

                                        <div class="text-center mb-11">
                                            <h1 class="text-gray-900 fw-bolder mb-3">Masuk</h1>
                                        </div>

                                        @if(session('success'))
                                            <div class="alert alert-success mb-4 text-center">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        {{-- ✅ FIX 3: tampilkan error login --}}
                                        @if(session('error'))
                                            <div class="alert alert-danger mb-4 text-center">
                                                {{ session('error') }}
                                            </div>
                                        @endif

                                        @if($errors->any())
                                            <div class="alert alert-danger mb-4">
                                                {{ $errors->first() }}
                                            </div>
                                        @endif

                                        {{-- Email --}}
                                        <div class="fv-row mb-8">
                                            <input
                                                type="email"
                                                placeholder="Email"
                                                name="email"
                                                value="{{ old('email') }}"
                                                autocomplete="off"
                                                class="form-control bg-transparent @error('email') is-invalid @enderror"
                                            />
                                        </div>

                                        {{-- Password --}}
                                        <div class="fv-row mb-3">
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control bg-transparent @error('password') is-invalid @enderror"
                                                    type="password"
                                                    placeholder="Password"
                                                    name="password"
                                                    autocomplete="off"
                                                />
                                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                                    data-kt-password-meter-control="visibility">
                                                    <i class="ki-outline ki-eye-slash fs-2"></i>
                                                    <i class="ki-outline ki-eye fs-2 d-none"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                            <div></div>
                                            <a href="/reset-password" class="link-primary text-decoration-underline">Lupa Kata Sandi?</a>
                                        </div>

                                        <div class="d-grid mb-10">
                                            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                                <span class="indicator-label">Masuk</span>
                                                <span class="indicator-progress">Tunggu...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>

                                        <div class="text-gray-500 text-center fw-semibold fs-6">
                                            Belum Punya Akun?
                                            <a href="/sign-up" class="link-primary text-decoration-underline">Daftar</a>
                                        </div>

                                    </form>

                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>var hostUrl = "{{ asset('template.demo6/demo6/assets/') }}";</script>
    <script src="{{ asset('template.demo6/demo6/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('template.demo6/demo6/assets/js/scripts.bundle.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.querySelector('[data-kt-password-meter-control="visibility"]');
            if (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const input = this.closest('.position-relative').querySelector('input');
                    const slash = this.querySelector('.ki-eye-slash');
                    const eye   = this.querySelector('.ki-eye');
                    if (input.type === 'password') {
                        input.type = 'text';
                        slash.classList.add('d-none');
                        eye.classList.remove('d-none');
                    } else {
                        input.type = 'password';
                        slash.classList.remove('d-none');
                        eye.classList.add('d-none');
                    }
                });
            }
        });
    </script>

</body>
</html>