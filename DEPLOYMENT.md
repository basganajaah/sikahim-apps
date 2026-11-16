# Panduan Deployment Gratis SIKAHIM

Dokumen ini menjelaskan persiapan deployment SIKAHIM ke internet menggunakan layanan free tier. Targetnya adalah deployment demo yang dapat diakses publik tanpa membeli hosting, domain, atau server database.

## Arsitektur yang digunakan

| Kebutuhan | Layanan | Keterangan |
|---|---|---|
| Source code | GitHub | Repository harus memiliki branch utama `main`. |
| Web Laravel | Render Free Web Service | Menjalankan aplikasi Laravel menggunakan Docker. |
| PostgreSQL | Neon Free atau Supabase Free | Database production yang dapat diakses dari Render. |
| File upload | Supabase Storage Free | Menyimpan thumbnail dan materi secara eksternal. |
| Database client | DBeaver | Opsional, untuk melihat database production. |

Render Free dapat sleep setelah tidak ada traffic. Supabase dan Neon juga memiliki batas kuota serta dapat menonaktifkan resource yang lama tidak digunakan. Konfigurasi ini ditujukan untuk demo atau tugas, bukan production dengan SLA.

## 1. Persiapan branch `main`

Jalankan dari root repository:

```powershell
git status
git branch --show-current
git branch -m master main
git add .
git commit -m "Prepare SIKAHIM for deployment"
git push -u origin main
```

Jika branch `main` sudah ada di GitHub, gunakan:

```powershell
git push -u origin main
```

Setelah berhasil, ubah **Default branch** pada GitHub melalui **Settings > Branches** menjadi `main`. Jangan menghapus branch lama sebelum memastikan branch `main` sudah dapat diakses dan deployment berhasil.

## 2. Pemeriksaan sebelum deploy

Pastikan validasi lokal berhasil:

```powershell
composer install
npm install
php artisan config:clear
php artisan view:cache
php artisan test
vendor\bin\pint --test
npm.cmd run build
```

Pastikan file berikut tidak pernah di-commit:

```text
.env
.env.*.local
storage/logs/*
```

`APP_KEY`, password database, token storage, dan secret provider hanya boleh diisi melalui Environment Variables pada Render.

## 3. Menyiapkan database PostgreSQL gratis

Pilih salah satu:

