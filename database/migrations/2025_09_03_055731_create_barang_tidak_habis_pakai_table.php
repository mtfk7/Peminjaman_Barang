<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_tidak_habis_pakai', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->string('kode_barang')->unique();
             $table->string('satuan');
            $table->integer('tahun_perolehan');
            $table->integer('total_stok')->default(0);
           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_tidak_habis_pakai');
    }
};
