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
        $table->string('kondisi_kembali')->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('peminjaman', function (Blueprint $table) {
        $table->dropColumn('kondisi_kembali');
    });
}

};
