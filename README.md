# SIKAHIM Apps

**SIKAHIM** adalah singkatan dari **Sistem Informasi Kegiatan dan Agenda Harian Masjid**. Aplikasi ini membantu pengurus masjid mengelola agenda secara terpusat dan membantu jamaah menemukan serta mendaftarkan diri pada kegiatan yang tersedia.

## Tentang aplikasi

SIKAHIM memiliki dua area utama:

- **Admin/pengurus:** mengelola kegiatan, kategori, tag, jadwal, kuota, materi, pengguna, pendaftaran, evaluasi, dan kalender agenda.
- **Jamaah/user:** melihat kegiatan terverifikasi, membaca detail agenda, mendaftar sebagai peserta, dan melihat riwayat pendaftaran.

Kegiatan dapat dibuat melalui formulir biasa atau langsung dari kalender. Sistem juga melakukan validasi jadwal, kuota, status verifikasi, dan pendaftaran ganda.

## Fitur utama

- Autentikasi login, registrasi, logout, verifikasi email, dan reset password.
- Dashboard admin dengan ringkasan statistik.
- CRUD kegiatan dengan kategori, tag, thumbnail, materi, lokasi, jadwal, dan kuota.
- Kalender kegiatan terverifikasi dengan pembuatan kegiatan berdasarkan tanggal yang dipilih.
- Pendaftaran jamaah dengan validasi kuota dan jadwal.
- Detail kegiatan, daftar peserta, evaluasi, dan notifikasi.
- Seeder idempotent dengan data contoh realistis.
- Tampilan responsif berbasis AdminLTE dengan tema SIKAHIM.

## Teknologi

- PHP 8.2 atau lebih baru
- Laravel 11
- PostgreSQL
- AdminLTE
- Vite, Tailwind CSS, dan Alpine.js

## Prasyarat

Pastikan perangkat sudah memiliki:

1. PHP dan ekstensi `pdo_pgsql`.
2. Composer.
3. Node.js dan npm.
4. PostgreSQL Server yang sedang berjalan.
5. DBeaver sebagai aplikasi pengelola database PostgreSQL.

> DBeaver hanya digunakan untuk membuat dan mengelola database. DBeaver bukan web server. Laragon juga tidak wajib dibuka; yang wajib berjalan adalah PHP/Composer dan PostgreSQL.

Periksa PHP dan ekstensi PostgreSQL:

```powershell
php -v
php -r "echo extension_loaded('pdo_pgsql') ? 'pdo_pgsql aktif' : 'pdo_pgsql belum aktif';"
composer -V
node -v
npm -v
```

## Instalasi

Jalankan perintah berikut dari folder repository:

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

### Menyiapkan database PostgreSQL melalui DBeaver

1. Pastikan service PostgreSQL aktif.
2. Buka DBeaver dan hubungkan ke PostgreSQL menggunakan username serta password PostgreSQL.
3. Buat database baru, misalnya `sikahim-apps`.
4. Buka file `.env` dan sesuaikan konfigurasi:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sikahim-apps
DB_USERNAME=postgres
DB_PASSWORD=password_postgresql_anda
```

Nama database pada `.env` harus sama persis dengan database yang dibuat di DBeaver. Laravel tidak membuat database PostgreSQL secara otomatis; Laravel hanya membuat tabel ketika migration dijalankan.

### Migration dan data contoh

```powershell
php artisan config:clear
php artisan migrate --seed
php artisan storage:link
npm.cmd run build
```

Seeder dapat dijalankan ulang karena menggunakan pola idempotent:

```powershell
php artisan db:seed
```

## Menjalankan aplikasi

### Cara sederhana

Jalankan server Laravel:

```powershell
php artisan serve
```

Buka [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Development dengan Vite

Gunakan dua terminal:

Terminal 1:

```powershell
php artisan serve
```

Terminal 2:

```powershell
npm.cmd run dev
```

`npm.cmd` digunakan pada sebagian instalasi Windows PowerShell ketika `npm.ps1` diblokir oleh Execution Policy.

Alternatif satu perintah:

```powershell
composer run dev
```

### Akun demo

Semua akun seeder menggunakan password `password`.

- Admin: `admin1@example.com`
- User: `user11@example.com`

Nama pengguna pada data seed menggunakan nama dummy realistis, bukan nama anggota tim.

## Alur penggunaan

### Admin

1. Login menggunakan akun admin.
2. Buka dashboard untuk melihat statistik.
3. Buat kegiatan melalui menu **Buat Kegiatan** atau klik tanggal pada **Kalender**.
4. Lengkapi kategori, tag, jadwal, kuota, lokasi, deskripsi, thumbnail, dan materi.
5. Periksa kegiatan pada daftar kegiatan dan kelola pengguna yang terdaftar.

### User

1. Registrasi atau login menggunakan akun user.
2. Lihat kegiatan yang sudah terverifikasi dan belum selesai.
3. Buka detail kegiatan.
4. Isi data pendaftaran dan kirim.
5. Pantau status pendaftaran pada riwayat kegiatan.

## Validasi dan testing

```powershell
php artisan route:list
php artisan view:cache
php artisan test
vendor\bin\pint --test
npm.cmd run build
```

Test menggunakan SQLite in-memory melalui `phpunit.xml`, sehingga tidak mengubah database PostgreSQL development.

## Struktur repository

- `routes/web.php`: rute publik, user, dan admin.
- `app/Http/Controllers`: logika autentikasi, kegiatan, pendaftaran, dashboard, dan pengguna.
- `app/Models`: model dan relasi domain aplikasi.
- `database/migrations`: struktur tabel database.
- `database/seeders`: data contoh kategori, tag, user, kegiatan, pendaftaran, evaluasi, dan notifikasi.
- `resources/views`: halaman, layout, dan komponen antarmuka.
- `public/css/fonts.css`: tema warna dan styling global SIKAHIM.
- `RINGKASAN-PERBAIKAN.md`: ringkasan teknis perubahan aplikasi.
- `DEPLOYMENT.md`: panduan deployment gratis ke Render dengan PostgreSQL dan storage eksternal.

## Anggota tim

| Nama | NIM |
|---|---:|
| Bandyaga Adiansyah Sugandi | 231511037 |
| Dwika Ali Ramdhan Ibrahim | 231511042 |
| Muhammad Azzam Izzuddin | 231511054 |
| Nino Erico Apandi Nainggolan | 231511058 |
| Zidan Tri Satria Mukti | 231511064 |
