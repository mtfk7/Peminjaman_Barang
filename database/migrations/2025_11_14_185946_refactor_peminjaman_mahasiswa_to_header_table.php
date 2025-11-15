<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrate data existing dari peminjaman_mahasiswa ke items
        // (Setiap record existing menjadi 1 item)
        // Hanya migrate jika ada data dan tabel items sudah ada
        if (Schema::hasTable('peminjaman_mahasiswa_items') && DB::table('peminjaman_mahasiswa')->count() > 0) {
            // Cek apakah kolom jam_kembali ada di peminjaman_mahasiswa
            $hasJamKembali = Schema::hasColumn('peminjaman_mahasiswa', 'jam_kembali');
            
            if ($hasJamKembali) {
                DB::statement("
                    INSERT INTO peminjaman_mahasiswa_items (
                        peminjaman_mahasiswa_id,
                        barang_id,
                        nama_barang,
                        jenis_barang,
                        jumlah,
                        satuan,
                        status,
                        jam_kembali,
                        created_at,
                        updated_at
                    )
                    SELECT 
                        id as peminjaman_mahasiswa_id,
                        barang_id,
                        nama_barang,
                        jenis_barang,
                        jumlah,
                        satuan,
                        status,
                        jam_kembali,
                        created_at,
                        updated_at
                    FROM peminjaman_mahasiswa
                    WHERE NOT EXISTS (
                        SELECT 1 FROM peminjaman_mahasiswa_items 
                        WHERE peminjaman_mahasiswa_items.peminjaman_mahasiswa_id = peminjaman_mahasiswa.id
                    )
                ");
            } else {
                // Jika kolom jam_kembali belum ada, insert tanpa jam_kembali
                DB::statement("
                    INSERT INTO peminjaman_mahasiswa_items (
                        peminjaman_mahasiswa_id,
                        barang_id,
                        nama_barang,
                        jenis_barang,
                        jumlah,
                        satuan,
                        status,
                        created_at,
                        updated_at
                    )
                    SELECT 
                        id as peminjaman_mahasiswa_id,
                        barang_id,
                        nama_barang,
                        jenis_barang,
                        jumlah,
                        satuan,
                        status,
                        created_at,
                        updated_at
                    FROM peminjaman_mahasiswa
                    WHERE NOT EXISTS (
                        SELECT 1 FROM peminjaman_mahasiswa_items 
                        WHERE peminjaman_mahasiswa_items.peminjaman_mahasiswa_id = peminjaman_mahasiswa.id
                    )
                ");
            }
        }

        // 2. Note: Kolom barang tetap dipertahankan di header untuk backward compatibility
        // Status keseluruhan akan di-calculate dari items via accessor di model
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus semua items
        DB::table('peminjaman_mahasiswa_items')->truncate();
        
        // Note: Kolom barang sudah tidak ada, jadi tidak bisa di-restore
        // Jika perlu rollback, harus restore dari backup database
    }
};
