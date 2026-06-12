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
    <style>
        .captcha-card {
            border: 1px solid #d2d6dc;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .captcha-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .captcha-card .form-check-input {
            width: 1.4rem;
            height: 1.4rem;
            cursor: pointer;
            border: 2px solid #adb5bd;
            border-radius: 2px;
            flex-shrink: 0;
            transition: opacity 0.2s;
        }
        .captcha-card .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
        .captcha-spinner-wrap {
            width: 1.4rem;
            height: 1.4rem;
            flex-shrink: 0;
            display: none;
            align-items: center;
            justify-content: center;
        }
        .captcha-spinner-wrap.show {
            display: flex;
        }
        .captcha-spinner {
            width: 1.2rem;
            height: 1.2rem;
            border: 2.5px solid #d2d6dc;
            border-top-color: #4285f4;
            border-radius: 50%;
            animation: captchaSpin 0.7s linear infinite;
        }
        @keyframes captchaSpin {
            to { transform: rotate(360deg); }
        }
        .captcha-label {
            font-size: 0.95rem;
            font-weight: 500;
            color: #212529;
            cursor: pointer;
            margin: 0;
        }
        .captcha-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            min-width: 70px;
        }
        .captcha-badge .badge-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4285f4, #34a853, #fbbc05, #ea4335);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .captcha-badge .badge-text {
            font-size: 0.65rem;
            color: #6c757d;
            text-align: center;
            line-height: 1.2;
        }
    </style>
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

                                    <form method="POST" action="{{ route('register') }}" class="form w-100">
                                        @csrf

                                        <div class="text-center mb-11">
                                            <h1 class="text-gray-900 fw-bolder mb-3">Pendaftaran</h1>
                                        </div>

                                        @if($errors->any())
                                        <div class="alert alert-danger mb-4">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif

                                        {{-- Nama --}}
                                        <div class="fv-row mb-8">
                                            <input type="text" placeholder="Nama Lengkap" name="name"
                                                   autocomplete="off" class="form-control bg-transparent"
                                                   value="{{ old('name') }}" />
                                        </div>

                                        {{-- Email --}}
                                        <div class="fv-row mb-8">
                                            <input type="email" placeholder="Email" name="email"
                                                   autocomplete="off" class="form-control bg-transparent"
                                                   value="{{ old('email') }}" />
                                        </div>

                                        {{-- Password --}}
                                        <div class="fv-row mb-8" data-kt-password-meter="true">
                                            <div class="mb-1">
                                                <div class="position-relative mb-3">
                                                    <input class="form-control bg-transparent" type="password"
                                                           placeholder="Password" name="password" autocomplete="off" />
                                                    <button id="kt_password_toggle" type="button" class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                                            onclick="togglePasswordVisibility(this)" aria-label="Toggle password visibility">
                                                        <i class="ki-outline ki-eye-slash fs-2"></i>
                                                        <i class="ki-outline ki-eye fs-2 d-none"></i>
                                                    </button>
                                                </div>
                                                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                                </div>
                                            </div>
                                            <div class="text-muted">Gunakan 8 karakter atau lebih dengan campuran huruf, angka, dan simbol.</div>
                                        </div>

                                        {{-- Konfirmasi Password --}}
                                        <div class="fv-row mb-8">
                                            <input type="password" placeholder="Ulangi Password"
                                                   name="password_confirmation" autocomplete="off"
                                                   class="form-control bg-transparent" />
                                        </div>

                                        {{-- Captcha --}}
                                        <div class="fv-row mb-8">
                                            <div class="captcha-card">
                                                <div class="captcha-left">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="captchaCheckbox" name="captcha_verified"
                                                           value="1" {{ old('captcha_verified') ? 'checked' : '' }} />
                                                    <div class="captcha-spinner-wrap" id="captchaSpinner">
                                                        <div class="captcha-spinner"></div>
                                                    </div>
                                                    <label class="captcha-label" for="captchaCheckbox">
                                                        Saya bukan robot
                                                    </label>
                                                </div>
                                                <div class="captcha-badge">
                                                    <div class="badge-icon">&#x21bb;</div>
                                                    <div class="badge-text">reCAPTCHA<br>Privacy - Terms</div>
                                                </div>
                                            </div>
                                            @if($errors->has('captcha_verified'))
                                                <div class="invalid-feedback d-block mt-1">
                                                    {{ $errors->first('captcha_verified') }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Tombol Daftar --}}
                                        <div class="d-grid mb-10">
                                            <button type="submit" id="kt_sign_up_submit" class="btn btn-primary" disabled>
                                                <span class="indicator-label">Daftar</span>
                                                <span class="indicator-progress">Tunggu...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                            </button>
                                        </div>

                                        <div class="text-gray-500 text-center fw-semibold fs-6">
                                            Sudah punya akun?
                                            <a href="/sign-in" class="link-primary fw-semibold text-decoration-underline">Silahkan Login</a>
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

            // Toggle tombol daftar berdasarkan captcha
            const captcha    = document.getElementById('captchaCheckbox');
            const btnDaftar  = document.getElementById('kt_sign_up_submit');

            const spinner = document.getElementById('captchaSpinner');

            if (captcha && btnDaftar) {
                btnDaftar.disabled = !captcha.checked;

                captcha.addEventListener('change', function () {
                    if (this.checked) {
                        // Sembunyikan checkbox, tampilkan spinner
                        this.style.display = 'none';
                        spinner.classList.add('show');
                        btnDaftar.disabled = true;

                        // Setelah 1.5 detik: sembunyikan spinner, tampilkan checkbox tercentang
                        setTimeout(() => {
                            spinner.classList.remove('show');
                            this.style.display = '';
                            btnDaftar.disabled = false;
                        }, 1500);
                    } else {
                        btnDaftar.disabled = true;
                    }
                });
            }

            // Toggle visibility password
            window.togglePasswordVisibility = function (button) {
                const input = document.querySelector('input[name="password"]');
                if (!input) return;
                const slash = button.querySelector('.ki-eye-slash');
                const eye   = button.querySelector('.ki-eye');
                if (input.type === 'password') {
                    input.type = 'text';
                    slash?.classList.add('d-none');
                    eye?.classList.remove('d-none');
                } else {
                    input.type = 'password';
                    slash?.classList.remove('d-none');
                    eye?.classList.add('d-none');
                }
            };

        });
    </script>

</body>
</html>