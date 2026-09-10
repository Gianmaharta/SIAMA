# SIAM - Sistem Informasi Arsip Digital

Panduan berikut berisi langkah-langkah untuk melakukan inisialisasi basis data proyek **SIAMA** di lingkungan lokal masing-masing, terutama untuk tim *Frontend*.

---

## 🛠️ Prasyarat

1. Pastikan service MySQL/MariaDB sudah berjalan (Laragon / XAMPP).
2. Buat database baru di MySQL bernama: `db_siama`.
3. Pastikan konfigurasi berkas `.env` sudah sesuai dengan database lokal.

### Konfigurasi `.env`
```env
database.default.hostname = localhost
database.default.database = db_siama
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port     = 3306
```

---

## 🚀 Langkah Setup Database (PENTING)
Buka Terminal/Command Prompt di direktori utama proyek, lalu jalankan perintah berikut secara berurutan:

### 1. Jalankan Migration
Membuat 12 tabel utama beserta relasi foreign key:
```bash
php spark migrate
```

### 2. Jalankan Seeder
Mengisi data awal master hak akses (roles), OPD, Bidang, serta **5 Akun Testing**:
```bash
php spark db:seed SiamaSeeder
```

---

## 🧪 Panduan Testing untuk Frontend

### A. Testing API Documentation (Swagger UI)
Pastikan server lokal berjalan (via Laragon atau `php spark serve`).
Akses URL berikut di browser:
👉 `http://siama.test/swagger` (atau `http://localhost:8080/swagger` jika pakai spark).

### B. Testing Auth (Login)
Untuk menguji aliran *login*, akses:
👉 `http://siama.test/login` (atau `http://localhost:8080/login`).

Gunakan salah satu **Email** dan **Password** berikut (semua password adalah `password123`):

| Role | Email |
| :--- | :--- |
| **Admin Pemkab** | `admin.pemkab@siama.test` |
| **Pimpinan** | `pimpinan@siama.test` |
| **Admin OPD** | `admin.opd@siama.test` |
| **Kepala Bidang** | `kabid@siama.test` |
| **Arsiparis** | `arsiparis@siama.test` |

---

## 🔄 Perintah Pendukung

Cek Status Migration:
```bash
php spark migrate:status
```

Reset Database (Hapus semua tabel & buat ulang dari awal):
```bash
php spark migrate:refresh
php spark db:seed SiamaSeeder
```