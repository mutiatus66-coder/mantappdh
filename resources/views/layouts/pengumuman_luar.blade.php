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
    <link href="{{ asset('template.demo6/demo6/assets/css/CostumeStyle.css') }}" rel="stylesheet" />
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
