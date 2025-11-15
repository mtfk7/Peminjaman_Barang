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
            // Buat kolom barang menjadi nullable karena sekarang sudah dipindah ke items
            $table->string('barang_id')->nullable()->change();
            $table->string('nama_barang')->nullable()->change();
            $table->enum('jenis_barang', ['habis_pakai', 'tidak_habis_pakai'])->nullable()->change();
            $table->integer('jumlah')->nullable()->change();
            $table->string('satuan')->nullable()->change();
            
            // Status juga bisa nullable karena sekarang dihitung dari items
            $table->enum('status', ['pending', 'approved', 'rejected', 'dikembalikan'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_mahasiswa', function (Blueprint $table) {
            // Kembalikan ke required (jika perlu rollback)
            $table->string('barang_id')->nullable(false)->change();
            $table->string('nama_barang')->nullable(false)->change();
            $table->enum('jenis_barang', ['habis_pakai', 'tidak_habis_pakai'])->nullable(false)->change();
            $table->integer('jumlah')->nullable(false)->change();
            $table->enum('status', ['pending', 'approved', 'rejected', 'dikembalikan'])->default('pending')->change();
        });
    }
};