- [Neon Free](https://neon.tech): PostgreSQL ringan untuk prototype.
- [Supabase Free](https://supabase.com): PostgreSQL sekaligus menyediakan Storage.

Untuk konfigurasi paling sederhana, gunakan **Supabase Free** agar database dan file storage berada dalam satu project.

### Supabase

1. Buat project baru.
2. Simpan password database.
3. Buka **Project Settings > Database**.
4. Salin host, port, database, user, dan password.
5. Buka **Storage > New bucket**.
6. Buat bucket bernama `sikahim-files`.
7. Jadikan bucket public untuk kebutuhan demo.
8. Catat:
   - Project URL.
   - Storage S3 endpoint.
   - S3 access key.
   - S3 secret key.
   - Nama bucket.

Jangan memasukkan secret key ke GitHub.

## 4. Persiapan storage upload

Render Free memiliki filesystem ephemeral. File yang disimpan ke `storage/app/public` dapat hilang ketika service restart, sleep, atau redeploy.

Repository ini saat ini menyimpan file kegiatan menggunakan disk lokal `public` pada `KegiatanController`. Sebelum mengaktifkan upload di deployment publik, storage upload perlu diarahkan ke Supabase Storage melalui disk S3-compatible.

### Paket yang diperlukan

Tambahkan adapter S3 Laravel:

```powershell
composer require league/flysystem-aws-s3-v3 "^3.0"
```

### Konfigurasi environment storage

Gunakan variable berikut pada Render:

```dotenv
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=isi_s3_access_key_supabase
AWS_SECRET_ACCESS_KEY=isi_s3_secret_key_supabase
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=sikahim-files
AWS_ENDPOINT=https://PROJECT_REF.storage.supabase.co/storage/v1/s3
AWS_URL=https://PROJECT_REF.supabase.co/storage/v1/object/public/sikahim-files
AWS_USE_PATH_STYLE_ENDPOINT=true
```

`PROJECT_REF` diganti dengan project reference Supabase.

### Catatan kompatibilitas aplikasi

Kode upload harus menggunakan disk yang sama dengan konfigurasi deployment:

```php
$file->store('thumbnails', 's3');
$file->store('materi', 's3');
```

URL file juga harus dibuat melalui Laravel Storage, bukan melalui URL lokal `/storage`:

```php
Storage::disk('s3')->url($kegiatan->thumbnail);
```

Jangan menggunakan `asset('storage/...')` untuk file yang disimpan di Supabase Storage. Setelah perubahan storage selesai, jalankan test dan build ulang sebelum push ke `main`.

## 5. Menyiapkan Docker untuk Render

Render menjalankan Laravel melalui Docker. Repository harus memiliki:

- `Dockerfile`
- `.dockerignore`
- script startup/deploy
- web server yang meneruskan request ke `public/index.php`

Gunakan panduan resmi Render untuk Laravel Docker:

<https://render.com/docs/deploy-php-laravel-docker>

Konfigurasi container wajib:

- PHP minimal 8.2.
- Ekstensi `pdo_pgsql`.
- Composer dependencies ter-install.
- Asset frontend sudah dibuat dengan `npm run build`.
- Web server mendengarkan `0.0.0.0:$PORT`.
- Root web diarahkan ke folder `public`.

## 6. Membuat Web Service di Render

1. Buka [Render Dashboard](https://dashboard.render.com).
2. Pilih **New > Web Service**.
3. Hubungkan repository GitHub.
4. Pilih branch `main`.
5. Pilih runtime **Docker**.
6. Pilih instance **Free**.
7. Tambahkan semua environment variables aplikasi.
8. Tambahkan environment variables database.
9. Tambahkan environment variables Supabase Storage.
10. Jalankan deployment.

Environment minimum:

```dotenv
APP_NAME=SIKAHIM
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:hasil_php_artisan_key_generate_show
APP_URL=https://nama-service.onrender.com
LOG_CHANNEL=stderr

DB_CONNECTION=pgsql
DB_HOST=host-database
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=password-database
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Buat `APP_KEY` secara lokal tanpa menulisnya ke file `.env`:

```powershell
php artisan key:generate --show
```

## 7. Migration dan seeding production

Setelah service berhasil build, jalankan migration satu kali melalui shell Render atau deploy command:

```bash
php artisan migrate --force
```

Untuk data demo:

```bash
php artisan db:seed --force
```

Jangan menggunakan `migrate:fresh` pada database publik karena perintah tersebut menghapus semua tabel dan data.

Seeder SIKAHIM menggunakan data contoh realistis dan dapat dijalankan ulang, tetapi seeding production tetap sebaiknya dilakukan hanya saat database baru dibuat.

## 8. Pengujian setelah online

Periksa urutan berikut:

1. Landing page dapat dibuka.
2. Login admin berhasil.
3. Dashboard admin dapat dibuka.
4. Daftar kegiatan tampil.
5. User dapat registrasi dan login.
6. User dapat melihat kegiatan.
7. Pendaftaran kegiatan berhasil.
8. Admin dapat membuat kegiatan.
9. Upload thumbnail dan materi tersimpan di Supabase Storage.
10. Setelah Render sleep lalu aktif kembali, data database dan file tetap tersedia.

Jika aplikasi menampilkan error database:

```bash
php artisan config:clear
php artisan migrate:status
```

Jika upload menghasilkan URL `/storage/...`, berarti kode upload masih menggunakan disk lokal dan belum diarahkan ke disk S3/Supabase.

## 9. Keamanan minimum

- Set `APP_DEBUG=false`.
- Jangan commit `.env`.
- Jangan menaruh password database di README.
- Gunakan password demo yang berbeda untuk deployment publik.
- Batasi ukuran dan tipe file upload.
- Jangan menjalankan `migrate:fresh` pada database online.
- Hapus data pribadi asli sebelum deployment demo.
- Periksa penggunaan kuota Render dan Supabase secara berkala.

## 10. Checklist final

- [ ] Branch GitHub utama adalah `main`.
- [ ] Repository dapat di-build dari kondisi bersih.
- [ ] `APP_KEY` sudah diisi di Render.
- [ ] `APP_DEBUG=false`.
- [ ] Database PostgreSQL production sudah dibuat.
- [ ] `DB_SSLMODE=require`.
- [ ] Migration sudah berhasil.
- [ ] Seeder demo sudah dijalankan.
- [ ] Adapter S3 sudah ter-install.
- [ ] Kode upload memakai disk S3.
- [ ] URL file memakai `Storage::disk('s3')->url(...)`.
- [ ] Bucket Supabase public untuk demo.
- [ ] Tidak ada secret di GitHub.
- [ ] Login, pendaftaran, pembuatan kegiatan, dan upload sudah diuji.

## Referensi resmi

- [Render Free Services](https://render.com/docs/free)
- [Render Laravel Docker Deployment](https://render.com/docs/deploy-php-laravel-docker)
- [Supabase Pricing](https://supabase.com/pricing)
- [Supabase Storage](https://supabase.com/docs/guides/storage)
- [Laravel Deployment](https://laravel.com/docs/11.x/deployment)
