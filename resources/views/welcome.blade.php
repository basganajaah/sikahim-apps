<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#102a43">
    <title>SIKAHIM | Sistem Informasi Kegiatan dan Agenda Harian Masjid</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        html, body { height: 100%; }
        body { background: #f5f7fb; overflow: hidden; }
        .landing { height: 100vh; min-height: 100vh; background: linear-gradient(135deg, #102a43 0%, #0f766e 100%); display: flex; flex-direction: column; overflow: hidden; }
        .landing-nav { border-bottom: 1px solid rgba(255,255,255,.15); }
        .landing-card { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); border-radius: 1rem; }
        .landing-mockup { border-radius: 1.25rem; box-shadow: 0 25px 70px rgba(0,0,0,.25); transform: rotate(2deg); }
        .landing-main { flex: 1; }
        .landing-main h1 { line-height: 1.1; }
        @media (max-width: 991.98px) {
            .landing-main { padding-top: 1.5rem; padding-bottom: 1.5rem; }
        }
        @media (max-width: 575.98px) {
            .landing-main h1 { font-size: 2.25rem; }
            .landing-main .lead { font-size: 1rem; }
            .landing-main .row.mt-5 { margin-top: 2rem !important; }
            .landing-card { padding: .65rem !important; }
            .landing-card small { font-size: .68rem; }
        }
    </style>
</head>
<body>
    <div class="landing text-white">
        <nav class="landing-nav py-2">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="text-white font-weight-bold text-lg text-decoration-none" href="{{ route('welcome') }}">
                    <i class="fas fa-mosque mr-2 text-warning"></i> SIKAHIM
                </a>
                <div>
                    @auth
                        <a class="btn btn-outline-light btn-sm" href="{{ route('dashboard') }}">Buka Dashboard</a>
                    @else
                        <a class="text-white mr-3" href="{{ route('login') }}">Masuk</a>
                        <a class="btn btn-light btn-sm" href="{{ route('register') }}">Daftar</a>
                    @endauth
                </div>
            </div>
        </nav>
        <main class="landing-main container d-flex align-items-center">
            <div class="row align-items-center w-100">
                <div class="col-lg-7">
                    <div class="sikahim-kicker mb-3">Sistem Informasi Kegiatan dan Agenda Harian Masjid</div>
                    <h1 class="display-4 font-weight-bold mb-4">Satu ruang untuk menghidupkan agenda masjid.</h1>
                    <p class="lead text-white-50 mb-4">Kelola kegiatan, bagikan agenda, dan ajak jamaah berpartisipasi dengan alur yang rapi dan mudah digunakan.</p>
                    <a class="btn btn-warning btn-lg px-4 mr-2" href="{{ route('register') }}">Mulai Sekarang <i class="fas fa-arrow-right ml-2"></i></a>
                    <a class="btn btn-outline-light btn-lg px-4 mt-2 mt-sm-0" href="{{ route('login') }}">Saya Sudah Punya Akun</a>
                    <div class="row mt-5">
                        <div class="col-sm-4 mb-3"><div class="landing-card p-3"><i class="fas fa-calendar-check text-warning mb-2"></i><div class="font-weight-bold">Agenda Teratur</div><small class="text-white-50">Semua kegiatan tercatat</small></div></div>
                        <div class="col-sm-4 mb-3"><div class="landing-card p-3"><i class="fas fa-users text-warning mb-2"></i><div class="font-weight-bold">Jamaah Terlibat</div><small class="text-white-50">Pendaftaran lebih mudah</small></div></div>
                        <div class="col-sm-4 mb-3"><div class="landing-card p-3"><i class="fas fa-chart-line text-warning mb-2"></i><div class="font-weight-bold">Terukur</div><small class="text-white-50">Evaluasi kegiatan</small></div></div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <img class="landing-mockup img-fluid mt-4" src="{{ asset('images/Masjid_LH_polban.jpg') }}" alt="Masjid">
                </div>
            </div>
        </main>
    </div>
</body>
</html>
