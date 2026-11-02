# PANDUAN PENGGUNA SISTEM (MANUAL BOOK)
## APLIKASI SIKLUS RAPAT KOORDINASI MINGGUAN & MANAJEMEN ISU OPERASIONAL
### PT PLN NUSANTARA POWER - UNIT PELAKSANA PENGENDALIAN PEMBANGKITAN SENGKANG (UP SENGKANG)

---

## DAFTAR ISI
1. [INFORMASI DOKUMEN & PENDAHULUAN](#1-informasi-dokumen--pendahuluan)
2. [STRUKTUR PENGGUNA & MATRIKS HAK AKSES](#2-struktur-pengguna--matriks-hak-akses)
3. [PETUNJUK MASUK & NAVIGASI SISTEM (LOGIN & PROFIL)](#3-petunjuk-masuk--navigasi-sistem)
4. [PANDUAN MODUL 1: DASHBOARD EKSEKUTIF & OPERASIONAL](#4-panduan-modul-1-dashboard-eksekutif--operasional)
5. [PANDUAN MODUL 2: SIKLUS RAPAT MINGGUAN & WORKSPACE INTERAKTIF](#5-panduan-modul-2-siklus-rapat-mingguan--workspace-interaktif)
6. [PANDUAN MODUL 3: MANAJEMEN ISU & ESKALASI BERJENJANG](#6-panduan-modul-3-manajemen-isu--eskalasi-berjenjang)
7. [PANDUAN MODUL 4: MANAJEMEN PEKERJAAN & TASK BACKLOG (N & N-1)](#7-panduan-modul-4-manajemen-pekerjaan--task-backlog)
8. [PANDUAN MODUL 5: PUSAT INFORMASI & PENGUMUMAN MANAJEMEN](#8-panduan-modul-5-pusat-informasi--pengumuman-manajemen)
9. [PANDUAN MODUL 6: PELAPORAN & EKSPOR DATA](#9-panduan-modul-6-pelaporan--ekspor-data)
10. [PANDUAN MODUL 7: ADMINISTRASI MASTER DATA (MUP & ADMIN)](#10-panduan-modul-7-administrasi-master-data)
11. [TANYA JAWAB & PEMECAHAN MASALAH (FAQ & TROUBLESHOOTING)](#11-tanya-jawab--pemecahan-masalah)

---

## 1. INFORMASI DOKUMEN & PENDAHULUAN

### 1.1 Identitas Dokumen
* **Nama Sistem:** Sistem Informasi Siklus Rapat Koordinasi Mingguan, Manajemen Isu & Evaluasi Kinerja Lapangan (Rapat & Issue Tracking UP Sengkang).
* **Instansi Klien:** PT PLN Nusantara Power - UP Sengkang.
* **Target Pengguna:** Manajer Unit Pelaksana (MUP), Asisten Manajer (ASMAN), Team Leader (TL), dan Staf Pelaksana/Operator Pembangkit.
* **Standar Acuan:** Business System Design (BSD) Section 2 & 3 Operasional PLN Nusantara Power.

### 1.2 Tujuan Aplikasi
Aplikasi ini dikembangkan untuk:
1. **Menstandardisasi Siklus Rapat Koordinasi Mingguan:** Mengotomatiskan siklus koordinasi berjenjang (Senin Pagi $\rightarrow$ Senin Siang $\rightarrow$ Selasa Pagi).
2. **Evaluasi Pekerjaan Presisi (N-1 & N):** Menghubungkan secara otomatis pekerjaan tertunda dari minggu lalu ($N-1$) ke dalam agenda rapat minggu berjalan ($N$).
3. **Penyelesaian Isu & Eskalasi Berjenjang:** Menyediakan jalur formal penanganan kendala dari level Staf Lapangan, Team Leader (TL), Asisten Manajer (ASMAN), hingga Manajer Unit Pelaksana (MUP).
4. **Digitalisasi Notulensi & Berita Acara:** Menghasilkan notulensi rapat resmi dan dokumentasi keputusan secara instan siap cetak (PDF).

---

## 2. STRUKTUR PENGGUNA & MATRIKS HAK AKSES

Sistem menerapkan prinsip **Role-Based Access Control (RBAC)** dan **Data Scoping** sesuai jenjang hierarki organisasi PLN UP Sengkang:

```
┌────────────────────────────────────────────────────────┐
│         MANAJER UNIT PELAKSANA (MUP / ADMIN)           │
│   Pengambil Keputusan Tertinggi, RTM, Seluruh Bidang   │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│              ASISTEN MANAJER (ASMAN)                   │
│   Koordinator Bidang (Operasi, Har, Eng, Bus. Support) │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│                TEAM LEADER (TL)                        │
│   Pimpinan Seksi / Unit (Blok 1, Blok 2, Har, K3, dll) │
└───────────────────────────┬────────────────────────────┘
                            │
┌───────────────────────────▼────────────────────────────┐
│               STAF PELAKSANA / OPERATOR                │
│   Pelaksana Lapangan, Input Isu, Pembaruan Progres     │
└────────────────────────────────────────────────────────┘
```

### Tabel Matriks Hak Akses Fitur:

| Fitur / Menu Utama | STAF | TEAM LEADER (TL) | ASISTEN MANAJER (ASMAN) | MUP / ADMIN |
| :--- | :---: | :---: | :---: | :---: |
| **Melihat Dashboard & KPI** | Unit Sendiri | Seksi Sendiri | Bidang Sendiri | Seluruh UP Sengkang |
| **Input Isu / Kendala Baru** | Ya | Ya | Ya | Ya |
| **Mengambil Keputusan Isu** | Tidak | Ya (Level Seksi) | Ya (Level Bidang) | Ya (Level Tertinggi) |
| **Eskalasi Isu ke Atasan** | Tidak | Ya (ke ASMAN) | Ya (ke MUP) | Tidak (Tingkat Puncak) |
| **Input Tugas / Pekerjaan Baru** | Ya (Tugas Pribadi) | Ya (Tugas Seksi) | Ya (Tugas Bidang) | Ya (Semua Tugas) |
| **Update Progres Tugas (0-100%)**| Ya | Ya | Ya | Ya |
| **Membuat Agenda Rapat Baru** | Tidak | Ya (Rapat TL+Staf) | Ya (Rapat Asman+TL) | Ya (Semua Forum/RTM)|
| **Akses Ruang Rapat (Workspace)**| Hadir & Diskusi | Pimpin Rapat TL | Pimpin Rapat Asman | Pimpin Rapat RTM |
| **Menyelesaikan Sesi Rapat** | **Ditolak (Ketat)** | Ya (Rapat Sendiri) | Ya (Rapat Sendiri) | Ya (Semua Rapat) |
| **Cetak Notulen & Berita Acara**| Ya | Ya | Ya | Ya |
| **Kelola Master Data Pengguna**| Tidak | Tidak | Tidak | **Ya Khusus MUP/Admin** |

---

## 3. PETUNJUK MASUK & NAVIGASI SISTEM

### 3.1 Halaman Masuk (Login)
1. Buka peramban (Google Chrome / Microsoft Edge / Safari) dan akses alamat web aplikasi PLN.
2. Masukkan **Alamat Email PLN** dan **Kata Sandi**.
3. Klik tombol **"Masuk ke Sistem"**.

> **Akun Default untuk Pengujian / Demonstrasi:**
> * **Manajer Unit Pelaksana (MUP):** `mup@plnnp.co.id` | Kata Sandi: `password123`
> * **Asisten Manajer Operasi (ASMAN):** `asman@plnnp.co.id` | Kata Sandi: `password123`
> * **Team Leader Produksi Blok 1 A (TL):** `tl@plnnp.co.id` | Kata Sandi: `password123`
> * **Staf Pelaksana (Operator):** `budi@plnnp.co.id` | Kata Sandi: `password123`

### 3.2 Navigasi Antarmuka (Layout)
* **Bilah Atas (Topbar):** Menampilkan tanggal hari ini (Format Indonesia), indikator siklus minggu aktif ($W_{40}, W_{41}$, dst), lonceng notifikasi agenda mendatang, serta menu profil akun.
* **Bilah Samping (Sidebar Menu):**
  * 📊 **Dashboard:** Ringkasan statistik & eksekutif.
  * 📋 **Pekerjaan (Task):** Pengelolaan daftar penugasan & backlog evaluasi.
  * ⚠️ **Isu & Kendala:** Pelaporan kendala teknis dan riwayat eskalasi.
  * 🤝 **Rapat Koordinasi:** Jadwal dan Workspace interaktif ruang rapat.
  * 📢 **Pusat Informasi:** Pengumuman resmi dari pimpinan UP.
  * 📑 **Laporan:** Ekspor data dan matriks evaluasi mingguan.
  * ⚙️ **Master Data:** (Hanya tampil untuk MUP/Admin).

### 3.3 Mengubah Profil & Kata Sandi
1. Klik nama akun Anda di pojok kanan atas bilah navigasi $\rightarrow$ pilih **Profil Saya**.
2. Anda dapat memperbarui Nama, Nomor Pegawai, dan Nomor Kontak.
3. Untuk mengubah kata sandi, isi **Kata Sandi Saat Ini**, lalu masukkan **Kata Sandi Baru** (minimal 8 karakter) $\rightarrow$ klik **Simpan Kata Sandi**.

---

## 4. PANDUAN MODUL 1: DASHBOARD EKSEKUTIF & OPERASIONAL

Dashboard dirancang adaptif berdasarkan peran pengguna yang sedang login:

```
┌────────────────────────────────────────────────────────────────────────┐
│  SIKLUS MINGGU BERJALAN: MINGGU 41 (OKTOBER 2026)                      │
├─────────────────┬──────────────────┬─────────────────┬─────────────────┤
│ TOTAL ISU AKTIF │ ISU KRITIKAL     │ TUGAS SELESAI   │ RAPAT TERJADWAL │
│     14 Isu      │    2 Isu (Merah) │   82% Selesai   │     3 Agenda    │
└─────────────────┴──────────────────┴─────────────────┴─────────────────┘
```

### Langkah Penggunaan Dashboard:
1. **Melihat Ringkasan Angka Kunci (Metric Cards):**
   * Periksa jumlah isu berstatus *Kritikal* yang membutuhkan mitigasi darurat.
   * Pantau persentase ketuntasan pekerjaan seksi/bidang.
2. **Filter Bidang (Khusus MUP & ASMAN):**
   * Gunakan dropdown filter di bagian atas dashboard untuk memfilter data per bidang (*Operasi, Pemeliharaan, Engineering, Business Support*).
3. **Pemberitahuan Cepat Lonceng:**
   * Klik ikon lonceng di header atas untuk melihat agenda rapat terdekat atau isu kritis yang menunggu disposisi Anda.

---

## 5. PANDUAN MODUL 2: SIKLUS RAPAT MINGGUAN & WORKSPACE INTERAKTIF

Modul Rapat adalah jantung tata kelola operasional PLN UP Sengkang yang menghubungkan evaluasi pekerjaan dan pengambilan keputusan.

### 5.1 Siklus Baku Rapat Mingguan UP Sengkang
Sistem telah mengonfigurasi 3 siklus baku secara terstruktur:
1. **🌅 Senin Pagi (08:30 - 10:00 WITA): Rapat TL + Staf**
   * *Level:* Seksi Unit (Produksi Blok 1, Blok 2, Har Listrik, Rendal, dll).
   * *Fokus:* Evaluasi pekerjaan lapangan minggu $N-1$ dan penampungan isu staf.
2. **☀️ Senin Siang (13:30 - 15:30 WITA): Rapat Asman + TL**
   * *Level:* Bidang (Operasi, Pemeliharaan, Engineering, Business Support).
   * *Fokus:* Sinkronisasi lintas seksi, evaluasi backlog bidang, tindak lanjut eskalasi dari TL.
3. **🏛️ Selasa Pagi (08:30 - 11:00 WITA): Rapat Tinjauan Manajemen (RTM) Asman + MUP**
   * *Level:* Manajemen Puncak (MUP & Seluruh Asisten Manajer).
   * *Fokus:* Keputusan strategis, ketersediaan anggaran/material kritis, dan penugasan prioritas unit.

---

### 5.2 Cara Membuat / Menjadwalkan Sesi Rapat (Fitur Auto-Preset 1-Klik)

```
[ + Buat Agenda Rapat ]
  ├── 🌅 Pilih Preset: Senin Pagi (TL + Staf)  ──> Tanggal, Jam 08:30, Ruang Seksi Otomatis Terisi
  ├── ☀️ Pilih Preset: Senin Siang (Asman + TL) ──> Tanggal, Jam 13:30, Ruang Bidang Otomatis Terisi
  └── 🏛️ Pilih Preset: Selasa Pagi (RTM MUP)    ──> Tanggal, Jam 08:30, Ruang Utama Otomatis Terisi
```

1. Buka menu **Rapat Koordinasi** di sidebar.
2. Pada bagian atas halaman, klik tombol **"+ Setel Room"** pada banner siklus, atau klik **"+ Buat Agenda Rapat"**.
3. **Pilih Preset Siklus Baku:**
   * Klik salah satu tombol preset (misal: *Senin Pagi: Rapat TL + Staf*).
   * Sistem secara cerdas akan langsung mengisi:
     * **Tanggal Rapat:** Otomatis memilih tanggal siklus yang tepat (jika hari ini sudah melewati Senin, sistem cerdas akan langsung memilih Senin minggu berikutnya).
     * **Jam Mulai & Selesai:** Otomatis terisi (misal: `08:30 - 10:00 WITA`).
     * **Lokasi Default:** Otomatis terisi (misal: *Ruang Rapat Seksi / Operasi*).
4. **Fleksibilitas Jadwal (Jika Ada Kendala):**
   * Kolom tanggal, jam, dan lokasi **tetap dapat diubah manual** apabila ada hari libur nasional, rapat darurat, atau pergeseran jadwal pimpinan.
5. **Mode Rapat & Tautan Zoom:**
   * Pilih mode: *Offline*, *Online (Zoom)*, atau *Hybrid*.
   * Jika memilih Online/Hybrid, masukkan link Zoom, Meeting ID, dan Passcode.
6. **Memilih Isu yang Dibahas:**
   * Centang daftar isu operasional aktif yang ingin dibahas dalam sesi rapat tersebut.
7. Klik tombol **"Simpan & Jadwalkan"**. Agenda rapat akan langsung muncul di baris teratas tab *Jadwal Aktif / Mendatang*.

---

### 5.3 Memasuki Ruang Rapat (Workspace Interaktif)

Untuk memulai atau mengikuti rapat yang sedang berlangsung:
1. Pada kartu rapat, klik tombol **"Buka Workspace"** (atau **"Masuk LIVE"**).
2. Anda akan diarahkan ke antarmuka terpadu Workspace Rapat yang terdiri dari:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ HEADER: Nama Rapat | Status (LIVE) | Waktu | [Cetak Notulen] | [Selesaikan Rapat]     │
├───────────────────────────────┬────────────────────────────────┬───────────────────────┤
│ KOLOM KIRI (Evaluasi & Tugas) │ KOLOM TENGAH (Isu & Keputusan) │ KOLOM KANAN (Diskusi) │
│ • Pengumuman Arahan Pimpinan  │ • Daftar Isu Diagendakan       │ • Chat Room Realtime  │
│ • Evaluasi Pekerjaan N-1      │ • Tombol Ambil Keputusan (TL+) │ • Kehadiran Peserta   │
│ • Form Tambah Tugas Baru (N)  │ • Tombol Eskalasi Isu (TL/Asm) │ • Log Aktivitas Rapat │
└───────────────────────────────┴────────────────────────────────┴───────────────────────┘
```

#### A. Evaluasi Pekerjaan Minggu Lalu ($N-1$)
* Pada panel kiri, tampil seluruh pekerjaan tertunda (backlog) dari minggu sebelumnya yang belum mencapai 100%.
* Pimpinan dan PIC dapat langsung meninjau persentase progres dan hambatan penyelesaian.

#### B. Mengambil Keputusan & Eskalasi di Dalam Rapat
* Pada panel tengah, klik isu yang sedang dibahas:
  * **Ambil Keputusan (Khusus TL, ASMAN, MUP):** Masukkan solusi/arahan pimpinan, tetapkan tanggal target, dan pilih PIC penanggung jawab. Isu akan otomatis berstatus *Decided/Resolved*.
  * **Eskalasi ke Atasan (Khusus TL & ASMAN):** Jika kendala tidak dapat diselesaikan di level seksi karena membutuhkan anggaran atau koordinasi antar-bidang, klik **"Eskalasi"** dan masukkan justifikasi eskalasi.

#### C. Memberikan Penugasan Baru (Minggu $N$)
* Klik tombol **"+ Tambah Pekerjaan Baru"** pada panel kiri.
* Masukkan nama tugas, tenggat waktu (*due date*), pilih pelaksana tugas (PIC), dan tentukan tingkat prioritas.
* Tugas baru ini otomatis tercatat sebagai komitmen kerja minggu berjalan ($N$).

#### D. Kolom Diskusi Realtime (Chat Room)
* Seluruh peserta rapat (termasuk Staf) dapat mengetik pesan, memberikan update teknis, atau mengajukan pertanyaan langsung di panel obrolan sebelah kanan. Pesan otomatis tersinkronisasi realtime.

---

### 5.4 Menyelesaikan Rapat & Pengamanan Hak Akses (Role Protection)

> **Aturan Keamanan:**
> Sesuai tata tertib pimpinan PLN UP Sengkang, **Staf Pelaksana TIDAK DIPERKENANKAN menyelesaikan sesi rapat**.
> * Akun dengan peran **STAFF** hanya akan melihat tombol **"Kembali"**.
> * Tombol **"Selesaikan Rapat"** hanya tampil dan dapat dieksekusi oleh **Pimpinan Rapat (Leader)** atau pengguna dengan role **TL, ASMAN, MUP, dan ADMIN**.

#### Langkah Menyelesaikan Rapat (Bagi Pimpinan):
1. Pada pojok kanan atas Workspace Rapat, klik tombol **"Selesaikan Rapat"**.
2. Muncul jendela pop-up konfirmasi:
   * Masukkan **Catatan Notulen Akhir / Kesimpulan Rapat** (arahan umum dan ringkasan putusan rapat).
3. Klik tombol **"Selesaikan & Rekap"**.
4. Status rapat akan berubah menjadi **"Selesai"**, notulen terkunci rapi, dan sistem akan mengarahkan ke halaman ringkasan rapat.

---

### 5.5 Mencetak Notulen & Berita Acara Resmi (Format PDF)
1. Dari halaman detail rapat atau langsung dari Workspace, klik tombol **"Cetak Notulen"**.
2. Sistem akan membuka dokumen notulen berstandar korporat PLN Nusantara Power yang mencakup:
   * Kop surat resmi PLN UP Sengkang.
   * Berita acara pelaksanaan, daftar peserta hadir, dan pimpinan rapat.
   * Rincian pembahasan isu beserta putusan/solusi yang ditetapkan.
   * Matriks penugasan pekerjaan minggu $N$ beserta PIC dan tenggat waktu.
   * Ruang tanda tangan pimpinan rapat.
3. Tekan kombinasi tombol keyboard `Ctrl + P` (atau `Cmd + P` di Mac) untuk langsung mencetak fisik atau memilih opsi **"Save as PDF"**.

---

## 6. PANDUAN MODUL 3: MANAJEMEN ISU & ESKALASI BERJENJANG

Modul ini mengelola seluruh hambatan operasional mulai dari temuan gangguan di pembangkit hingga tindak lanjut solusi.

```
┌──────────────┐      ┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│ STAF / TL    │ ───> │  TEAM LEADER │ ───> │ ASMAN BIDANG │ ───> │  MANAJER UP  │
│ Lapor Kendala│      │Putusan Seksi │      │Putusan Bidang│      │Putusan Puncak│
└──────────────┘      └──────┬───────┘      └──────┬───────┘      └──────────────┘
                             │ Eskalasi            │ Eskalasi
                             ▼                     ▼
                      Jika Perlu Anggaran   Jika Butuh Otoritas
                      / Bantuan Bidang Lain / Anggaran Besar
```

### 6.1 Melaporkan Isu / Kendala Baru
1. Buka menu **Isu & Kendala** $\rightarrow$ klik tombol **"+ Catat Isu Baru"**.
2. Lengkapi formulir:
   * **Judul Isu:** Ringkas dan jelas (Contoh: *"Kebocoran Seal Pompa Feedwater Unit 1"*).
   * **Unit / Seksi Pelapor:** Pilih seksi terkait (misal: *Produksi Blok 1 A*).
   * **Kategori:** Pilih kategori (*Mekanikal, Elektrikal, Instrumen & Kontrol, K3, Operasi, Umum*).
   * **Prioritas:**
     * `LOW` / `MEDIUM`: Kendala rutin tanpa mengganggu produksi listrik.
     * `HIGH`: Berpotensi menurunkan kapasitas produksi (*derating*).
     * `CRITICAL`: Mengancam trip pembangkit atau membahayakan keselamatan jiwa.
   * **Tingkat Awal (Target Level):** Otomatis disesuaikan ke level `TL`.
   * **Deskripsi Detail:** Uraikan kronologi temuan, dampak potensial, dan indikasi awal.
3. Klik **"Simpan Isu"**. Isu akan memperoleh nomor registrasi unik (Contoh: `#ISS-202610-0012`).

---

### 6.2 Alur Penanganan & Eskalasi Berjenjang

1. **Tahap 1: Evaluasi di Level Team Leader (TL)**
   * TL membuka daftar isu di seksinya.
   * Jika kendala dapat diatasi dengan sumber daya internal seksi: TL mengeklik **"Tetapkan Keputusan"**, mengisi instruksi perbaikan dan menugaskan teknisi. Status berubah menjadi *Decided/Resolved*.
   * Jika membutuhkan pengadaan suku cadang, bantuan vendor, atau koordinasi antar seksi: TL mengeklik **"Eskalasikan ke Asman"**, mengisi catatan justifikasi eskalasi. Level isu naik ke level `ASMAN`.

2. **Tahap 2: Evaluasi di Level Asisten Manajer (ASMAN)**
   * Asman menerima notifikasi eskalasi isu di bidangnya.
   * Asman dapat menetapkan putusan teknis bidang, menyetujui anggaran operasional belanja rutin, atau mengoordinasikan bantuan dari seksi pemeliharaan/engineering.
   * Jika membutuhkan persetujuan anggaran investasi besar (*capex*) atau berdampak pada ketersediaan daya sistem kelistrikan Sulbagsel: Asman mengeklik **"Eskalasikan ke MUP"**. Level isu naik ke level `MUP`.

3. **Tahap 3: Putusan Strategis Manajer Unit Pelaksana (MUP)**
   * MUP meninjau isu-isu berstatus eskalasi tingkat unit.
   * MUP menetapkan kebijakan strategis, persetujuan anggaran darurat, atau disposisi penanganan khusus.
   * Putusan MUP bersifat mengikat dan otomatis menyelesaikan status eskalasi menjadi *Decided*.

---

## 7. PANDUAN MODUL 4: MANAJEMEN PEKERJAAN & TASK BACKLOG (N & N-1)

Modul Pekerjaan digunakan untuk memantau eksekusi seluruh instruksi kerja dan penugasan operasional seksi.

### 7.1 Menambah Tugas Baru
1. Buka menu **Pekerjaan** $\rightarrow$ klik **"+ Tambah Pekerjaan"**.
2. Masukkan:
   * **Nama Tugas:** Contoh: *"Kalibrasi Sensor Tekanan Boiler Blok 1"*.
   * **Pelaksana (PIC):** Pilih staf atau teknisi penanggung jawab.
   * **Tenggat Waktu (Due Date):** Tanggal target penyelesaian.
   * **Kaitan Isu (Opsional):** Pilih nomor isu terkait jika pekerjaan ini merupakan tindak lanjut dari suatu kendala.
   * **Prioritas:** *Rendah, Sedang, Tinggi, Mendesak*.
3. Klik **"Simpan Tugas"**.

### 7.2 Memperbarui Progres Pekerjaan (Slider Progress 0 - 100%)
1. Pada kartu atau baris pekerjaan, PIC atau atasan dapat langsung mengubah persentase progres.
2. Geser bilah progres (*progress slider*) atau klik tombol persentase:
   * `0% - 25%`: Pekerjaan baru dialokasikan (*Assigned*).
   * `50% - 75%`: Pekerjaan dalam proses eksekusi (*In Progress*).
   * `100%`: Pekerjaan tuntas diselesaikan (*Done / Completed*).
3. Jika progres mencapai 100%, status tugas otomatis berubah menjadi **Selesai**. Jika tanggal hari ini melampaui tenggat waktu dan progres belum 100%, sistem secara otomatis menandai tugas sebagai **Terlambat (Overdue)**.

---

## 8. PANDUAN MODUL 5: PUSAT INFORMASI & PENGUMUMAN MANAJEMEN

Modul ini mengimplementasikan prinsip *Cascading Information* (FR-09) agar arahan manajemen tersampaikan secara transparan ke seluruh tingkatan organisasi.

### 8.1 Membaca Pengumuman
* Seluruh pengguna dapat melihat pengumuman, surat edaran, memo pimpinan, serta pedoman keselamatan (K3) yang ditargetkan untuk level mereka di menu **Pusat Informasi**.

### 8.2 Menerbitkan Informasi Baru (Khusus Pimpinan)
1. Klik **"+ Tambah Pengumuman"**.
2. Tentukan **Target Level**:
   * `ALL`: Dapat dibaca oleh seluruh pegawai UP Sengkang.
   * `STAFF`: Ditujukan khusus staf pelaksana.
   * `TL`: Ditujukan khusus para Team Leader.
   * `ASMAN`: Ditujukan khusus para Asisten Manajer.
3. Masukkan judul dan isi pengumuman $\rightarrow$ klik **"Terbitkan Informasi"**.

---

## 9. PANDUAN MODUL 6: PELAPORAN & EKSPOR DATA

Modul Laporan digunakan untuk monitoring berkala dan bahan evaluasi pimpinan:
1. Buka menu **Laporan** di sidebar.
2. Anda dapat melihat:
   * **Statistik Ketuntasan Pekerjaan:** Rasio pekerjaan selesai tepat waktu vs terlambat.
   * **Matriks Penyelesaian Isu:** Waktu rata-rata penanganan isu per seksi.
   * **Rekapitulasi Sesi Rapat:** Jumlah notulen dan keputusan yang dihasilkan per bulan.
3. **Ekspor Data ke Format Excel / CSV:**
   * Klik tombol **"Ekspor CSV"** di kanan atas.
   * File data mentah akan otomatis terunduh ke komputer Anda dan siap diolah menggunakan Microsoft Excel untuk lampiran laporan manajemen bulanan.

---

## 10. PANDUAN MODUL 7: ADMINISTRASI MASTER DATA (MUP & ADMIN)

Menu ini hanya dapat diakses oleh akun dengan level **MUP** atau **ADMIN** untuk menjaga integritas data organisasi:
1. Buka menu **Master Data** di sidebar.
2. **Kelola Pengguna (Users):**
   * Menambah akun pegawai baru (Nama, Email PLN, NIP/Employee ID, Unit Kerja, dan Jabatan).
   * Menetapkan peran sistem (*MUP, ASMAN, TL, STAFF*).
   * Mengaktifkan atau menonaktifkan akun pegawai yang mutasi/pensiun.
3. **Kelola Unit Organisasi:**
   * Memetakan struktur seksi di bawah bidang, serta bidang di bawah UP Sengkang.

---

## 11. TANYA JAWAB & PEMECAHAN MASALAH (FAQ & TROUBLESHOOTING)

### Q1: Mengapa saat saya (Staff) berada di Workspace Rapat, tombol "Selesaikan Rapat" tidak ada?
> **Jawaban:** Hal ini merupakan fitur pengamanan sistem, bukan error. Sesuai tata tertib operasional PLN UP Sengkang, penutupan dan pengesahan notulen rapat hanya boleh dilakukan oleh Pimpinan Rapat (TL, Asman, atau MUP). Akun Staf hanya disediakan tombol **"Kembali"**.

### Q2: Saya baru saja membuat agenda rapat, tetapi kenapa tidak muncul di daftar paling atas?
> **Jawaban:** 
> 1. Pastikan Anda berada di tab **"Jadwal Aktif / Mendatang"** (bukan tab *Riwayat Selesai*).
> 2. Sistem memprioritaskan sesi rapat yang sedang berstatus **LIVE (Berlangsung)** di urutan paling atas. Jika agenda yang Anda buat berstatus *Akan Datang*, posisinya berada di bawah rapat yang sedang aktif live hari ini.
> 3. Periksa filter bidang di bagian atas tabel, pastikan terpilih **"Semua Bidang / Divisi"**.

### Q3: Apakah jadwal rapat yang menggunakan preset otomatis bisa diubah jika tanggalnya bertepatan dengan hari libur nasional?
> **Jawaban:** Sangat bisa! Preset 1-Klik disediakan untuk mempermudah pengisian otomatis tanggal siklus baku. Namun seluruh kolom input (*Tanggal Rapat, Jam Mulai, Jam Selesai, dan Ruangan*) **tetap bersifat fleksibel** dan dapat diedit secara bebas sebelum Anda mengeklik tombol Simpan.

### Q4: Mengapa akun Staff saya tidak bisa melihat Rapat Asman + TL atau Rapat RTM?
> **Jawaban:** Sistem menerapkan *Role Scoping* ketat. Rapat level koordinasi bidang (Asman+TL) dan rapat manajemen puncak (RTM) bersifat rahasia dan dibatasi hanya untuk pimpinan terkait. Staf hanya dapat mengakses Rapat TL + Staf di seksinya sendiri atau rapat di mana Staf tersebut terdaftar sebagai peserta undangan.

### Q5: Bagaimana jika ada kendala mendesak (Kritikal) di luar jam rapat rutin?
> **Jawaban:** Anda tidak perlu menunggu jadwal rapat hari Senin. Masuk ke menu **Isu & Kendala**, klik **"+ Catat Isu Baru"**, dan pilih prioritas **`CRITICAL`**. Sistem akan segera memunculkan peringatan merah di Dashboard dan notifikasi lonceng atasan terkait untuk penanganan segera.

---

## 12. KONTAK DUKUNGAN TEKNIS
Jika Anda menemukan kendala teknis lebih lanjut atau membutuhkan penyesuaian hak akses:
* **Unit Pelaksana:** PT PLN Nusantara Power - UP Sengkang
* **Tim Pengembang & Administrator TI:** IT Support & Operation Team UP Sengkang
* **Email Dukungan:** `admin.sengkang@plnnp.co.id`

---
*Dokumen ini disusun sebagai panduan operasional resmi penerapan sistem digitalisasi rapat dan manajemen isu berjenjang PT PLN Nusantara Power UP Sengkang (Versi 2.1).*
