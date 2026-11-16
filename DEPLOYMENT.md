# Deployment SIKAHIM

Dokumen ini mencatat konfigurasi deployment demo SIKAHIM menggunakan GitHub, Render Free, PostgreSQL terkelola, dan Supabase Storage.

Konfigurasi ini ditujukan untuk demo atau tugas. Render Free dapat menghentikan service setelah tidak aktif, sedangkan layanan database dan storage memiliki batas kuota. Konfigurasi tersebut belum ditujukan untuk production dengan SLA.

## Arsitektur

| Komponen | Layanan | Keterangan |
|---|---|---|
| Source code | GitHub | Branch deployment: `main` |
| Aplikasi Laravel | Render Free Web Service | Dibangun dan dijalankan melalui Docker |
| Database | Supabase Free atau Neon Free | PostgreSQL production |
| File upload | Supabase Storage Free | Thumbnail dan materi kegiatan |
| Database client | DBeaver | Opsional, untuk inspeksi database |

Render menggunakan filesystem ephemeral. File yang disimpan di container akan hilang saat service restart, sleep, atau redeploy. Karena itu, upload production diarahkan ke Supabase Storage melalui adapter S3.

## Source code

Branch `main` digunakan sebagai sumber deployment:

```powershell
git status
git branch --show-current
git push -u origin main
```

Default branch repository GitHub perlu diarahkan ke `main` melalui **Settings > Branches**. File `.env`, kredensial database, access key, dan secret key tidak boleh masuk ke repository.

## Pemeriksaan sebelum deployment

```powershell
composer install
npm install
php artisan config:clear
php artisan view:cache
php artisan test
vendor\bin\pint --test
npm.cmd run build
```

File lokal berikut tidak termasuk source deployment:

```text
.env
.env.*.local
storage/logs/*
```

## PostgreSQL

Supabase digunakan sebagai pilihan utama karena menyediakan PostgreSQL dan Storage dalam satu project. Neon dapat digunakan sebagai alternatif database.

Informasi yang diperlukan dari provider database:

- host
- port
- nama database
- username
- password
- kebutuhan SSL

Untuk koneksi Render ke Supabase, gunakan Session Pooler apabila koneksi IPv4 diperlukan. Nilai `DB_SSLMODE` diset ke `require`.

## Supabase Storage

Bucket public `sikahim-files` digunakan untuk kebutuhan demo. Nilai berikut tersedia pada pengaturan S3 Supabase:

- Project reference
- S3 endpoint
- access key
- secret key
- region

Secret key hanya disimpan sebagai environment variable Render.

### Environment variable storage

```dotenv
FILESYSTEM_DISK=s3
FILESYSTEM_UPLOAD_DISK=s3

AWS_ACCESS_KEY_ID=isi_s3_access_key
AWS_SECRET_ACCESS_KEY=isi_s3_secret_key
AWS_DEFAULT_REGION=isi_region_supabase
AWS_BUCKET=sikahim-files
AWS_ENDPOINT=https://PROJECT_REF.storage.supabase.co/storage/v1/s3
AWS_URL=https://PROJECT_REF.supabase.co/storage/v1/object/public/sikahim-files
AWS_USE_PATH_STYLE_ENDPOINT=true
```

`PROJECT_REF` diganti dengan project reference Supabase. Adapter `league/flysystem-aws-s3-v3` sudah tercantum di `composer.json` dan dipasang saat image Docker dibangun.

Di lokal, `FILESYSTEM_UPLOAD_DISK=public` tetap dapat digunakan. Kode upload membaca disk dari konfigurasi tersebut, sedangkan URL file dibuat melalui disk yang sama.

## Docker dan Render

Repository menyediakan `Dockerfile` dan `.dockerignore`. Image melakukan hal berikut:

- menggunakan PHP 8.3;
- memasang ekstensi `pdo_pgsql` dan `mbstring`;
- memasang dependency Composer;
- membangun asset frontend;
- menjalankan migration;
- menjalankan Laravel pada `0.0.0.0:$PORT`.

Migration dijalankan saat container mulai. Seeder tidak dijalankan otomatis agar restart service tidak menulis ulang data demo.

## Environment variable Render

Web Service Render menggunakan runtime **Docker**, branch `main`, dan instance **Free**.

Variabel aplikasi minimum:

```dotenv
APP_NAME=SIKAHIM
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:hasil_php_artisan_key_generate_show
APP_URL=https://nama-service.onrender.com
ASSET_URL=https://nama-service.onrender.com
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

`APP_URL` dan `ASSET_URL` harus menggunakan `https://`. Aplikasi juga memaksa HTTPS pada environment production untuk mencegah asset diblokir sebagai mixed content.

Nilai `APP_KEY` dapat dibuat tanpa menyimpan perubahan ke `.env`:

```powershell
php artisan key:generate --show
```

Tambahkan variabel Supabase Storage dari bagian sebelumnya pada Environment Variables Render.

## Migration dan seeding

Setelah deployment berhasil:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Image saat ini menjalankan migration ketika service dimulai. Perintah `migrate:fresh` tidak digunakan pada database publik karena menghapus seluruh tabel dan data.

## Verifikasi service

Pemeriksaan pascadeployment:

1. Landing page memuat asset CSS dan gambar melalui HTTPS.
2. Login admin dan user berhasil.
3. Dashboard dan daftar kegiatan dapat dibuka.
4. User dapat melakukan pendaftaran.
5. Admin dapat membuat kegiatan.
6. Thumbnail dan materi tersimpan di Supabase Storage.
7. Data tetap tersedia setelah service sleep dan aktif kembali.

Perintah pemeriksaan melalui Render Shell:

```bash
php artisan config:clear
php artisan migrate:status
```

URL file yang masih berbentuk `/storage/...` menandakan upload masih menggunakan disk lokal.

## Keamanan

- `APP_DEBUG=false` pada deployment.
- Secret hanya disimpan di Environment Variables Render.
- Password akun demo diganti sebelum URL dibagikan.
- File upload dibatasi berdasarkan ukuran dan MIME type.
- Data pribadi asli tidak digunakan pada data demo.
- `migrate:fresh` tidak dijalankan pada database publik.
- Kuota Render dan Supabase dipantau secara berkala.

## Checklist

- [ ] Default branch GitHub adalah `main`.
- [ ] Repository dapat dibangun dari kondisi bersih.
- [ ] `APP_KEY` sudah tersedia di Render.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL` dan `ASSET_URL` menggunakan HTTPS.
- [ ] Database PostgreSQL production tersedia.
- [ ] `DB_SSLMODE=require`.
- [ ] Migration berhasil.
- [ ] Seeder demo telah dijalankan.
- [ ] Adapter S3 terpasang.
- [ ] Bucket Supabase tersedia.
- [ ] Upload dan URL file menggunakan disk S3.
- [ ] Secret tidak terdapat di GitHub.
- [ ] Login, pendaftaran, pembuatan kegiatan, dan upload telah diuji.

## Referensi

- [Render Free Services](https://render.com/docs/free)
- [Render Laravel Docker Deployment](https://render.com/docs/deploy-php-laravel-docker)
- [Supabase Pricing](https://supabase.com/pricing)
- [Supabase Storage](https://supabase.com/docs/guides/storage)
- [Laravel Deployment](https://laravel.com/docs/11.x/deployment)
