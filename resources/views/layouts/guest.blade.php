<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIKAHIM') }}</title>

        <style>
            body {
                height: 100vh;
                margin: 0;
                overflow: hidden;
                font-family: 'Figtree', sans-serif;
                background-image: url('{{ asset('images/Masjid_LH_polban.jpg') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                color: #ffffff; /* Warna teks default */
                scroll-behavior: smooth; /* Animasi scrolling halus */
            }

            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8); /* Overlay lebih gelap */
                z-index: 1;
            }

            .container {
                box-sizing: border-box;
                height: 100vh;
                position: relative;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 16px;
            }

            .box {
                background: linear-gradient(145deg, #2d2d2d, #3c3c3c); /* Gradien warna gelap */
                color: #ffffff;
                width: 100%;
                max-width: 400px;
                padding: 24px;
                border-radius: 12px;
                box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.5), inset 0px 1px 2px rgba(255, 255, 255, 0.1);
                text-align: center;
            }

            .box a {
                display: inline-block;
                margin: 10px 0;
                padding: 10px 20px;
                font-size: 16px;
                font-weight: 600;
                color: #000000; /* Teks tombol warna gelap */
                background: #FFCC00; /* Kuning mencolok */
                border-radius: 5px;
                text-decoration: none;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
                transition: background 0.3s, transform 0.3s;
            }

            .box a:hover {
                background: #e6b800; /* Warna hover lebih gelap */
                transform: translateY(-2px);
            }

            .logo {
                width: 88px;
                height: 88px;
                margin-bottom: 12px;
                background:gray; /* Latar belakang logo gelap */
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
                cursor: pointer;
            }

            .logo img {
                width: 60%;
                height: auto;
            }

            @media (max-width: 768px) {
                .box {
                    padding: 20px;
                }

                .logo {
                    width: 64px;
                    height: 64px;
                    margin-bottom: 8px;
                }
            }
        </style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <!-- Overlay -->
        <div class="overlay"></div>

        <!-- Container -->
        <div class="container">
            <!-- Logo -->
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('images/Logo_LukmanHakim.png') }}" alt="Logo">
            </a>

            <!-- Box -->
            <div class="box">
                <h1 class="text-2xl font-bold mb-4">Selamat Datang</h1>
                <p class="mb-6">Silakan masuk atau daftar untuk melanjutkan.</p>

                {{ $slot }}
            </div>
        </div>

    </body>
</html>
