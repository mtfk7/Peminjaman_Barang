<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role jika belum ada
        $roles = [
            'Kepala',
            'Admin Persediaan Barang',
            'Admin Peminjaman Barang',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Buat user contoh sesuai struktur tabel kamu
        $kepala = User::firstOrCreate(
            ['username' => 'kepala'],
            [
                'nama_user' => 'Kepala Penannggung Jawab',
                'email' => 'kepala@example.com',
                'no_telp' => '081111111111',
                'password' => bcrypt('password'),
                'role' => 'Kepala',
            ]
        );
        $kepala->assignRole('Kepala');

        $adminPersediaan = User::firstOrCreate(
            ['username' => 'persediaan'],
            [
                'nama_user' => 'Admin Persediaan',
                'email' => 'persediaan@example.com',
                'no_telp' => '082222222222',
                'password' => bcrypt('password'),
                'role' => 'Admin Persediaan Barang',
            ]
        );
        $adminPersediaan->assignRole('Admin Persediaan Barang');

        $adminPeminjaman = User::firstOrCreate(
            ['username' => 'peminjaman'],
            [
                'nama_user' => 'Admin Peminjaman',
                'email' => 'peminjaman@example.com',
                'no_telp' => '083333333333',
                'password' => bcrypt('password'),
                'role' => 'Admin Peminjaman Barang',
            ]
        );
        $adminPeminjaman->assignRole('Admin Peminjaman Barang');
    }
}
