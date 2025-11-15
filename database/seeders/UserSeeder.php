<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
     public function run(): void {
         DB::table('users')->updateOrInsert(
        ['username' => 'admin'], // cek berdasarkan username
        [
            'nama_user' => 'admin',
            'password' => Hash::make('password'),
           'role' => 'Admin Peminjaman Barang',
            'email' => 'admin@example.com',
            'no_telp' => '08123456789',
            'updated_at' => now(),
        ]
    );
    }
}
