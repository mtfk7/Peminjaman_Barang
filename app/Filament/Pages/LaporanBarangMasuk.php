<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use App\Models\BarangMasuk;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Carbon\Carbon;

class LaporanBarangMasuk extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Laporan Barang';
    protected static ?string $navigationLabel = 'Laporan Barang Masuk';
    protected static string $view = 'filament.pages.laporan-barang-masuk';
    protected static ?int $navigationSort = 8;

    public $periode = 'harian';
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $jenis_barang;
    public $laporan = [];

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('periode')
                ->label('Periode')
                ->options([
                    'harian' => 'Harian (Hari Ini)',
                    'mingguan' => 'Mingguan (7 Hari Terakhir)',
                    'bulanan' => 'Bulanan (30 Hari Terakhir)',
                    'custom' => 'Custom (Rentang Tanggal)',
                ])
                ->default('harian')
                ->reactive(),

            Forms\Components\DatePicker::make('tanggal_mulai')
                ->label('Dari Tanggal')
                ->visible(fn ($get) => $get('periode') === 'custom')
                ->required(fn ($get) => $get('periode') === 'custom'),

            Forms\Components\DatePicker::make('tanggal_selesai')
                ->label('Sampai Tanggal')
                ->visible(fn ($get) => $get('periode') === 'custom')
                ->required(fn ($get) => $get('periode') === 'custom'),

            Forms\Components\Select::make('jenis_barang')
                ->label('Jenis Barang')
                ->options([
                    'semua' => 'Semua Jenis',
                    'habis' => 'Barang Habis Pakai',
                    'tidak_habis' => 'Barang Tidak Habis Pakai',
                ])
                ->default('semua'),
        ];
    }

    public function generateReport()
{
    $query = BarangMasuk::query()
        ->with(['barangHabisPakai', 'barangTidakHabisPakai']) // ✅ tambahkan ini
        ->orderBy('tanggal_masuk', 'asc');

    // Filter jenis barang
    if ($this->jenis_barang && $this->jenis_barang !== 'semua') {
        $query->where('jenis_barang', $this->jenis_barang);
    }

    // Filter periode
    if ($this->periode === 'harian') {
        $query->whereDate('tanggal_masuk', Carbon::today());
    } elseif ($this->periode === 'mingguan') {
        $query->whereBetween('tanggal_masuk', [
            Carbon::now()->subDays(7)->toDateString(),
            Carbon::now()->toDateString(),
        ]);
    } elseif ($this->periode === 'bulanan') {
        $query->whereBetween('tanggal_masuk', [
            Carbon::now()->subDays(30)->toDateString(),
            Carbon::now()->toDateString(),
        ]);
    } elseif ($this->periode === 'custom' && $this->tanggal_mulai && $this->tanggal_selesai) {
        $query->whereBetween('tanggal_masuk', [
            $this->tanggal_mulai,
            $this->tanggal_selesai,
        ]);
    }

    $this->laporan = $query->get();

    if ($this->laporan->isEmpty()) {
        Notification::make()
            ->title('Tidak ada data pada periode yang dipilih.')
            ->warning()
            ->send();
    }
}


    // public function generateReport()
    // {
    //     $query = BarangMasuk::query()->orderBy('tanggal_masuk', 'asc');

    //     // 🔹 Filter berdasarkan jenis barang
    //     if ($this->jenis_barang && $this->jenis_barang !== 'semua') {
    //         $query->where('jenis_barang', $this->jenis_barang);
    //     }

    //     // 🔹 Filter berdasarkan periode
    //     if ($this->periode === 'harian') {
    //         $query->whereDate('tanggal_masuk', Carbon::today());
    //     } elseif ($this->periode === 'mingguan') {
    //         $query->whereBetween('tanggal_masuk', [
    //             Carbon::now()->subDays(7)->toDateString(),
    //             Carbon::now()->toDateString(),
    //         ]);
    //     } elseif ($this->periode === 'bulanan') {
    //         $query->whereBetween('tanggal_masuk', [
    //             Carbon::now()->subDays(30)->toDateString(),
    //             Carbon::now()->toDateString(),
    //         ]);
    //     } elseif ($this->periode === 'custom' && $this->tanggal_mulai && $this->tanggal_selesai) {
    //         $query->whereBetween('tanggal_masuk', [
    //             $this->tanggal_mulai,
    //             $this->tanggal_selesai,
    //         ]);
    //     }

    //     $this->laporan = $query->get();

    //     if ($this->laporan->isEmpty()) {
    //         Notification::make()
    //             ->title('Tidak ada data pada periode yang dipilih.')
    //             ->warning()
    //             ->send();
    //     }
    // }

    public function exportPdf()
    {
        if (empty($this->laporan)) {
            Notification::make()
                ->title('Silakan tampilkan laporan terlebih dahulu sebelum mencetak.')
                ->warning()
                ->send();
            return;
        }

        $pdf = Pdf::loadView('pdf.laporan-barang-masuk', [
            'laporan' => $this->laporan,
            'periode' => $this->periode,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'jenis_barang' => $this->jenis_barang,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'laporan-barang-masuk.pdf');
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user->hasAnyRole(['Kepala', 'Admin Persediaan Barang']);
    }
}
