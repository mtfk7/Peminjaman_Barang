<?php

namespace App\Filament\Resources\LaporanDataBarangTidakHabisPakaiResource\Pages;

use App\Filament\Resources\LaporanDataBarangTidakHabisPakaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BarangTidakHabisPakai;

class ListLaporanDataBarangTidakHabisPakais extends ListRecords
{
    protected static string $resource = LaporanDataBarangTidakHabisPakaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('Export PDF')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $records = BarangTidakHabisPakai::all();

                    $pdf = Pdf::loadView('exports.laporan-barang-tidak-habis-pakai', [
                        'records' => $records,
                    ]);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'laporan-barang-tidak-habis-pakai.pdf');
                }),
        ];
    }
}
