# SIAM - Sistem Informasi Arsip Digital

Panduan berikut berisi langkah-langkah untuk melakukan inisialisasi basis data proyek **SIAMA** di lingkungan lokal masing-masing.

---

## 🛠️ Prasyarat

1. Pastikan service MySQL/MariaDB sudah berjalan (Laragon / XAMPP).
2. Buat database baru di MySQL, contoh: `siam_db`.
3. Pastikan konfigurasi berkas `.env` sudah sesuai dengan database lokal.

### Konfigurasi `.env`
```env
database.default.hostname = localhost
database.default.database = siam_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port     = 3306

🚀 Langkah Setup Database
Buka Terminal/Command Prompt di direktori utama proyek, lalu jalankan perintah berikut secara berurutan:

1. Jalankan Migration
Membuat 12 tabel utama beserta relasi foreign key:
"php spark migrate"

2. Jalankan Seeder
Mengisi data awal master hak akses (roles) ke dalam tabel roles:
"php spark db:seed RolesSeeder"


🔄 Perintah Pendukung
Cek Status Migration:
"php spark migrate:status"

Reset Database (Hapus semua tabel & buat ulang dari awal):
"php spark migrate:refresh"
"php spark db:seed RolesSeeder"