# MANTAP PDH - Panduan Setup & Instalasi

Proyek ini adalah sistem manajemen event dan penilaian usulan inovasi (INOTEK/INODA). Panduan ini dibuat untuk membantu Anda menginstal dan menjalankan semua fitur proyek pada **device baru**.

## Persyaratan Sistem (Prerequisites)
Pastikan sistem Anda sudah terinstal perangkat lunak berikut:
- **PHP** (minimal versi 8.3)
- **Composer** (untuk dependensi PHP)
- **Node.js & NPM** (untuk dependensi Frontend/Tailwind/Playwright)
- **Git** (opsional, untuk *version control*)
- Database menggunakan **SQLite** (sudah *built-in* dengan PHP)

---

## 🛠️ Cara Instalasi & Setup di Device Baru

Ikuti langkah-langkah di bawah ini secara berurutan pada terminal/Command Prompt Anda:

**1. Clone Repository (Jika dari Git)**
```bash
git clone https://github.com/mutiatus66-coder/mantappdh.git
cd mantappdh
```

**2. Instalasi Dependensi PHP (Composer)**
```bash
composer install
```

**3. Konfigurasi Environment**
Duplikat file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*(Di OS Windows, gunakan perintah: `copy .env.example .env`)*

**4. Generate Application Key**
```bash
php artisan key:generate
```

**5. Setup Database & Seeding Data**
Proyek ini menggunakan SQLite secara bawaan. Jalankan migrasi dan isi data dummy:
```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```
*(Catatan: Perintah `--seed` sangat penting agar akun Admin default dan dummy usulan/event ter-generate otomatis).*

**6. Instalasi Dependensi Frontend (NPM) & Build Assets**
```bash
npm install
npm run build
```
*(Gunakan `npm run dev` jika Anda ingin mengaktifkan hot-reload saat memodifikasi tampilan CSS/JS).*

**7. Menautkan Storage (Opsional, untuk fitur upload)**
```bash
php artisan storage:link
```

**8. Jalankan Local Development Server**
```bash
php artisan serve
```
Aplikasi sekarang dapat diakses melalui browser pada: **http://localhost:8000** atau **http://mantappdh.test** (tergantung konfigurasi environment lokal Anda).

---

## 🔐 Kredensial Akses Default

Setelah Anda berhasil menjalankan `php artisan migrate --seed`, akun berikut bisa langsung digunakan untuk login:

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `admin@admin.com` | `password` |

*(Note: Data penilai, peserta, atau admin tambahan juga tersedia. Silakan cek di menu User via Super Admin).*

---

## 🧪 Menjalankan Automation E2E Testing

Proyek ini memiliki *automation test* menggunakan **Laravel Dusk** dan **Playwright**. Pastikan server aplikasi berjalan (`php artisan serve`) sebelum Anda mengeksekusi test.

### Menggunakan Playwright (Direkomendasikan)
Playwright memungkinkan testing berjalan lebih cepat di berbagai browser modern.
1. Pastikan Anda telah menginstal browser Playwright:
   ```bash
   npx playwright install
   ```
2. Jalankan _suite_ spesifik:
   ```bash
   # Test Admin Full CRUD
   npx playwright test tests-playwright/playwright/tests/admin
   
   # Test User Penilai
   node playwright/penilai.js
   
   # Test User Peserta
   node playwright/peserta.js
   ```

### Menggunakan Laravel Dusk (Native)
Pastikan Chrome terinstal di perangkat Anda, lalu jalankan:
```bash
# Semua test untuk Admin Master
php artisan dusk tests/Browser/Admin/Test1_MasterTest.php

# Test Penilai
php artisan dusk tests/Browser/PenilaiWorkflowTest.php

# Test Peserta
php artisan dusk tests/Browser/PesertaWorkFlowTest.php
```

---

## 🔄 Rollback Database Cepat
Jika Anda sedang men-testing atau mendemonstrasikan sistem dan ingin melakukan *reset* data secara berkala, Anda bisa menggunakan perintah artisan bawaan:
- Rollback 10 menit yang lalu: `php artisan db:rollback 10m`
- Rollback 30 menit yang lalu: `php artisan db:rollback 30m`
- Rollback 24 jam yang lalu: `php artisan db:rollback 24h`
- Rollback 3 hari yang lalu: `php artisan db:rollback 3d`
