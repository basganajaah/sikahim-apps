<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2B1F17">
    <title>SIKAHIM | Sistem Informasi Kegiatan dan Agenda Harian Masjid</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sikahim.css') }}">
    <style>
        body { background: var(--sk-jati); color: #F4EEE6; }

        .landing { min-height: 100vh; display: flex; flex-direction: column; }

        .landing-nav { padding: 1.1rem 0; border-bottom: 1px solid rgba(244, 238, 230, .1); }

        .landing-brand {
            display: inline-flex; align-items: center; gap: .6rem;
            color: #fff; text-decoration: none;
            font-family: var(--sk-font-head); font-weight: 700; letter-spacing: .02em;
        }
        .landing-brand:hover { color: #fff; text-decoration: none; }
        .landing-brand img { width: 34px; height: 34px; border-radius: 50%; background: #fff; padding: 2px; }

        .landing-nav .nav-link-plain { color: rgba(244, 238, 230, .82); font-weight: 600; margin-right: 1.25rem; }
        .landing-nav .nav-link-plain:hover { color: #fff; text-decoration: none; }

        .btn-brass { background: var(--sk-kuningan); color: var(--sk-jati); border: 0; font-weight: 700; }
        .btn-brass:hover, .btn-brass:focus { background: #D9A945; color: var(--sk-jati); }
        .btn-ghost { border: 1px solid rgba(244, 238, 230, .35); color: #F4EEE6; font-weight: 600; }
        .btn-ghost:hover, .btn-ghost:focus { background: rgba(244, 238, 230, .08); color: #fff; }

        .landing-main { flex: 1; display: flex; align-items: center; padding: 3rem 0; }

        .landing h1 {
            font-family: var(--sk-font-head);
            font-weight: 800;
            font-size: clamp(2.3rem, 1.4rem + 3.4vw, 4rem);
            line-height: 1.02;
            letter-spacing: -.035em;
            color: #fff;
            max-width: 14ch;
            margin-bottom: 1.25rem;
        }

        .landing .lead { color: rgba(244, 238, 230, .78); font-size: 1.12rem; max-width: 46ch; margin-bottom: 2rem; }

        .landing-steps {
            list-style: none; padding: 0; margin: 2.75rem 0 0;
            display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem;
            max-width: 520px;
            border-top: 1px solid rgba(244, 238, 230, .14);
            padding-top: 1.25rem;
            counter-reset: step;
        }
        .landing-steps li { counter-increment: step; font-size: .92rem; color: rgba(244, 238, 230, .72); }
        .landing-steps li::before {
            content: counter(step);
            display: block;
            font-family: var(--sk-font-head);
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--sk-kuningan);
            margin-bottom: .2rem;
        }
        .landing-steps strong { display: block; color: #fff; font-weight: 600; margin-bottom: .1rem; }

        .landing-arch {
            position: relative;
            margin-left: auto;
            max-width: 400px;
            padding: 18px;
        }
        .landing-arch::before {
            content: "";
            position: absolute; inset: 0;
            border-radius: 999px 999px 18px 18px;
            background-image: var(--sk-kerawang);
            background-size: 48px 48px;
            opacity: .5;
        }
        .landing-arch img {
            position: relative;
            display: block;
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            border-radius: 999px 999px 10px 10px;
            border: 3px solid var(--sk-kuningan);
        }

        @media (max-width: 575.98px) {
            .landing-steps { grid-template-columns: 1fr; gap: .9rem; }
            .landing-nav .nav-link-plain { margin-right: .75rem; }
        }
    </style>
</head>
<body>
    <div class="landing">
        <nav class="landing-nav">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="landing-brand" href="{{ route('welcome') }}">
                    <img src="{{ asset('images/Logo_LukmanHakim.png') }}" alt="">
                    SIKAHIM
                </a>
                <div class="d-flex align-items-center">
                    @auth
                        <a class="btn btn-brass btn-sm px-3" href="{{ route('dashboard') }}">Buka dashboard</a>
                    @else
                        <a class="nav-link-plain" href="{{ route('login') }}">Masuk</a>
                        <a class="btn btn-brass btn-sm px-3" href="{{ route('register') }}">Daftar</a>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="landing-main">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h1>Satu ruang untuk menghidupkan agenda masjid.</h1>
                        <p class="lead">Pengurus mencatat dan memverifikasi setiap kegiatan. Jamaah melihat jadwalnya, mendaftar, lalu memberi penilaian setelah acara selesai.</p>
                        @auth
                            <a class="btn btn-brass btn-lg px-4" href="{{ route('dashboard') }}">Buka dashboard</a>
                        @else
                            <a class="btn btn-brass btn-lg px-4 mr-2 mb-2" href="{{ route('register') }}">Buat akun jamaah</a>
                            <a class="btn btn-ghost btn-lg px-4 mb-2" href="{{ route('login') }}">Masuk</a>
                        @endauth

                        <ol class="landing-steps">
                            <li><strong>Pilih kegiatan</strong>Dari jadwal yang sudah diverifikasi.</li>
                            <li><strong>Daftar</strong>Isi nama, usia, dan jenis kelamin.</li>
                            <li><strong>Beri evaluasi</strong>Rating dan ulasan setelah selesai.</li>
                        </ol>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block">
                        <div class="landing-arch">
                            <img src="{{ asset('images/Masjid_LH_polban.jpg') }}" alt="Masjid Lukman Hakim">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
