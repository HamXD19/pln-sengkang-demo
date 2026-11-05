# ⚡ Sistem Informasi Siklus Rapat Koordinasi & Manajemen Isu Operasional
### PT PLN Nusantara Power — UP Sengkang

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Vue Version](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3.x-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)

Aplikasi berbasis web untuk standardisasi siklus rapat koordinasi mingguan, evaluasi pekerjaan backlog ($N-1$ & $N$), eskalasi isu operasional berjenjang (Staf $\rightarrow$ TL $\rightarrow$ ASMAN $\rightarrow$ MUP), dan pembuatan notulensi resmi otomatis di lingkungan PT PLN Nusantara Power Unit Pelaksana Pengendalian Pembangkitan Sengkang.

---

## 📋 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Kebutuhan Server (Server Requirements)](#-kebutuhan-server-server-requirements)
3. [Panduan Pemasangan di Hosting](#-panduan-pemasangan-di-hosting)
   - [Opsi 1: Shared Hosting / cPanel (Rekomendasi Pemula & Praktis)](#opsi-1-shared-hosting--cpanel)
   - [Opsi 2: VPS (Ubuntu/Debian dengan Nginx / Apache)](#opsi-2-vps-virtual-private-server)
   - [Opsi 3: Serverless / Cloud (Vercel)](#opsi-3-serverless--cloud-vercel)
4. [Instalasi di Lingkungan Lokal (Localhost Development)](#-instalasi-di-lingkungan-lokal-localhost-development)
5. [Struktur Pengguna & Akun Default](#-struktur-pengguna--akun-default)
6. [Pemecahan Masalah (Troubleshooting)](#-pemecahan-masalah-troubleshooting)

---

## 🚀 Fitur Utama

- **Siklus Koordinasi Mingguan:**
  - *Senin Pagi:* Rapat Teknis Seksi (Blok 1, Blok 2, Pemeliharaan, Enjiniring, K3).
  - *Senin Siang:* Konsolidasi Bidang bersama Asisten Manajer (ASMAN).
  - *Selasa Pagi:* Rapat Tinjauan Manajemen (RTM) bersama Manajer Unit Pelaksana (MUP).
- **Manajemen Isu Lapangan & Eskalasi Berjenjang:** Jalur formal penanganan kendala dari Staf hingga MUP disertai batas SLA dan status evaluasi.
- **Backlog Pekerjaan Otomatis ($N-1$ & $N$):** Pekerjaan minggu lalu yang tertunda otomatis tercatat untuk dievaluasi pada rapat minggu berjalan.
- **Notulensi Rapat & Cetak PDF:** Auto-generate notulensi dan berita acara resmi rapat siap cetak atau ekspor.
- **Role-Based Access Control (RBAC):** Hak akses berjenjang untuk MUP, ASMAN, TL, dan Staf.

---

## 💻 Kebutuhan Server (Server Requirements)

Sebelum melakukan instalasi di hosting atau server, pastikan server memenuhi spesifikasi berikut:

- **PHP:** Versi `>= 8.2`
- **Ekstensi PHP Wajib:**
  - `BCMath`
  - `Ctype`
  - `cURL`
  - `DOM` / `XML`
  - `Fileinfo`
  - `Filter`
  - `JSON`
  - `Mbstring`
  - `OpenSSL`
  - `PCRE`
  - `PDO` & driver database (`pdo_mysql` untuk MySQL / MariaDB, atau `pdo_sqlite`)
  - `Session`
  - `Tokenizer`
- **Database:** MySQL `>= 8.0` / MariaDB `>= 10.4` / SQLite 3
- **Composer:** Versi `>= 2.2`
- **Node.js & NPM:** Node.js `>= 18.x` & NPM `>= 9.x` (digunakan saat build frontend)
- **Web Server:** Apache (dengan `mod_rewrite` aktif) atau Nginx

---

## 🌐 Panduan Pemasangan di Hosting

Pilih salah satu metode deployment yang sesuai dengan jenis hosting yang Anda gunakan:

---

### OPSI 1: Shared Hosting / cPanel

Shared hosting cPanel adalah pilihan paling umum. Ada **dua metode penataan struktur folder**:

#### METODE A (Sangat Direkomendasikan - Standar Keamanan Laravel)
Memisahkan kode sistem di luar `public_html` agar file konfigurasi (`.env`), kode sumber, dan storage terlindungi dari akses publik.

##### Langkah 1: Build Aset Frontend di Komputer Lokal
Di komputer lokal Anda, jalankan perintah berikut untuk meng-compile file Vue & Tailwind ke dalam folder `public/build`:
```bash
npm install
npm run build
```
Pastikan folder `public/build/` sudah terbuat dan berisi aset css & js.

##### Langkah 2: Persiapkan File ZIP Proyek
Kemas seluruh file proyek ke dalam format `.zip`, **KECUALI**:
- Folder `node_modules/`
- Folder `.git/`
*(Folder `vendor/` boleh disertakan jika hosting Anda tidak memiliki fitur SSH / Terminal cPanel).*

##### Langkah 3: Upload ke cPanel
1. Login ke **cPanel**.
2. Buka menu **File Manager**.
3. Masuk ke direktori root akun hosting Anda (satu level di atas `public_html`, biasanya `/home/username/`).
4. Buat folder baru, misalnya bernama `plnsengkang`.
5. Upload file `.zip` tadi ke dalam folder `/home/username/plnsengkang/` lalu lakukan **Extract**.
6. Pindahkan seluruh isi dari folder `/home/username/plnsengkang/public/` ke dalam folder `/home/username/public_html/`.

##### Langkah 4: Sesuaikan File `index.php` di `public_html`
Buka file `/home/username/public_html/index.php` dengan Code Editor cPanel, lalu ubah baris path berikut agar mengarah ke folder `plnsengkang`:

```php
// Cari baris berikut:
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
```

Ubah menjadi:
```php
if (file_exists($maintenance = __DIR__.'/../plnsengkang/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../plnsengkang/vendor/autoload.php';

$app = require_once __DIR__.'/../plnsengkang/bootstrap/app.php';
```
> **Catatan:** Jika Anda mengarahkan domain/subdomain ke folder khusus selain `public_html` (misal `public_html/rapat/`), sesuaikan path relatif `../plnsengkang` sesuai posisi foldernya.

##### Langkah 5: Buat Database di cPanel
1. Di cPanel, buka menu **MySQL® Database Wizard**.
2. Buat database baru (misal: `username_plnsengkang`).
3. Buat user database baru dan buat password yang kuat.
4. Berikan hak akses penuh (**ALL PRIVILEGES**) pada user terhadap database tersebut.

##### Langkah 6: Konfigurasi File `.env`
1. Buka folder `/home/username/plnsengkang/`.
2. Jika file `.env` belum ada, rename atau salin file `.env.example` menjadi `.env`.
3. Edit file `.env` dan sesuaikan nilainya:
```env
APP_NAME="PLN UP Sengkang"
APP_ENV=production
APP_KEY=base64:... (akan digenerate atau gunakan key lokal)
APP_DEBUG=false
APP_URL=https://domainanda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=username_plnsengkang
DB_USERNAME=username_plnuser
DB_PASSWORD=password_database_anda

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=public
```

##### Langkah 7: Generate Key, Migrasi, & Seeding Database
**Jika Hosting Memiliki Akses Terminal / SSH cPanel:**
Buka menu **Terminal** di cPanel, lalu ketik perintah berikut:
```bash
cd /home/username/plnsengkang
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Jika Hosting TIDAK Memiliki Terminal / SSH:**
1. Ekspor database lokal Anda via phpMyAdmin lokal ke file `.sql`.
2. Buka menu **phpMyAdmin** di cPanel hosting, pilih database yang telah dibuat, lalu lakukan **Import** file `.sql` tersebut.
3. Untuk membuat symlink storage tanpa terminal, buat file baru bernama `symlink.php` di dalam `public_html/` dengan isi:
```php
<?php
$targetFolder = $_SERVER['DOCUMENT_ROOT'] . '/../plnsengkang/storage/app/public';
$linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
if (symlink($targetFolder, $linkFolder)) {
    echo 'Symlink berhasil dibuat!';
} else {
    echo 'Gagal membuat symlink.';
}
```
Akses file tersebut sekali melalui browser (`https://domainanda.com/symlink.php`), lalu **segera hapus file `symlink.php` tersebut demi keamanan**.

##### Langkah 8: Atur Izin Akses Folder (Permissions)
Pastikan folder berikut memiliki permission write (`775` atau `755`):
- `/home/username/plnsengkang/storage/`
- `/home/username/plnsengkang/bootstrap/cache/`

---

#### METODE B (Jika Domain / Subdomain Bisa Diatur Document Root-nya)
Jika paket hosting Anda mengizinkan pengaturan Document Root pada Addon Domain / Subdomain:
1. Upload seluruh file proyek ke folder `/home/username/plnsengkang/`.
2. Di cPanel menu **Domains** / **Subdomains**, atur **Document Root** langsung mengarah ke:
   ```text
   /home/username/plnsengkang/public
   ```
3. Langkah konfigurasi database, `.env`, migrasi, dan `storage:link` sama seperti Langkah 5, 6, dan 7 di atas.

---

### OPSI 2: VPS (Virtual Private Server)
*(Ubuntu 22.04 / 24.04 LTS dengan Nginx & PHP 8.2+)*

#### 1. Persiapan Server & Paket Pendukung
Update server dan install PHP 8.2 beserta modul yang dibutuhkan:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server git curl unzip
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-sqlite3
```

Install Composer dan Node.js:
```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

#### 2. Clone Repositori Proyek
```bash
cd /var/www
sudo git clone https://github.com/HamXD19/pln-sengkang-demo.git plnsengkang
cd plnsengkang
```

#### 3. Install Dependensi & Build Aset
```bash
# Install paket PHP untuk production
composer install --no-dev --optimize-autoloader

# Install dependensi frontend dan build aset Vite
npm install
npm run build
```

#### 4. Konfigurasi Lingkungan (`.env`)
```bash
cp .env.example .env
nano .env
```
Sesuaikan konfigurasi database, `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL`.

Lalu jalankan inisialisasi aplikasi:
```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize
```

#### 5. Pengaturan Hak Akses & Permission
```bash
sudo chown -R www-data:www-data /var/www/plnsengkang
sudo chmod -R 775 /var/www/plnsengkang/storage
sudo chmod -R 775 /var/www/plnsengkang/bootstrap/cache
```

#### 6. Konfigurasi Nginx Server Block
Buat file konfigurasi Nginx:
```bash
sudo nano /etc/nginx/sites-available/plnsengkang
```
Isi dengan konfigurasi berikut:
```nginx
server {
    listen 80;
    server_name plnsengkang.yourdomain.com; # Ganti dengan domain atau IP server Anda
    root /var/www/plnsengkang/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Aktifkan konfigurasi dan reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/plnsengkang /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 7. Pasang SSL Gratis (Let's Encrypt Certbot)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d plnsengkang.yourdomain.com
```

#### 8. Setup Background Worker & Scheduler (Opsional)
Jalankan cron job Laravel Scheduler:
```bash
sudo crontab -e -u www-data
```
Tambahkan baris berikut di baris paling bawah:
```text
* * * * * cd /var/www/plnsengkang && php artisan schedule:run >> /dev/null 2>&1
```

---

### OPSI 3: Serverless / Cloud (Vercel)

Proyek ini sudah dilengkapi dengan file konfigurasi [`vercel.json`](vercel.json) dan runtime bridge di [`api/index.php`](api/index.php).

1. Buat akun di [Vercel](https://vercel.com) dan hubungkan dengan repository GitHub ini.
2. Tambahkan **Environment Variables** di dashboard project Vercel:
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: *Hasil generate dari lokal*
   - `DB_CONNECTION`: `mysql` (hubungkan ke managed database seperti PlanetScale, Aiven, Supabase, atau Neon)
   - Konfigurasi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
3. Klik **Deploy**. Vercel akan otomatis mengeksekusi build script dan mendeploy aplikasi.

---

## 💻 Instalasi di Lingkungan Lokal (Localhost Development)

Jika Anda ingin menjalankan aplikasi di komputer lokal untuk kebutuhan pengembangan:

```bash
# 1. Clone repository
git clone https://github.com/HamXD19/pln-sengkang-demo.git
cd pln-sengkang-demo

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate App Key
php artisan key:generate

# 5. Jalankan migrasi dan seeder database awal
php artisan migrate --seed

# 6. Hubungkan storage
php artisan storage:link

# 7. Install paket frontend Node.js
npm install

# 8. Jalankan server lokal (otomatis menjalankan server PHP, Vite, Queue, dan Logs)
composer run dev
```
Akses aplikasi melalui browser di: `http://127.0.0.1:8000`

---

## 👥 Struktur Pengguna & Akun Default

Setelah menjalankan migrasi dengan opsi `--seed`, sistem telah menyediakan akun pengguna terstruktur berdasarkan hierarki PLN UP Sengkang:

| Peran (Role) | Nama Pengguna / Jabatan | Email Default | Password Default | Akses Utama |
| :--- | :--- | :--- | :--- | :--- |
| **MUP** | Manajer Unit Pelaksana (MUP) | `mup.sengkang@pln.co.id` | `password` | Seluruh Modul, RTM, Eksekutif Dashboard, Master Data |
| **ASMAN** | Asisten Manajer Operasi & Pemeliharaan | `asman.opshar@pln.co.id` | `password` | Konsolidasi Bidang, Disposisi & Eskalasi Isu, Task Backlog |
| **ASMAN** | Asisten Manajer Enjiniring | `asman.enjiniring@pln.co.id` | `password` | Koordinasi Teknis, Evaluasi Keandalan, Task Backlog |
| **ASMAN** | Asisten Manajer Keuangan & Umum | `asman.keu.umum@pln.co.id` | `password` | Support Layanan, Pengumuman Manajemen, Anggaran |
| **TL** | Team Leader Operasi Blok 1 | `tl.operasi.b1@pln.co.id` | `password` | Rapat Teknis Seksi, Input Isu Lapangan, Penugasan Staf |
| **TL** | Team Leader Pemeliharaan Mesin | `tl.har.mesin@pln.co.id` | `password` | Work Order Perbaikan, Jadwal Overhaul, Manajemen Isu |
| **TL** | Team Leader K3 & Kam | `tl.k3kam@pln.co.id` | `password` | Safety Issue, Incident Reporting, Rapat K3 |
| **STAFF** | Operator Pembangkit / Staf | `operator.b1@pln.co.id` | `password` | Pelaporan Kendala, Update Status Pekerjaan Pribadi |

> 📌 **Catatan Keamanan:** Segera ganti seluruh password akun default ini setelah aplikasi terpasang di server production! Daftar lengkap akun dan struktur NIP pegawai dapat dilihat di dokumen [DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.md](DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.md).

---

## 🛠️ Pemecahan Masalah (Troubleshooting)

### 1. Muncul Error "500 Server Error" setelah Upload ke cPanel
- **Penyebab:** Versi PHP di cPanel masih di bawah 8.2 atau ekstensi PHP belum aktif.
- **Solusi:** Buka menu **Select PHP Version** di cPanel, ubah ke PHP **8.2** atau **8.3**, lalu pastikan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `bcmath`, dan `xml` sudah dicentang.
- Periksa log detail di file `storage/logs/laravel.log`.

### 2. Tampilan Berantakan / CSS dan JS Tidak Termuat (Vite Manifest Not Found)
- **Penyebab:** Folder `public/build` belum ter-compile atau belum ter-upload ke hosting.
- **Solusi:** Jalankan `npm run build` di lokal, kemudian pastikan folder `public/build` di-upload ke direktori `public_html/build` (atau `public/build`).

### 3. Gambar / File Lampiran Tidak Muncul (Error 404 pada `/storage/...`)
- **Penyebab:** Symlink storage belum terbuat atau path symlink salah.
- **Solusi:** Jalankan `php artisan storage:link` via Terminal cPanel / SSH. Jika hosting tidak ada Terminal, gunakan skrip `symlink.php` yang dijelaskan pada [Langkah 7 Opsi 1](#langkah-7-generate-key-migrasi--seeding-database).

### 4. Tombol atau Form Mengalami Error "CSRF Token Mismatch" (Error 419)
- **Penyebab:** Konfigurasi session atau cookie domain di `.env` belum sesuai dengan domain aktif.
- **Solusi:** Pastikan `APP_URL` di file `.env` sudah menggunakan domain yang benar dengan protokol `https://` (jika menggunakan SSL). Jalankan `php artisan config:clear`.

### 5. Permission Denied pada Folder `storage` atau `bootstrap/cache`
- **Penyebab:** Web server tidak memiliki hak tulis (write permission) ke folder cache.
- **Solusi:** Ubah permission folder `storage/` dan `bootstrap/cache/` beserta sub-foldernya menjadi `775` (atau `755` tergantung konfigurasi user server hosting Anda).

---

## 📄 Dokumen Pendukung Lainnya

- 📖 [Panduan Pengguna Sistem (Manual Book)](MANUAL_BOOK_PLN_SENGKANG.md) — Panduan pengoperasian modul bagi pengguna.
- 👥 [Daftar Akun Pegawai PLN UP Sengkang](DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.md) — Rincian NIP, unit seksi, dan email resmi.
- 🏛️ [Laporan Struktur Organisasi & Hak Akses](LAPORAN_STRUKTUR_DAN_HAK_AKSES_PLN_SENGKANG.md) — Matriks peran dan hierarki wewenang sistem.

---

## ⚖️ Lisensi

Sistem ini dikembangkan khusus untuk unit operasional **PT PLN Nusantara Power — UP Sengkang**. Hak cipta dan kepemilikan kode dilindungi oleh ketentuan instansi terkait.
