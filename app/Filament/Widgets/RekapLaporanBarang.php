<?php

namespace App\Filament\Widgets;

use App\Models\Peminjaman;
use Filament\Widgets\Widget;

class RekapLaporanBarang extends Widget
{
    protected static string $view = 'filament.widgets.rekap-laporan-barang';

    public function getRekap(): array
    {
        return [
            'habis_pakai' => Peminjaman::where('jenis_barang', 'habis_pakai')->sum('jumlah'),
            'tidak_habis_pakai' => Peminjaman::where('jenis_barang', 'tidak_habis_pakai')->sum('jumlah'),
            'total' => Peminjaman::sum('jumlah'),
        ];
    }

    public static function canView(): bool
{
    return false; // Supaya nggak pernah muncul di dashboard
}

}
