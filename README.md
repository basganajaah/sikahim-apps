# SIKAHIM Apps

SIKAHIM (Sistem Informasi Kegiatan dan Agenda Harian Masjid) adalah aplikasi web untuk mengelola kegiatan masjid, agenda harian, pendaftaran jamaah, dan evaluasi kegiatan.

## Ruang lingkup

SIKAHIM memiliki dua area penggunaan:

- **Admin/pengurus:** mengelola kegiatan, kategori, tag, jadwal, kuota, materi, pengguna, pendaftaran, evaluasi, notifikasi, dan kalender.
- **Jamaah/user:** melihat kegiatan terverifikasi, membaca detail agenda, mendaftar, dan memantau riwayat pendaftaran.

Kegiatan dapat dibuat melalui formulir atau langsung dari kalender. Sistem memvalidasi jadwal, kuota, status verifikasi, serta pendaftaran ganda.

## Fitur

- Login, registrasi, logout, verifikasi email, dan reset password.
- Dashboard admin dengan ringkasan statistik.
- Pengelolaan kegiatan dengan kategori, tag, lokasi, jadwal, kuota, thumbnail, dan materi.
- Kalender kegiatan dan pembuatan kegiatan berdasarkan tanggal.
- Pendaftaran jamaah dengan validasi kuota dan jadwal.
- Daftar peserta, evaluasi, dan notifikasi.
- Seeder idempotent dengan data contoh.
- Antarmuka responsif berbasis AdminLTE dengan tema SIKAHIM.

## Teknologi

- PHP 8.2+
- Laravel 11
- PostgreSQL
- AdminLTE
- Vite, Tailwind CSS, dan Alpine.js

## Persyaratan

- PHP dengan ekstensi `pdo_pgsql`
- Composer
- Node.js dan npm
- PostgreSQL
- DBeaver (opsional, sebagai client database)

DBeaver digunakan untuk membuat dan mengelola database PostgreSQL, bukan untuk menjalankan aplikasi. Server aplikasi dapat dijalankan melalui `php artisan serve`.

Verifikasi instalasi:

```powershell
php -v
php -r "echo extension_loaded('pdo_pgsql') ? 'pdo_pgsql aktif' : 'pdo_pgsql belum aktif';"
composer -V
node -v
npm -v
```

## Instalasi lokal

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

### Konfigurasi PostgreSQL

Buat database PostgreSQL melalui DBeaver, lalu sesuaikan `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sikahim-apps
DB_USERNAME=postgres
DB_PASSWORD=password_postgresql_anda
```

Laravel membuat tabel melalui migration, tetapi tidak membuat database PostgreSQL secara otomatis.

### Migration dan data awal

```powershell
php artisan config:clear
php artisan migrate --seed
php artisan storage:link
npm.cmd run build
```

Seeder dapat dijalankan kembali:

```powershell
php artisan db:seed
```

## Menjalankan aplikasi

Server Laravel:

```powershell
php artisan serve
```

Aplikasi tersedia di [http://127.0.0.1:8000](http://127.0.0.1:8000).

Untuk pengembangan dengan Vite, jalankan dua terminal:

```powershell
# Terminal 1
php artisan serve

# Terminal 2
npm.cmd run dev
```

`npm.cmd` digunakan pada Windows PowerShell ketika `npm.ps1` diblokir oleh Execution Policy. Alternatifnya:

```powershell
composer run dev
```

## Akun demo

Semua akun hasil seeder menggunakan password `password`.

| Peran | Email |
|---|---|
| Admin | `admin1@example.com` |
| User | `user11@example.com` |

Ganti password akun demo sebelum aplikasi dibuka untuk penggunaan publik.

## Alur utama

### Admin

1. Masuk ke dashboard admin.
2. Buat kegiatan melalui menu **Buat Kegiatan** atau **Kalender**.
3. Lengkapi kategori, tag, jadwal, kuota, lokasi, deskripsi, thumbnail, dan materi.
4. Kelola pengguna, peserta, evaluasi, dan notifikasi.

### User

1. Registrasi atau masuk menggunakan akun yang tersedia.
2. Pilih kegiatan yang sudah terverifikasi.
3. Buka detail kegiatan dan kirim pendaftaran.
4. Pantau status melalui riwayat pendaftaran.

## Validasi

```powershell
php artisan route:list
php artisan view:cache
php artisan test
vendor\bin\pint --test
npm.cmd run build
```

Test menggunakan SQLite in-memory melalui `phpunit.xml`, sehingga tidak mengubah database PostgreSQL development.

## Struktur repository

- `routes/web.php` — rute publik, user, dan admin.
- `app/Http/Controllers` — logika autentikasi, kegiatan, pendaftaran, dashboard, dan pengguna.
- `app/Models` — model dan relasi domain.
- `database/migrations` — struktur database.
- `database/seeders` — data contoh aplikasi.
- `resources/views` — halaman, layout, dan komponen antarmuka.
- `public/css/fonts.css` — tema dan styling global SIKAHIM.
- `RINGKASAN-PERBAIKAN.md` — ringkasan perubahan aplikasi.
- `DEPLOYMENT.md` — konfigurasi deployment Render, PostgreSQL, dan storage eksternal.

## Deployment

Deployment demo menggunakan Render Free, PostgreSQL Supabase atau Neon, dan Supabase Storage. Detail arsitektur serta konfigurasi tersedia di [DEPLOYMENT.md](DEPLOYMENT.md).

## Anggota tim

| Nama | NIM |
|---|---:|
| Bandyaga Adiansyah Sugandi | 231511037 |
| Dwika Ali Ramdhan Ibrahim | 231511042 |
| Muhammad Azzam Izzuddin | 231511054 |
| Nino Erico Apandi Nainggolan | 231511058 |
| Zidan Tri Satria Mukti | 231511064 |
