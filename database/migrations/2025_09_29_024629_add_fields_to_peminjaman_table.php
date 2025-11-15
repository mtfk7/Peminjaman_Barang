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
    Schema::table('peminjaman', function (Blueprint $table) {
        
        $table->string('kelas')->nullable();
        $table->time('jam_pinjam')->nullable();
        $table->time('jam_kembali')->nullable();
    });
}

public function down(): void
{
    Schema::table('peminjaman', function (Blueprint $table) {
        $table->dropColumn(['tanggal_pinjam', 'kelas', 'jam_pinjam', 'jam_kembali']);
    });
}

};
