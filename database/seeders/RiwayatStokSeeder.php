<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class RiwayatStokSeeder extends Seeder
{
     public function run(): void {
        DB::table('riwayat_stok')->insert([
            [
                'id_barang' => 1,
                 'user_id' => 1,
                'jenis_transaksi' => 'masuk',
                'jumlah' => 5,
                'keterangan' => 'Pengadaan awal',
                'tanggal_transaksi' => now(),
                
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_barang' => 1,
                 'user_id' => 1,
                'jenis_transaksi' => 'masuk',
                'jumlah' => 100,
                'keterangan' => 'Stok awal bolpoin',
                'tanggal_transaksi' => now(),
                
                'created_at' => now(),
                'updated_at' => now(),
            ]
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
