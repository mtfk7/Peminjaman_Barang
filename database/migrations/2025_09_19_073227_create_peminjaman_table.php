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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();

            // Relasi barang
            $table->string('barang_id');

            // Jenis barang: habis pakai / tidak habis pakai
            $table->enum('jenis_barang', ['habis_pakai', 'tidak_habis_pakai']);

            // Nama peminjam (misal mahasiswa/staf)
            $table->string('nama_peminjam');
            

            // Jumlah barang yang dipinjam
            $table->integer('jumlah');

            // Tanggal pinjam dan tanggal kembali
            $table->date('tanggal_pinjam')->default(now());
            

            // Status peminjaman
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
