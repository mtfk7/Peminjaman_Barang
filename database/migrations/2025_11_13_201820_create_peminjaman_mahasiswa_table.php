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
        Schema::create('peminjaman_mahasiswa', function (Blueprint $table) {
            $table->id();
            
            // Data Mahasiswa
            $table->string('nama_mahasiswa');
            $table->string('nim'); // Removed unique() - one student can borrow multiple times
            $table->string('email');
            $table->string('no_telp');
            
            // Data Barang yang Dipinjam
            $table->string('barang_id');
            $table->string('nama_barang');
            $table->enum('jenis_barang', ['habis_pakai', 'tidak_habis_pakai']);
            $table->integer('jumlah');
            $table->string('satuan')->nullable();
            
            // Informasi Peminjaman
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->text('keperluan')->nullable();
            
            // QR Code & Status
            $table->string('qr_code')->unique(); // Token unik untuk QR
            $table->enum('status', ['pending', 'approved', 'rejected', 'dikembalikan'])->default('pending');
            
            // Approval Info
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_mahasiswa');
    }
};
