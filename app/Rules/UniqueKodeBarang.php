<?php

namespace App\Rules;

use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;
use Illuminate\Contracts\Validation\Rule;

class UniqueKodeBarang implements Rule
{
    protected $ignoreId;
    protected $table;

    public function __construct($ignoreId = null, $table = null)
    {
        $this->ignoreId = $ignoreId; // biar pas edit tidak bentrok
        $this->table = $table;       // biar bisa tahu dari resource mana
    }

    public function passes($attribute, $value): bool
    {
        // ✅ Cek di tabel Barang Habis Pakai
        $queryHabis = BarangHabisPakai::where('kode_barang', $value);
        if ($this->table === 'habis' && $this->ignoreId) {
            $queryHabis->where('id', '!=', $this->ignoreId);
        }
        if ($queryHabis->exists()) {
            return false;
        }

        // ✅ Cek di tabel Barang Tidak Habis Pakai
        $queryTidak = BarangTidakHabisPakai::where('kode_barang', $value);
        if ($this->table === 'tidak' && $this->ignoreId) {
            $queryTidak->where('id', '!=', $this->ignoreId);
        }
        if ($queryTidak->exists()) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'Gunakan Kode Barang Yang Lain';
    }
}
