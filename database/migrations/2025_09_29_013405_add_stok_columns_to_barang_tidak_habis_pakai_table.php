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
    Schema::table('barang_tidak_habis_pakai', function (Blueprint $table) {
        $table->integer('stok_baik')->default(0);
        $table->integer('stok_kurang_baik')->default(0);
        $table->integer('stok_tidak_baik')->default(0);
    });
}

public function down(): void
{
    Schema::table('barang_tidak_habis_pakai', function (Blueprint $table) {
        $table->dropColumn(['stok_baik', 'stok_kurang_baik', 'stok_tidak_baik']);
    });
}

};
