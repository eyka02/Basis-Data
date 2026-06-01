# Backend - Sistem Manajemen Klinik Medis Hewan

Express.js backend untuk Sistem Manajemen Klinik Medis Hewan dengan fitur master data, riwayat kunjungan, manajemen stok otomatis, dan laporan.

## Fitur Utama

- **Kelola Data Master**: CRUD untuk pemilik dan hewan peliharaan
- **Fitur Riwayat Kunjungan**: Mencatat setiap kunjungan berobat
- **Fitur Stok Otomatis**: Pengurangan stok otomatis saat obat/vaksin digunakan
- **Fitur Laporan**: Analytics dan statistik penggunaan layanan medis

## Requirements

- Node.js v14+
- MySQL 5.7+
- npm atau yarn

## Instalasi

1. Clone repository dan masuk ke folder backend:
```bash
cd backend
```

2. Install dependencies:
```bash
npm install
```

3. Setup environment variables:
```bash
cp .env.example .env
```

Kemudian edit file `.env` dengan konfigurasi database Anda:
```
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=your_password
DB_NAME=klinikmedishewan
DB_PORT=3306
PORT=5000
NODE_ENV=development
```

4. Jalankan database setup (import `klinikmedishewan.sql` ke MySQL):
```bash
mysql -u root -p klinikmedishewan < ../klinikmedishewan.sql
```

## Menjalankan Server

Development mode (dengan auto-reload):
```bash
npm run dev
```

Production mode:
```bash
npm start
```

Server akan berjalan di `http://localhost:5000`

## API Endpoints

### Pemilik (Owner)
- `GET /api/pemilik` - Dapatkan semua pemilik
- `GET /api/pemilik/:id` - Dapatkan pemilik berdasarkan ID
- `POST /api/pemilik` - Buat pemilik baru
- `PUT /api/pemilik/:id` - Update pemilik
- `DELETE /api/pemilik/:id` - Hapus pemilik

### Hewan (Animal)
- `GET /api/hewan` - Dapatkan semua hewan
- `GET /api/hewan/:id` - Dapatkan hewan berdasarkan ID
- `GET /api/hewan/pemilik/:id_pemilik` - Dapatkan hewan milik pemilik tertentu
- `POST /api/hewan` - Buat hewan baru
- `PUT /api/hewan/:id` - Update data hewan
- `DELETE /api/hewan/:id` - Hapus hewan

### Obat/Vaksin (Medicine/Vaccine)
- `GET /api/obat-vaksin` - Dapatkan semua obat/vaksin
- `GET /api/obat-vaksin/:id` - Dapatkan obat/vaksin berdasarkan ID
- `GET /api/obat-vaksin/type/:jenis` - Dapatkan obat atau vaksin berdasarkan tipe
- `POST /api/obat-vaksin` - Tambah obat/vaksin baru
- `PUT /api/obat-vaksin/:id` - Update obat/vaksin
- `DELETE /api/obat-vaksin/:id` - Hapus obat/vaksin

### Kunjungan (Visit)
- `GET /api/kunjungan` - Dapatkan semua kunjungan
- `GET /api/kunjungan/:id` - Dapatkan kunjungan berdasarkan ID
- `GET /api/kunjungan/hewan/:id_hewan` - Dapatkan riwayat kunjungan hewan
- `POST /api/kunjungan` - Buat kunjungan baru
- `PUT /api/kunjungan/:id` - Update kunjungan
- `DELETE /api/kunjungan/:id` - Hapus kunjungan

### Detail Obat (Medicine Details) - dengan Auto Stock Decrement
- `GET /api/detail-obat` - Dapatkan semua detail obat
- `GET /api/detail-obat/kunjungan/:id_kunjungan` - Dapatkan obat dari kunjungan tertentu
- `POST /api/detail-obat` - Tambah obat untuk kunjungan (auto decrement stok)
- `PUT /api/detail-obat/:id` - Update detail obat (adjust stok)
- `DELETE /api/detail-obat/:id` - Hapus detail obat (restore stok)

### Laporan (Reports)
- `GET /api/laporan/most-used-items` - Obat/vaksin paling sering digunakan
- `GET /api/laporan/item-statistics/:id_obat` - Statistik penggunaan item tertentu
- `GET /api/laporan/medical-services` - Layanan medis paling sering digunakan
- `GET /api/laporan/visit-statistics` - Statistik kunjungan (support date range)
- `GET /api/laporan/animal-statistics` - Statistik berdasarkan jenis hewan
- `GET /api/laporan/clinic-summary` - Ringkasan keseluruhan klinik

## Contoh Request

### Membuat Pemilik Baru
```bash
curl -X POST http://localhost:5000/api/pemilik \
  -H "Content-Type: application/json" \
  -d '{
    "nama_pemilik": "Damis Karyo",
    "telepon": "089912457890",
    "alamat": "Jln. Sukamulya, No. 9 Kab. Garut"
  }'
```

### Membuat Hewan
```bash
curl -X POST http://localhost:5000/api/hewan \
  -H "Content-Type: application/json" \
  -d '{
    "id_pemilik": 152,
    "nama_hewan": "Fluffy",
    "jenis_hewan": "Kucing",
    "ras": "Persia",
    "umur_bulan": 24
  }'
```

### Membuat Obat/Vaksin
```bash
curl -X POST http://localhost:5000/api/obat-vaksin \
  -H "Content-Type: application/json" \
  -d '{
    "nama_item": "Paracetamol",
    "jenis": "Obat",
    "stok": 100,
    "harga": 5000
  }'
```

### Membuat Kunjungan
```bash
curl -X POST http://localhost:5000/api/kunjungan \
  -H "Content-Type: application/json" \
  -d '{
    "id_hewan": 1,
    "tanggal_kunjungan": "2026-06-01",
    "keluhan": "Diare",
    "diagnosa": "Gastroenteritis",
    "tindakan_medis": "Injeksi cairan"
  }'
```

### Membuat Detail Obat (Auto Stock Decrement)
```bash
curl -X POST http://localhost:5000/api/detail-obat \
  -H "Content-Type: application/json" \
  -d '{
    "id_kunjungan": 1,
    "id_obat": 1,
    "jumlah": 5
  }'
```

### Mendapatkan Laporan Most Used Items
```bash
curl http://localhost:5000/api/laporan/most-used-items
```

## Struktur Folder

```
backend/
├── config/
│   └── database.js          # Konfigurasi koneksi database
├── routes/
│   ├── pemilik.js           # Routes untuk pemilik
│   ├── hewan.js             # Routes untuk hewan
│   ├── kunjungan.js         # Routes untuk kunjungan
│   ├── obatVaksin.js        # Routes untuk obat/vaksin
│   ├── detailObat.js        # Routes untuk detail obat (with auto stock)
│   └── laporan.js           # Routes untuk laporan
├── server.js                # Entry point aplikasi
├── .env.example             # Contoh konfigurasi environment
├── .gitignore               # Git ignore file
├── package.json             # Dependencies
└── README.md                # Dokumentasi
```

## Fitur Keamanan

- Database transactions untuk operasi critical (detail obat)
- Validasi input data
- Error handling yang proper
- Connection pooling untuk performa optimal

## Development

Untuk development, gunakan:
```bash
npm run dev
```

Ini akan menggunakan `nodemon` untuk auto-reload ketika ada perubahan file.

## License

ISC
