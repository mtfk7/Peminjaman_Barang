<?php

namespace App\Filament\Widgets;

use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;
use App\Models\Peminjaman;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $totalHabis = BarangHabisPakai::count();
        $totalTidakHabis = BarangTidakHabisPakai::count();

        $today = Carbon::today();

        // Barang tidak habis pakai yang sedang dipinjam (status Dipinjam)
        $dipinjam = Peminjaman::where('status', 'Dipinjam')
            ->where('jenis_barang', 'tidak_habis_pakai')
            ->whereDate('tanggal_pinjam', $today)
            ->sum('jumlah');

        // Barang tidak habis pakai yang sudah dikembalikan (status Dikembalikan)
        $dikembalikan = Peminjaman::where('status', 'Dikembalikan')
            ->where('jenis_barang', 'tidak_habis_pakai')
            ->whereDate('tanggal_pinjam', $today) // pakai tanggal_pinjam, bukan tanggal_kembali
            ->sum('jumlah');

        return [
            Card::make('Total Barang Habis Pakai', $totalHabis)
                ->icon('heroicon-o-archive-box')
                ->color('success'),

            Card::make('Total Barang Tidak Habis Pakai', $totalTidakHabis)
                ->icon('heroicon-o-cube')
                ->color('info'),

            Card::make('Barang Sedang Dipinjam', $dipinjam)
                ->icon('heroicon-o-arrow-path')
                ->color($dipinjam > 0 ? 'warning' : 'success'),

            Card::make('Barang Sudah Dikembalikan', $dikembalikan)
                ->icon('heroicon-o-arrow-uturn-down')
                ->color($dikembalikan > 0 ? 'success' : 'secondary'),
        ];
    }
}
