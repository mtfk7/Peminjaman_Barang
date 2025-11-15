<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class BarangSeeder extends Seeder
{
     public function run(): void {
        DB::table('barang')->insert([
            [
                'kode_barang' => 'EL-001',
                'nama_barang' => 'Proyektor',
                'id_kategori' => 1,
                'id_lokasi' => 1,
                'jenis_barang' => 'tidak habis pakai',
                'jumlah_stok' => 5,
                'kondisi_baik' => 4,
                'kondisi_kurang_baik' => 1,
                'kondisi_tidak_baik' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
           
        ]);
    }
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     //
    // }
}
