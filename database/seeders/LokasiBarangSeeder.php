<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LokasiBarang;

class LokasiBarangSeeder extends Seeder
{
    public function run(): void
    {
        $lokasi = [
            'Gudang A',
            'Gudang B',
            
        ];

        foreach ($lokasi as $l) {
            LokasiBarang::create([
                'nama_lokasi' => $l,
            ]);
        }
    }
}
