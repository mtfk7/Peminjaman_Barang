<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriBarang;

class KategoriBarangSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Elektronik',
            'ATK',
            'Furniture',
        
        ];

        foreach ($kategori as $k) {
            KategoriBarang::create([
                'nama_kategori' => $k,
            ]);
        }
    }
}
