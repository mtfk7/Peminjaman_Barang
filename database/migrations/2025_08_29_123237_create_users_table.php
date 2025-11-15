<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_user');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', [
                'Kepala',
                'Admin Persediaan Barang',
                'Admin Peminjaman Barang',
            ])->default('Admin Peminjaman Barang');
            $table->string('email')->unique();
            $table->string('no_telp');
            $table->timestamps();
           
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
