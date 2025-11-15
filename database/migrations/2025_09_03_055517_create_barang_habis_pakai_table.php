<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_habis_pakai', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->string('kode_barang')->unique();

    
            // ✅ total stok otomatis = jumlah stok kondisi
            $table->integer('total_stok')->default(0);
            $table->integer('batas_minimum')->default(0);
            $table->string('satuan');
            $table->integer('tahun_perolehan');

           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_habis_pakai');
    }
};
