<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#2B1F17">

        <title>{{ config('app.name', 'SIKAHIM') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..700&family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/sikahim.css') }}">

        <style>
            body { margin: 0; background: var(--sk-batu); }

            .sk-auth {
                min-height: 100vh;
                display: grid;
                grid-template-columns: 1fr;
            }

            .sk-auth-visual { display: none; }

            .sk-auth-main {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2.5rem 1.25rem;
            }

            .sk-auth-panel { width: 100%; max-width: 400px; }

            .sk-auth-brand {
                display: inline-flex;
                align-items: center;
                gap: .65rem;
                margin-bottom: 2rem;
                color: var(--sk-tinta);
                text-decoration: none;
                font-family: var(--sk-font-head);
                font-weight: 700;
                font-size: 1.1rem;
                letter-spacing: .02em;
            }

            .sk-auth-brand img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #fff;
                border: 1px solid var(--sk-garis);
                padding: 3px;
            }

            .sk-auth-title {
                font-family: var(--sk-font-head);
                font-size: 1.75rem;
                font-weight: 700;
                letter-spacing: -.02em;
                line-height: 1.15;
                color: var(--sk-tinta);
                margin: 0 0 .4rem;
            }

            .sk-auth-lead { color: var(--sk-tinta-2); margin: 0 0 1.75rem; }

            .sk-field + .sk-field { margin-top: 1rem; }

            .sk-auth-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                margin-top: 1rem;
                font-size: .9rem;
            }

            .sk-check { display: inline-flex; align-items: center; gap: .5rem; color: var(--sk-tinta-2); }
            .sk-check input { width: 1rem; height: 1rem; accent-color: var(--sk-lumut); border-radius: 4px; }

            .sk-auth a { color: var(--sk-lumut); font-weight: 600; text-decoration: none; }
            .sk-auth a:hover { color: var(--sk-lumut-dark); text-decoration: underline; }

            .sk-auth .sk-btn-block { width: 100%; margin-top: 1.5rem; padding: .8rem 1.2rem; }

            .sk-auth-alt {
                margin-top: 1.75rem;
                padding-top: 1.25rem;
                border-top: 1px solid var(--sk-garis);
                color: var(--sk-tinta-2);
                font-size: .92rem;
            }

            .sk-auth select.sk-input { appearance: auto; }

            @media (min-width: 992px) {
                .sk-auth { grid-template-columns: minmax(0, 5fr) minmax(0, 6fr); }

                .sk-auth-visual {
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                    position: relative;
                    overflow: hidden;
                    color: #F4EEE6;
                    padding: 3rem;
                    background-color: var(--sk-jati);
                    background-image:
                        linear-gradient(180deg, rgba(43, 31, 23, .35) 0%, rgba(43, 31, 23, .92) 78%),
                        url('{{ asset('images/Masjid_LH_polban.jpg') }}');
                    background-size: cover;
                    background-position: center;
                }

                .sk-auth-visual::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    width: 72px;
                    background-image: var(--sk-kerawang);
                    background-size: 72px 72px;
                    opacity: .55;
                    border-left: 1px solid rgba(200, 149, 47, .4);
                }

                .sk-auth-visual h2 {
                    font-family: var(--sk-font-head);
                    font-size: 2.1rem;
                    font-weight: 700;
                    line-height: 1.12;
                    letter-spacing: -.02em;
                    color: #fff;
                    max-width: 16ch;
                    margin: 0 0 .75rem;
                }

                .sk-auth-visual p { max-width: 40ch; margin: 0; color: rgba(244, 238, 230, .8); }
                .sk-auth-visual > * { position: relative; max-width: calc(100% - 72px); }
            }
        </style>
    </head>
    <body>
        <div class="sk-auth">
            <aside class="sk-auth-visual" aria-hidden="true">
                <h2>Agenda masjid dalam satu tempat.</h2>
                <p>Lihat jadwal yang sudah diverifikasi pengurus, daftar sebagai peserta, lalu beri penilaian setelah kegiatan selesai.</p>
            </aside>

            <main class="sk-auth-main">
                <div class="sk-auth-panel">
                    <a href="{{ url('/') }}" class="sk-auth-brand">
                        <img src="{{ asset('images/Logo_LukmanHakim.png') }}" alt="">
                        SIKAHIM
                    </a>

                    @isset($heading)
                        <h1 class="sk-auth-title">{{ $heading }}</h1>
                    @endisset
                    @isset($lead)
                        <p class="sk-auth-lead">{{ $lead }}</p>
                    @endisset

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
