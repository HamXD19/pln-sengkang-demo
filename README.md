# ⚡ Sistem Informasi Siklus Rapat Koordinasi & Manajemen Isu Operasional
### PT PLN Nusantara Power — UP Sengkang

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Database](https://img.shields.io/badge/Database-Oracle_19c_/_21c_/_23c-F80000?style=for-the-badge&logo=oracle&logoColor=white)](https://www.oracle.com/database/)
[![Vue Version](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3.x-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)

Aplikasi berbasis web untuk standardisasi siklus rapat koordinasi mingguan, evaluasi pekerjaan backlog ($N-1$ & $N$), eskalasi isu operasional berjenjang (Staf $\rightarrow$ TL $\rightarrow$ ASMAN $\rightarrow$ MUP), dan pembuatan notulensi resmi otomatis di lingkungan PT PLN Nusantara Power Unit Pelaksana Pengendalian Pembangkitan Sengkang.

Aplikasi ini menggunakan basis data kelas enterprise **Oracle Database** (didukung oleh package `yajra/laravel-oci8` dan ekstensi PHP `OCI8`).

---

## 📋 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Spesifikasi & Kebutuhan Server Oracle](#-spesifikasi--kebutuhan-server-oracle)
3. [Panduan Pemasangan & Deployment Server](#-panduan-pemasangan--deployment-server)
   - [Opsi 1: VPS / Dedicated / On-Premise Server PLN (Rekomendasi Utama)](#opsi-1-vps--dedicated--on-premise-server-pln-rekomendasi-resmi)
   - [Opsi 2: Docker Container (Oracle Database 23c Free + Web App)](#opsi-2-docker-container-oracle-23c-free)
   - [Opsi 3: Shared Hosting / cPanel (Dengan Remote Oracle Connection)](#opsi-3-shared-hosting--cpanel-koneksi-oracle-remote)
4. [Instalasi di Komputer Lokal (Localhost Development)](#-instalasi-di-komputer-lokal-localhost-development)
5. [Struktur Pengguna & Akun Default](#-struktur-pengguna--akun-default)
6. [Pemecahan Masalah Khusus Oracle (Troubleshooting)](#-pemecahan-masalah-khusus-oracle-troubleshooting)

---

## 🚀 Fitur Utama

- **Siklus Koordinasi Mingguan:**
  - *Senin Pagi:* Rapat Teknis Seksi (Blok 1, Blok 2, Pemeliharaan, Enjiniring, K3).
  - *Senin Siang:* Konsolidasi Bidang bersama Asisten Manajer (ASMAN).
  - *Selasa Pagi:* Rapat Tinjauan Manajemen (RTM) bersama Manajer Unit Pelaksana (MUP).
- **Manajemen Isu Lapangan & Eskalasi Berjenjang:** Jalur formal penanganan kendala dari Staf hingga MUP disertai batas SLA dan status evaluasi.
- **Backlog Pekerjaan Otomatis ($N-1$ & $N$):** Pekerjaan minggu lalu yang tertunda otomatis tercatat untuk dievaluasi pada rapat minggu berjalan.
- **Notulensi Rapat & Cetak PDF (Per Sesi & Per Minggu):** Auto-generate notulensi dan berita acara resmi rapat siap cetak atau ekspor dengan nomor risalah resmi PLN.
- **Penugasan PIC Cerdas (Searchable Autocomplete):** Memilih PIC dari 154 pegawai PLN cukup dengan mengetikkan kata kunci nama, NID, atau jabatan tanpa scroll panjang.
- **Role-Based Access Control (RBAC):** Hak akses berjenjang untuk MUP, ASMAN, TL, dan Staf.

---

## 💻 Spesifikasi & Kebutuhan Server Oracle

Aplikasi ini dirancang untuk berjalan optimal dengan database korporat **Oracle Database**:

- **Database:** Oracle Database `19c`, `21c`, atau `23c Free / XE` (Service Name / Pluggable Database, port `1521`).
- **PHP:** Versi `>= 8.2`
- **Ekstensi PHP Wajib:**
  - `oci8` (PHP OCI8 extension untuk koneksi ke Oracle Database)
  - `pdo_oci` (opsional untuk PDO Oracle)
  - `BCMath`, `Ctype`, `cURL`, `DOM`, `Fileinfo`, `Filter`, `JSON`, `Mbstring`, `OpenSSL`, `PCRE`, `Session`, `Tokenizer`
- **Pustaka Client:** Oracle Instant Client versi `>= 19.x` atau `21.x` (`instantclient-basic` & `instantclient-sdk`)
- **Composer:** Versi `>= 2.2` (dengan package `yajra/laravel-oci8: ^12.11`)
- **Node.js & NPM:** Node.js `>= 18.x` & NPM `>= 9.x` (untuk build frontend Vite & Tailwind)
- **Web Server:** Nginx atau Apache (dengan `mod_rewrite` aktif)

---

## 🌐 Panduan Pemasangan & Deployment Server

### OPSI 1: VPS / Dedicated / On-Premise Server PLN (Rekomendasi Resmi)
*(Berlaku untuk Ubuntu 22.04 / 24.04 LTS atau RHEL / Oracle Linux)*

Metode ini adalah standar resmi untuk aplikasi internal PLN yang terhubung ke server database Oracle.

#### Langkah 1: Install Paket Pendukung & PHP 8.2+
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx git curl unzip libaio1 build-essential php8.2 php8.2-fpm \
    php8.2-dev php-pear php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl \
    php8.2-zip php8.2-gd php8.2-intl
```

#### Langkah 2: Pasang Oracle Instant Client & Ekstensi OCI8
1. Unduh **Oracle Instant Client Basic** dan **SDK** (versi Linux x86-64):
   ```bash
   sudo mkdir -p /opt/oracle
   cd /opt/oracle
   # Contoh versi 21.x (bisa juga versi 19.x)
   sudo wget https://download.oracle.com/otn_software/linux/instantclient/2113000/instantclient-basic-linux.x64-21.13.0.0.0dbru.zip
   sudo wget https://download.oracle.com/otn_software/linux/instantclient/2113000/instantclient-sdk-linux.x64-21.13.0.0.0dbru.zip
   sudo unzip instantclient-basic-linux.x64-21.13.0.0.0dbru.zip
   sudo unzip instantclient-sdk-linux.x64-21.13.0.0.0dbru.zip
   ```

2. Konfigurasi path pustaka Oracle di sistem:
   ```bash
   echo /opt/oracle/instantclient_21_13 | sudo tee /etc/ld.so.conf.d/oracle-instantclient.conf
   sudo ldconfig
   ```

3. Kompilasi dan aktifkan ekstensi PHP `oci8`:
   ```bash
   # Jalankan instalasi OCI8 via PECL
   sudo pecl install oci8
   # Ketika ditanya path instantclient, masukkan:
   # instantclient,/opt/oracle/instantclient_21_13
   ```

4. Daftarkan ekstensi `oci8.so` ke PHP:
   ```bash
   echo "extension=oci8.so" | sudo tee /etc/php/8.2/mods-available/oci8.ini
   sudo ln -s /etc/php/8.2/mods-available/oci8.ini /etc/php/8.2/fpm/conf.d/20-oci8.ini
   sudo ln -s /etc/php/8.2/mods-available/oci8.ini /etc/php/8.2/cli/conf.d/20-oci8.ini
   sudo systemctl restart php8.2-fpm
   ```

5. Verifikasi bahwa OCI8 telah aktif:
   ```bash
   php -m | grep oci8
   # Output harus menampilkan: oci8
   ```

#### Langkah 3: Clone Repositori & Install Dependensi
```bash
cd /var/www
sudo git clone https://github.com/HamXD19/pln-sengkang-demo.git plnsengkang
cd plnsengkang

# Install dependensi PHP (termasuk yajra/laravel-oci8)
composer install --no-dev --optimize-autoloader

# Install dependensi frontend dan build aset Vite
npm install
npm run build
```

#### Langkah 4: Konfigurasi File Lingkungan (`.env`)
Salin file konfigurasi:
```bash
cp .env.example .env
nano .env
```
Sesuaikan konfigurasi koneksi **Oracle Database**:
```env
APP_NAME="PLN UP Sengkang"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://plnsengkang.yourdomain.com

# KONEKSI RESMI ORACLE DATABASE
DB_CONNECTION=oracle
DB_HOST=127.0.0.1                  # Atau IP server Oracle (misal: 10.x.x.x)
DB_PORT=1521                       # Port standar Oracle Listener
DB_DATABASE=freepdb1               # Nama database / Pluggable Database
DB_SERVICE_NAME=freepdb1           # Service Name Oracle (misal: freepdb1 atau orclpdb)
DB_TNS=127.0.0.1:1521/freepdb1     # String TNS koneksi
DB_USERNAME=irham                  # Schema / User Oracle
DB_PASSWORD=Irham123               # Password User Oracle
DB_CHARSET=AL32UTF8
DB_SERVER_VERSION=23c              # 19c / 21c / 23c

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=public
```

#### Langkah 5: Eksekusi Migrasi Oracle & Seeder Data
Jalankan migrasi Laravel DDL khusus Oracle (Yajra OCI8 akan otomatis membuat tabel, sequence, dan trigger Oracle):
```bash
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize
```

#### Langkah 6: Atur Izin Folder (Permissions)
```bash
sudo chown -R www-data:www-data /var/www/plnsengkang
sudo chmod -R 775 /var/www/plnsengkang/storage
sudo chmod -R 775 /var/www/plnsengkang/bootstrap/cache
```

#### Langkah 7: Konfigurasi Nginx Server Block
Buat file konfigurasi Nginx:
```bash
sudo nano /etc/nginx/sites-available/plnsengkang
```
Isi konfigurasi berikut:
```nginx
server {
    listen 80;
    server_name plnsengkang.yourdomain.com;
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
Aktifkan dan restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/plnsengkang /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### OPSI 2: Docker Container (Oracle 23c Free)

Jika server Anda menggunakan lingkungan Docker, Anda dapat menjalankan Oracle Database 23c Free secara instan:

```bash
# 1. Jalankan container Oracle Database 23c Free resmi
docker run -d \
  --name oracle23c \
  -p 1521:1521 \
  -e ORACLE_PWD=Irham123 \
  container-registry.oracle.com/database/free:latest

# 2. Tunggu hingga status database "healthy"
docker logs -f oracle23c

# 3. Pada file .env aplikasi, atur:
# DB_CONNECTION=oracle
# DB_HOST=127.0.0.1
# DB_PORT=1521
# DB_SERVICE_NAME=freepdb1
# DB_USERNAME=system (atau buat user baru)
# DB_PASSWORD=Irham123
```

Lalu jalankan migrasi database:
```bash
php artisan migrate --seed
```

---

### OPSI 3: Shared Hosting / cPanel (Koneksi Oracle Remote)

> ⚠️ **Catatan Penting Mengenai Shared Hosting:**
> Sebagian besar penyedia shared hosting cPanel murah **tidak menyediakan** modul Oracle Instant Client / OCI8 secara bawaan. 
> Jika Anda harus menggunakan cPanel:
> 1. Pastikan paket cPanel Anda adalah **Cloud / Dedicated cPanel** yang memiliki akses root atau modul `ea-php82-php-oci8` yang sudah diaktifkan oleh administrator hosting.
> 2. Database Oracle dapat ditempatkan di server Oracle PLN terpisah (Remote Database), lalu cPanel bertindak sebagai Web Server PHP yang terhubung via IP & port `1521`.
> 3. Konfigurasi Document Root cPanel wajib diarahkan ke folder `public/`.

---

## 💻 Instalasi di Komputer Lokal (Localhost Development)

Untuk menjalankan proyek di lingkungan pengembangan lokal yang terhubung ke Oracle Database:

```bash
# 1. Clone repository
git clone https://github.com/HamXD19/pln-sengkang-demo.git
cd pln-sengkang-demo

# 2. Install dependensi PHP (termasuk yajra/laravel-oci8)
composer install

# 3. Salin file environment Oracle
cp .env.example .env

# 4. Generate App Key
php artisan key:generate

# 5. Pastikan koneksi Oracle di .env sudah sesuai:
# DB_CONNECTION=oracle
# DB_HOST=127.0.0.1
# DB_PORT=1521
# DB_SERVICE_NAME=freepdb1
# DB_USERNAME=irham
# DB_PASSWORD=Irham123

# 6. Jalankan migrasi dan seeder awal di Oracle
php artisan migrate --seed

# 7. Hubungkan folder storage publik
php artisan storage:link

# 8. Install dependensi frontend & build
npm install
npm run build

# 9. Jalankan server lokal
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

## 🛠️ Pemecahan Masalah Khusus Oracle (Troubleshooting)

### 1. Error `Call to undefined function oci_connect()` atau `Driver [oracle] not supported`
- **Penyebab:** Ekstensi PHP OCI8 belum terpasang atau belum diaktifkan di file `php.ini`.
- **Solusi:** 
  1. Pastikan Oracle Instant Client sudah diekstrak ke `/opt/oracle/instantclient_...` dan terdaftar di `ldconfig`.
  2. Install modul OCI8 dengan `sudo pecl install oci8`.
  3. Pastikan baris `extension=oci8.so` ada di konfigurasi PHP CLI dan PHP-FPM, lalu restart PHP (`sudo systemctl restart php8.2-fpm`).

### 2. Error `ORA-12541: TNS:no listener`
- **Penyebab:** Oracle Database Listener belum berjalan di server database atau port `1521` diblokir firewall.
- **Solusi:**
  1. Periksa status listener di server Oracle: `lsnrctl status`.
  2. Jika mati, jalankan dengan: `lsnrctl start`.
  3. Pastikan port `1521` dibuka pada firewall server (`sudo ufw allow 1521/tcp`).

### 3. Error `ORA-12514: TNS:listener does not currently know of service requested`
- **Penyebab:** Nilai `DB_SERVICE_NAME` di file `.env` tidak cocok dengan service name aktif di Oracle.
- **Solusi:**
  1. Periksa service name yang terdaftar pada listener dengan perintah `lsnrctl status`.
  2. Pada Oracle 23c Free, service default umumnya adalah `freepdb1` (huruf kecil). Pada Oracle 19c/21c biasanya `orclpdb` atau `orcl`.
  3. Sesuaikan `DB_SERVICE_NAME=freepdb1` dan `DB_DATABASE=freepdb1` di `.env`.

### 4. Error `ORA-01017: invalid username/password; logon denied`
- **Penyebab:** Username atau password Oracle salah, atau user belum diberikan hak akses (grant).
- **Solusi:**
  1. Pastikan kredensial di `.env` sudah benar.
  2. Jika membuat user baru di Oracle, pastikan sudah diberi wewenang:
     ```sql
     GRANT CONNECT, RESOURCE, DBA TO irham;
     ALTER USER irham QUOTA UNLIMITED ON USERS;
     ```

### 5. Gambar / File Lampiran Tidak Muncul (Error 404 pada `/storage/...`)
- **Penyebab:** Symlink storage belum terbuat.
- **Solusi:** Jalankan `php artisan storage:link`.

---

## 📄 Dokumen Pendukung Lainnya

- 📖 [Panduan Pengguna Sistem (Manual Book)](MANUAL_BOOK_PLN_SENGKANG.md) — Panduan pengoperasian modul bagi pengguna.
- 👥 [Daftar Akun Pegawai PLN UP Sengkang](DAFTAR_AKUN_PEGAWAI_PLN_SENGKANG.md) — Rincian NIP, unit seksi, dan email resmi.
- 🏛️ [Laporan Struktur Organisasi & Hak Akses](LAPORAN_STRUKTUR_DAN_HAK_AKSES_PLN_SENGKANG.md) — Matriks peran dan hierarki wewenang sistem.

---

## ⚖️ Lisensi

Sistem ini dikembangkan khusus untuk unit operasional **PT PLN Nusantara Power — UP Sengkang**. Hak cipta dan kepemilikan kode dilindungi oleh ketentuan instansi terkait.
