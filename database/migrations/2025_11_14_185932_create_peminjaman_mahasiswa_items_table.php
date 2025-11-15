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
        Schema::create('peminjaman_mahasiswa_items', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke peminjaman_mahasiswa (header)
            $table->foreignId('peminjaman_mahasiswa_id')
                  ->constrained('peminjaman_mahasiswa')
                  ->onDelete('cascade');
            
            // Data Barang yang Dipinjam
            $table->string('barang_id');
            $table->string('nama_barang');
            $table->enum('jenis_barang', ['habis_pakai', 'tidak_habis_pakai']);
            $table->integer('jumlah');
            $table->string('satuan')->nullable();
            
            // Status per item
            $table->enum('status', ['pending', 'approved', 'rejected', 'dikembalikan'])->default('pending');
            
            // Jam kembali per item (untuk barang tidak habis pakai)
            $table->string('jam_kembali')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_mahasiswa_items');
    }
};
