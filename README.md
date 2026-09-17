# SIAMA - Sistem Alih Media Arsip

Panduan berikut berisi langkah-langkah untuk melakukan inisialisasi basis data proyek **SIAMA** di lingkungan lokal masing-masing, terutama untuk tim *Frontend*. 

Telah dilakukan pembaharuan sistem (Refactoring) yang meliputi:
- **UUID Primary Key**: Seluruh ID kini menggunakan format string UUID v4 (36 karakter) alih-alih `INT AUTO_INCREMENT`.
- **Kolom Audit Terstandar**: Terdapat 4 kolom audit di setiap tabel transaksi: `created_at`, `updated_at`, `created_by`, dan `updated_by`.
- **Keamanan Akun (Force Password Change)**: Akun bawaan (*seeder*) sekarang menggunakan sandi *default* `Admin123!` dan pengguna diwajibkan untuk mengganti kata sandi setelah *login* perdana.
- **Pemisahan Hak Akses Penilaian**: Modul Penilaian Arsip (Skor Prioritas & Autentikasi) dipisahkan secara eksklusif. Hanya **Admin Pemkab (Role 1)** yang memiliki akses (Write/Update), sedangkan Arsiparis hanya bersifat (Read-Only).

---

## 🛠️ Prasyarat

1. Pastikan service MySQL/MariaDB sudah berjalan (Laragon / XAMPP).
2. Buat database baru di MySQL bernama: `db_siama`.
3. Pastikan konfigurasi berkas `.env` sudah sesuai dengan database lokal.

### Konfigurasi `.env`
```env
database.default.hostname = localhost
database.default.database = db_siama
database.default.username = root (sesuaikan dengan username anda)
database.default.password = (sesuaikan dengan password anda)
database.default.DBDriver = MySQLi
database.default.port     = 3306
```

---

## 🚀 Langkah Setup Database (PENTING)
Buka Terminal/Command Prompt di direktori utama proyek, lalu jalankan perintah berikut secara berurutan:

### 1. Jalankan Migration
Membuat seluruh 13 tabel utama beserta *Fulltext index* dan relasi *foreign key*:
```bash
php spark migrate
```

### 2. Jalankan Seeder
Mengisi data awal untuk master hak akses (Roles), OPD, Bidang, **5 Akun Testing**, **Kode Klasifikasi**, **Data Dummy SPT Terkait**, serta **Data Dummy Arsip** untuk keperluan pengetesan sistem Penilaian Arsip.
```bash
php spark db:seed SiamaSeeder
```

> **CATATAN**: Mengingat sistem kita menggunakan UUID yang selalu digenerate secara acak pada setiap *run*, sangat disarankan untuk melakukan *reset* database (*drop* & *create*) terlebih dahulu sebelum menjalankan Seeder ini jika database Anda sudah pernah diisi. Ini demi mencegah konflik *Foreign Key* atau relasi arsip yang menggantung.

---

## 🧪 Panduan Testing untuk Frontend

### A. Testing API Documentation (Swagger UI)
Pastikan server lokal berjalan (via Laragon atau `php spark serve`).
Akses URL berikut di browser:
👉 `http://siama.test/swagger` (atau `http://localhost:8080/swagger` jika pakai spark).

### B. Testing Auth (Login)
Untuk menguji aliran *login*, akses:
👉 `http://siama.test/login` (atau `http://localhost:8080/login`).

Gunakan salah satu **Email** berikut dengan **Password Default**: `Admin123!`

| Role | Email |
| :--- | :--- |
| **Admin Pemkab** | `admin.pemkab@siama.test` |
| **Pimpinan** | `pimpinan@siama.test` |
| **Admin OPD** | `admin.opd@siama.test` |
| **Kepala Bidang** | `kabid@siama.test` |
| **Arsiparis** | `arsiparis@siama.test` |

> **PERHATIAN:** Setelah login, Anda akan langsung diredirect ke halaman `/change-password` untuk **mengganti password default**. Sistem tidak akan mengizinkan Anda mengakses _Dashboard_ maupun modul lainnya sebelum password berhasil diubah (minimal 8 karakter).

---

## 🔄 Perintah Pendukung

Cek Status Migration:
```bash
php spark migrate:status
```

Reset Database (Hapus semua tabel & buat ulang dari awal):
```bash
# Untuk menghindari kendala Foreign Key, disarankan me-reset dengan cara manual:
# 1. DROP DATABASE db_siama;
# 2. CREATE DATABASE db_siama;
# Lalu jalankan ulang perintah:
php spark migrate
php spark db:seed SiamaSeeder
```