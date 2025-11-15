<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Livewire\WithPagination;
use App\Models\Peminjaman;
use App\Models\PeminjamanMahasiswa;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Facades\Filament;

class HistoriPeminjaman extends Page implements HasForms
{
    use WithPagination, InteractsWithForms;

    protected static string $view = 'filament.pages.histori-peminjaman';

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Histori Peminjaman';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Peminjaman Barang';

    public ?string $periode = 'harian';
    public ?string $from = null;
    public ?string $until = null;
    public ?string $jenis_barang = null; // filter jenis
    public bool $showReport = false;

    public function mount(): void
    {
        $this->from = now()->toDateString();
        $this->until = now()->toDateString();

        $this->form->fill([
            'periode' => $this->periode,
            'from' => $this->from,
            'until' => $this->until,
            'jenis_barang' => $this->jenis_barang,
        ]);
    }

     public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user->hasAnyRole(['Kepala', 'Admin Peminjaman Barang']);
    }


    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('periode')
                ->label('Periode')
                ->options([
                    'harian' => 'Harian',
                    'mingguan' => 'Mingguan',
                    'bulanan' => 'Bulanan',
                    'custom' => 'Custom',
                ])
                ->reactive()
                ->default('harian'),

            Forms\Components\DatePicker::make('from')
                ->label('Dari Tanggal'),

            Forms\Components\DatePicker::make('until')
                ->label('Sampai Tanggal')
                ->visible(fn ($get) => $get('periode') === 'custom'),

            Forms\Components\Select::make('jenis_barang')
                ->label('Jenis Barang')
                ->options([
                    'habis_pakai' => 'Habis Pakai',
                    'tidak_habis_pakai' => 'Tidak Habis Pakai',
                ])
                ->placeholder('Semua Jenis')
                ->reactive(),
        ];
    }

    public function generateReport(): void
    {
        $this->showReport = true;
        $this->resetPage();
    }

    public function resetFilter(): void
    {
        $this->periode = 'harian';
        $this->from = now()->toDateString();
        $this->until = now()->toDateString();
        $this->jenis_barang = null;
        $this->showReport = false;
        $this->resetPage();

        $this->form->fill([
            'periode' => $this->periode,
            'from' => $this->from,
            'until' => $this->until,
            'jenis_barang' => $this->jenis_barang,
        ]);
    }

    public function getPeminjamanProperty()
    {
        $query = Peminjaman::with(['barangHabisPakai', 'barangTidakHabisPakai']);

        if ($this->showReport) {
            if ($this->periode === 'harian' && $this->from) {
                $query->whereDate('tanggal_pinjam', $this->from);
            } elseif ($this->periode === 'mingguan' && $this->from) {
                $query->whereBetween('tanggal_pinjam', [
                    now()->parse($this->from)->startOfWeek(),
                    now()->parse($this->from)->endOfWeek(),
                ]);
            } elseif ($this->periode === 'bulanan' && $this->from) {
                $query->whereMonth('tanggal_pinjam', date('m', strtotime($this->from)))
                      ->whereYear('tanggal_pinjam', date('Y', strtotime($this->from)));
            } elseif ($this->periode === 'custom' && $this->from && $this->until) {
                $query->whereBetween('tanggal_pinjam', [$this->from, $this->until]);
            }
        } else {
            $query->whereDate('tanggal_pinjam', '<', now()->toDateString());
        }

        if ($this->jenis_barang) {
            $query->where('jenis_barang', $this->jenis_barang);
        }

        return $query->orderBy('tanggal_pinjam', 'desc')->paginate(10);
    }

    // Peminjaman Mahasiswa (dari web mobile)
    public function getPeminjamanMahasiswaProperty()
    {
        $query = PeminjamanMahasiswa::with(['mahasiswa', 'approver', 'items']);

        if ($this->showReport) {
            if ($this->periode === 'harian' && $this->from) {
                $query->whereDate('tanggal_pinjam', $this->from);
            } elseif ($this->periode === 'mingguan' && $this->from) {
                $query->whereBetween('tanggal_pinjam', [
                    now()->parse($this->from)->startOfWeek(),
                    now()->parse($this->from)->endOfWeek(),
                ]);
            } elseif ($this->periode === 'bulanan' && $this->from) {
                $query->whereMonth('tanggal_pinjam', date('m', strtotime($this->from)))
                      ->whereYear('tanggal_pinjam', date('Y', strtotime($this->from)));
            } elseif ($this->periode === 'custom' && $this->from && $this->until) {
                $query->whereBetween('tanggal_pinjam', [$this->from, $this->until]);
            }
        }

        // Filter berdasarkan jenis barang di items
        if ($this->jenis_barang) {
            $query->whereHas('items', function($q) {
                $q->where('jenis_barang', $this->jenis_barang);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }
}
