<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_barang', ['habis', 'tidak_habis']); // untuk tahu asalnya
            $table->unsignedBigInteger('barang_id'); // id dari tabel habis / tidak_habis
            $table->integer('jumlah_masuk');
            $table->date('tanggal_masuk')->default(now());
            $table->string('keterangan')->nullable();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
