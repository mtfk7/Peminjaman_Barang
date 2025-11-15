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
            // Drop unique index on nim column
            $table->dropUnique(['nim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_mahasiswa', function (Blueprint $table) {
            // Re-add unique constraint on nim (if needed to rollback)
            $table->unique('nim');
        });
    }
};
