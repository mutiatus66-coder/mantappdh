<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rumah Inovasi - @yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/template.demo6/demo6/assets/media/logos/mgt.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap">
    <link href="/template.demo6/demo6/assets/plugins/global/plugins.bundle.css" rel="stylesheet">
    <link href="/template.demo6/demo6/assets/css/style.bundle.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: #ffffff; }
        .modern-header {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .menu-link.nav-link {
            font-weight: 600;
            color: #1e293b !important;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s;
        }
        .menu-link.nav-link:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }
        .btn-login {
            background: #2563eb;
            color: white;
            border-radius: 0px;
            padding: 0.5rem 1.8rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-login:hover {
            background: #1d4ed8;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 0;
            text-align: center;
            margin-top: 2rem;
        }
        .footer p { color: #64748b; font-size: 0.875rem; margin: 0; }
    </style>
</head>
<body>
    <div class="modern-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between py-2 py-lg-3">
                <a href="/" class="text-decoration-none">
                    <h1 style="font-size: 2rem; font-weight: 800; color: #2563eb;">RUMAHINOVASI</h1>
                </a>
                <div class="d-none d-lg-flex gap-2">
                    <a class="menu-link nav-link" href="/">Beranda</a>
                    <a class="menu-link nav-link" href="{{ route('pengumuman.luar.index') }}">Pengumuman</a>
                </div>
                <a href="/sign-in" class="btn-login">Login</a>
            </div>
        </div>
    </div>

    <main>
        @yield('content')
    </main>

    <div class="footer">
        <div class="container">
            <p>&copy; Copyright <strong>Dinas Kominfo.</strong> All rights reserved.<br>Develop by Dinas Kominfo Magetan Development</p>
        </div>
    </div>

    <script src="/template.demo6/demo6/assets/plugins/global/plugins.bundle.js"></script>
    <script src="/template.demo6/demo6/assets/js/scripts.bundle.js"></script>
    @stack('scripts')
</body>
</html>
