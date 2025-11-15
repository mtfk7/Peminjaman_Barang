<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman_mahasiswa', function (Blueprint $table) {
            // Tambah kolom baru
            $table->string('nama_peminjam')->nullable()->after('nama_mahasiswa');
            $table->time('jam_pinjam')->nullable()->after('tanggal_pinjam');
            $table->time('jam_kembali')->nullable()->after('jam_pinjam');
            $table->string('mata_kuliah')->nullable()->after('keperluan');
            $table->string('kelas')->nullable()->after('mata_kuliah');
            
            // Ubah tanggal_kembali menjadi nullable (tidak dihapus untuk backward compatibility)
            $table->date('tanggal_kembali')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['nama_peminjam', 'jam_pinjam', 'jam_kembali', 'mata_kuliah', 'kelas']);
            $table->date('tanggal_kembali')->nullable(false)->change();
        });
    }
};
