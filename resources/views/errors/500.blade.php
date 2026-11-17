<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>500 | SIKAHIM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700..800&family=Hanken+Grotesk:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sikahim.css') }}">
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 2rem 1.25rem; box-sizing: border-box; }
        .err { max-width: 460px; }
        .err-code {
            font-family: var(--sk-font-head);
            font-weight: 800;
            font-size: clamp(5rem, 3rem + 10vw, 9rem);
            line-height: .9;
            letter-spacing: -.05em;
            color: var(--sk-jati);
            margin: 0 0 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 3px solid var(--sk-kuningan);
            display: inline-block;
        }
        h1 { font-size: 1.5rem; margin: 0 0 .5rem; }
        p { color: var(--sk-tinta-2); margin: 0 0 1.75rem; line-height: 1.55; }
        a.sk-btn { text-decoration: none; }
    </style>
</head>
<body>
    <main class="err">
        <div class="err-code" aria-hidden="true">500</div>
        <h1>Server sedang bermasalah</h1>
        <p>Permintaan Anda gagal diproses. Coba muat ulang halaman dalam beberapa menit. Jika masih gagal, hubungi pengurus.</p>
        <a class="sk-btn sk-btn-primary" href="{{ url('/') }}">Kembali ke beranda</a>
    </main>
</body>
</html>