<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class LaporanBarang extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.laporan-barang';
    protected static ?string $title = 'Laporan Barang Keluar';
    protected static ?string $navigationGroup = 'Laporan Barang';
    protected static ?int $navigationSort = 9;

    public $jenisBarang = 'habis_pakai';
    public $periode = 'harian';
    public $tanggalMulai;
    public $tanggalSelesai;
    public $laporan = [];

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user->hasAnyRole(['Kepala', 'Admin Persediaan Barang']);
    }

    public function mount()
    {
        $this->tanggalMulai = Carbon::now()->format('Y-m-d');
        $this->tanggalSelesai = Carbon::now()->format('Y-m-d');
    }

    public function generateReport()
    {
        // Tentukan range tanggal berdasarkan periode
        switch ($this->periode) {
            case 'harian':
                $this->tanggalSelesai = $this->tanggalMulai;
                break;
            case 'mingguan':
                $this->tanggalMulai = Carbon::now()->subDays(6)->format('Y-m-d');
                $this->tanggalSelesai = Carbon::now()->format('Y-m-d');
                break;
            case 'bulanan':
                $this->tanggalMulai = Carbon::now()->subDays(29)->format('Y-m-d');
                $this->tanggalSelesai = Carbon::now()->format('Y-m-d');
                break;
        }

        // Query sesuai jenis barang
        if ($this->jenisBarang === 'habis_pakai') {
            $this->laporan = DB::table('peminjaman')
                ->join('barang_habis_pakai', 'peminjaman.barang_id', '=', 'barang_habis_pakai.id')
                ->select(
                    'barang_habis_pakai.nama_barang',
                    'barang_habis_pakai.satuan',
                    'barang_habis_pakai.total_stok',
                    'barang_habis_pakai.batas_minimum',
                    DB::raw('SUM(peminjaman.jumlah) as total_keluar')
                )
                ->where('peminjaman.jenis_barang', '=', 'habis_pakai')
                ->whereBetween('peminjaman.tanggal_pinjam', [$this->tanggalMulai, $this->tanggalSelesai])
                ->groupBy(
                    'barang_habis_pakai.nama_barang',
                    'barang_habis_pakai.satuan',
                    'barang_habis_pakai.total_stok',
                    'barang_habis_pakai.batas_minimum'
                )
                ->get();
        } else {
            $this->laporan = DB::table('peminjaman')
    ->join('barang_tidak_habis_pakai', 'peminjaman.barang_id', '=', 'barang_tidak_habis_pakai.id')
    ->select(
        'barang_tidak_habis_pakai.nama_barang',
        'barang_tidak_habis_pakai.satuan',
        DB::raw('SUM(peminjaman.jumlah) as total_dipinjam'),
        DB::raw('SUM(CASE WHEN kondisi_kembali = "baik" THEN peminjaman.jumlah ELSE 0 END) as kembali_baik'),
        DB::raw('SUM(CASE WHEN kondisi_kembali = "kurang_baik" THEN peminjaman.jumlah ELSE 0 END) as kembali_kurang_baik'),
        DB::raw('SUM(CASE WHEN kondisi_kembali = "tidak_baik" THEN peminjaman.jumlah ELSE 0 END) as kembali_tidak_baik'),
        'barang_tidak_habis_pakai.total_stok'
    )
    ->where('peminjaman.jenis_barang', '=', 'tidak_habis_pakai')
    ->whereBetween('peminjaman.tanggal_pinjam', [$this->tanggalMulai, $this->tanggalSelesai])
    ->groupBy(
        'barang_tidak_habis_pakai.nama_barang',
        'barang_tidak_habis_pakai.satuan',
        'barang_tidak_habis_pakai.total_stok'
    )
    ->get();

        }

        // ✅ Notifikasi otomatis
        if ($this->laporan->isEmpty()) {
            Notification::make()
                ->title('⚠️ Tidak ada data ditemukan')
                ->body('Silakan ubah filter tanggal atau jenis barang.')
                ->warning()
                ->duration(4000)
                ->send();
        } else {
            Notification::make()
                ->title('✅ Laporan berhasil dimuat')
                ->body('Data laporan barang berhasil ditampilkan.')
                ->success()
                ->duration(2500)
                ->send();
        }
    }

    public function exportPdf()
    {
        if (empty($this->laporan)) {
            Notification::make()
                ->title('⚠️ Tidak ada data untuk diexport')
                ->warning()
                ->duration(4000)
                ->send();
            return;
        }

        $pdf = Pdf::loadView('exports.laporan-barang-pdf', [
            'laporan' => $this->laporan,
            'jenisBarang' => $this->jenisBarang,
            'periode' => ucfirst($this->periode),
            'tanggalMulai' => $this->tanggalMulai,
            'tanggalSelesai' => $this->tanggalSelesai,
        ])->setPaper('a4', 'portrait');

        return Response::streamDownload(
            fn() => print($pdf->output()),
            'laporan-barang-' . now()->format('Ymd_His') . '.pdf'
        );
    }
}
