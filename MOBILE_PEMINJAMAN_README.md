# 📱 Sistem Peminjaman Barang Mahasiswa dengan QR Code

Sistem web mobile terintegrasi untuk mahasiswa mengajukan peminjaman barang dengan approval via QR Code.

## 🎯 Fitur Utama

### Untuk Mahasiswa:
- ✅ Melihat daftar barang tersedia (habis pakai & tidak habis pakai)
- ✅ Mengajukan peminjaman barang secara online
- ✅ Mendapatkan QR Code setelah pengajuan
- ✅ Cek status peminjaman berdasarkan NIM
- ✅ Download/Print QR Code untuk approval

### Untuk Admin:
- ✅ Scan QR Code menggunakan kamera
- ✅ Approve atau Reject peminjaman
- ✅ Mengelola data peminjaman mahasiswa di panel admin
- ✅ Melihat histori peminjaman mahasiswa
- ✅ Filter berdasarkan status dan jenis barang

## 🚀 URL & Routes

### Mahasiswa (Web Mobile)
- **Beranda:** `http://127.0.0.1:8000/mahasiswa`
- **Form Peminjaman:** `http://127.0.0.1:8000/mahasiswa/pinjam`
- **QR Code:** `http://127.0.0.1:8000/mahasiswa/peminjaman/{id}`
- **Cek Status:** `http://127.0.0.1:8000/mahasiswa/status`

### Admin
- **QR Scanner:** `http://127.0.0.1:8000/admin/qr-scanner`
- **Detail Peminjaman:** `http://127.0.0.1:8000/admin/qr-scanner/detail/{qrCode}`
- **Panel Admin:** `http://127.0.0.1:8000/admin/peminjaman-mahasiswas`

## 📊 Database Schema

### Tabel: `peminjaman_mahasiswa`

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| nama_mahasiswa | string | Nama lengkap mahasiswa |
| nim | string | NIM mahasiswa (unique) |
| email | string | Email mahasiswa |
| no_telp | string | Nomor telepon |
| barang_id | string | ID barang yang dipinjam |
| nama_barang | string | Nama barang |
| jenis_barang | enum | habis_pakai / tidak_habis_pakai |
| jumlah | integer | Jumlah barang |
| satuan | string | Satuan barang |
| tanggal_pinjam | date | Tanggal mulai pinjam |
| tanggal_kembali | date | Tanggal estimasi kembali |
| keperluan | text | Keperluan peminjaman |
| qr_code | string | Token unique untuk QR (UUID) |
| status | enum | pending / approved / rejected / dikembalikan |
| approved_by | bigint | User ID yang approve |
| approved_at | timestamp | Waktu approval |
| rejection_reason | text | Alasan jika ditolak |

## 🎨 Teknologi yang Digunakan

- **Backend:** Laravel 11
- **Frontend:** Tailwind CSS
- **Admin Panel:** Filament 3
- **QR Code Generator:** SimpleSoftwareIO/simple-qrcode
- **QR Scanner:** HTML5-QRCode
- **Database:** MySQL

## 📝 Cara Penggunaan

### 1. Mahasiswa Mengajukan Peminjaman

1. Buka `http://127.0.0.1:8000/mahasiswa`
2. Pilih barang yang ingin dipinjam
3. Klik "Pinjam Barang"
4. Isi form peminjaman:
   - Data mahasiswa (Nama, NIM, Email, No. Telp)
   - Jumlah barang yang dipinjam
   - Tanggal pinjam dan kembali
   - Keperluan (opsional)
5. Klik "Ajukan Peminjaman"
6. QR Code akan otomatis ter-generate
7. Download/Screenshot QR Code
8. Tunjukkan QR Code ke Admin

### 2. Admin Approve Peminjaman

1. Login ke admin panel
2. Buka `http://127.0.0.1:8000/admin/qr-scanner`
3. Klik "Mulai Scan"
4. Arahkan kamera ke QR Code mahasiswa
5. Sistem akan otomatis redirect ke detail peminjaman
6. Verifikasi data peminjaman
7. Klik "SETUJUI PEMINJAMAN" untuk approve
   - Atau "TOLAK PEMINJAMAN" jika ditolak (isi alasan)
8. Stok barang akan otomatis berkurang jika disetujui

### 3. Mahasiswa Cek Status

1. Buka `http://127.0.0.1:8000/mahasiswa/status`
2. Masukkan NIM
3. Klik "Cari"
4. Lihat semua peminjaman dan statusnya

## 🔐 Hak Akses

### Admin yang Bisa Mengakses:

| Role | QR Scanner | Peminjaman Mahasiswa |
|------|-----------|---------------------|
| Kepala | ✅ | ✅ |
| Admin Peminjaman Barang | ✅ | ✅ |
| Admin Persediaan Barang | ❌ | ❌ |

## 🎯 Flow Sistem

```
1. Mahasiswa mengajukan peminjaman
   ↓
2. Sistem generate QR Code (UUID)
   ↓
3. Mahasiswa mendapat QR Code
   ↓
4. Mahasiswa datang ke admin dengan QR Code
   ↓
5. Admin scan QR Code
   ↓
6. Admin verifikasi data
   ↓
7a. Jika APPROVED:
    - Status berubah jadi "approved"
    - Stok barang dikurangi
    - Mahasiswa dapat mengambil barang
    
7b. Jika REJECTED:
    - Status berubah jadi "rejected"
    - Alasan penolakan dicatat
    - Mahasiswa dapat melihat alasan
```

## 📱 Mobile Responsive

Semua halaman mahasiswa sudah dioptimalkan untuk mobile:
- Responsive design dengan Tailwind CSS
- Touch-friendly UI
- Mobile-first approach
- Fast loading

## 🔧 Maintenance

### Menambah Barang Baru
1. Login sebagai Admin Persediaan atau Kepala
2. Masuk ke menu "Barang Habis Pakai" atau "Barang Tidak Habis Pakai"
3. Klik "Create"
4. Isi data barang
5. Barang otomatis muncul di web mobile mahasiswa

### Melihat Laporan Peminjaman
1. Login ke admin panel
2. Buka menu "Peminjaman Mahasiswa"
3. Filter berdasarkan status atau jenis barang
4. Export data jika diperlukan

## 🐛 Troubleshooting

### QR Scanner Tidak Berfungsi
- Pastikan browser memiliki izin akses kamera
- Gunakan HTTPS (atau localhost)
- Coba browser lain (Chrome/Firefox recommended)

### QR Code Tidak Valid
- Pastikan QR Code belum expired
- Cek koneksi database
- Verifikasi QR Code format

### Stok Tidak Berkurang
- Cek logic di `QRScannerController@approve`
- Verifikasi `barang_id` sesuai dengan database
- Cek log error Laravel

## 📞 Support

Untuk pertanyaan atau masalah, hubungi admin sistem.

---

**Dibuat dengan ❤️ menggunakan Laravel & Filament**



