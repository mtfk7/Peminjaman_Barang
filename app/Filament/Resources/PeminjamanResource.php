<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?string $navigationGroup = 'Peminjaman Barang';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis_barang')
                    ->label('Jenis Barang')
                    ->options([
                        'habis_pakai' => 'Habis Pakai',
                        'tidak_habis_pakai' => 'Tidak Habis Pakai',
                    ])
                    ->required()
                    ->reactive(),

                Forms\Components\Select::make('barang_id')
                    ->label('Barang')
                    ->options(function (callable $get) {
                        if ($get('jenis_barang') === 'habis_pakai') {
                            return BarangHabisPakai::pluck('nama_barang', 'id');
                        }
                        if ($get('jenis_barang') === 'tidak_habis_pakai') {
                            return BarangTidakHabisPakai::pluck('nama_barang', 'id');
                        }
                        return [];
                    })
                    ->required()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $jenis = $get('jenis_barang');
                        $barang = null;

                        if ($jenis === 'habis_pakai') {
                            $barang = BarangHabisPakai::find($state);
                        } elseif ($jenis === 'tidak_habis_pakai') {
                            $barang = BarangTidakHabisPakai::find($state);
                        }

                        $set('satuan', $barang?->satuan);
                    }),

                //                Forms\Components\Repeater::make('details')
                // ->relationship()
                // ->schema([
                //     Forms\Components\Select::make('jenis_barang')
                //         ->options([
                //             'habis_pakai' => 'Habis Pakai',
                //             'tidak_habis_pakai' => 'Tidak Habis Pakai',
                //         ])
                //         ->required()
                //         ->reactive(),

                // Forms\Components\Select::make('barang_id')
                //     ->label('Barang')
                //     ->options(function (callable $get) {
                //         if ($get('jenis_barang') === 'habis_pakai') {
                //             return BarangHabisPakai::pluck('nama_barang', 'id');
                //         } elseif ($get('jenis_barang') === 'tidak_habis_pakai') {
                //             return BarangTidakHabisPakai::pluck('nama_barang', 'id');
                //         }
                //         return [];
                //     })
                //     ->required()
                //     ->reactive()
                //     ->afterStateUpdated(function ($state, callable $set, callable $get) {
                //         $jenis = $get('jenis_barang');

                //         if ($jenis === 'habis_pakai') {
                //             $barang = BarangHabisPakai::find($state);
                //         } elseif ($jenis === 'tidak_habis_pakai') {
                //             $barang = BarangTidakHabisPakai::find($state);
                //         } else {
                //             $barang = null;
                //         }

                //         // isi otomatis kolom satuan
                //         $set('satuan', $barang?->satuan);
                //         // simpan stok untuk validasi nanti
                //         $set('stok_tersedia', $barang?->total_stok);
                //     }),

                // Forms\Components\TextInput::make('jumlah')
                //     ->numeric()
                //     ->required()
                //     ->reactive()
                //     ->rule(function (callable $get) {
                //         return function (string $attribute, $value, \Closure $fail) use ($get) {
                //             $stok = $get('stok_tersedia');
                //             if ($stok !== null && $value > $stok) {
                //                 $fail("Jumlah melebihi stok tersedia ({$stok}).");
                //             }
                //         };
                //     }),

                Forms\Components\TextInput::make('satuan')
                    ->label('Satuan')
                    ->disabled()
                    ->dehydrated(true),



                Forms\Components\TextInput::make('nama_peminjam')
                    ->label('Nama Peminjam')
                    ->required(),

                Forms\Components\TextInput::make('jumlah')
    ->label('Jumlah')
    ->numeric()
    ->required()
    ->minValue(1)
    ->rule(function (callable $get) {
        return function (string $attribute, $value, $fail) use ($get) {
            $status = $get('status');
            $jenis = $get('jenis_barang');
            $barangId = $get('barang_id');

            // ⚠️ Abaikan validasi stok kalau status sudah "Dikembalikan"
            if ($status === 'Dikembalikan' || !$barangId) {
                return;
            }

            if ($jenis === 'habis_pakai') {
                $barang = BarangHabisPakai::find($barangId);
                if ($barang && $value > $barang->total_stok) {
                    $fail("Jumlah melebihi stok tersedia ({$barang->total_stok}).");
                }
            }

            if ($jenis === 'tidak_habis_pakai') {
                $barang = BarangTidakHabisPakai::find($barangId);
                if ($barang && $value > $barang->stok_baik) {
                    $fail("Jumlah melebihi stok baik yang tersedia ({$barang->stok_baik}).");
                }
            }
        };
    }),



                Forms\Components\DatePicker::make('tanggal_pinjam')
                    ->label('Tanggal Pinjam')
                    ->default(now())
                    ->required(),

                Forms\Components\TextInput::make('kelas')
                    ->label('Kelas')
                    ->required(),

                Forms\Components\TextInput::make('jam_pinjam')
                    ->label('Jam Pinjam')
                    ->default(now()->format('H:i:s'))
                    ->disabled()
                    ->dehydrated(true),

                // Forms\Components\TextInput::make('jam_kembali')
                // ->label('Jam Kembali')
                // ->default(null)   // biar kosong saat buat baru
                // ->disabled(fn (callable $get) => $get('status') !== 'Dikembalikan') // aktif cuma saat Dikembalikan
                // ->dehydrated(true), // tetap tersimpan

                Forms\Components\Select::make('kondisi_kembali')
                    ->label('Kondisi Setelah Dikembalikan')
                    ->options([
                        'baik' => 'Baik',
                        'kurang_baik' => 'Kurang Baik',
                        'tidak_baik' => 'Tidak Baik',
                    ])
                    ->visible(
                        fn(callable $get) =>
                        $get('jenis_barang') === 'tidak_habis_pakai' && $get('status') === 'Dikembalikan'
                    )
                    ->required(
                        fn(callable $get) =>
                        $get('jenis_barang') === 'tidak_habis_pakai' && $get('status') === 'Dikembalikan'
                    ),

                Forms\Components\TextInput::make('jam_kembali')
                    ->label('Jam Kembali')
                    ->disabled()
                    ->default(
                        fn($record, $get) =>
                        ($record?->jam_kembali)
                        ? $record->jam_kembali
                        : ($get('status') === 'Dikembalikan'
                            ? now('Asia/Makassar')->format('H:i:s')
                            : null)
                    )
                    ->visible(fn($get) => $get('status') === 'Dikembalikan')
                    ->reactive(),




                // Forms\Components\Select::make('kondisi_kembali')
                //     ->label('Kondisi Setelah Dikembalikan')
                //     ->options([
                //         'baik' => 'Baik',
                //         'kurang_baik' => 'Kurang Baik',
                //         'tidak_baik' => 'Tidak Baik',
                //     ])
                //     ->visible(
                //         fn(callable $get) =>
                //         $get('jenis_barang') === 'tidak_habis_pakai' && $get('status') === 'Dikembalikan'
                //     )
                //     ->required(
                //         fn(callable $get) =>
                //         $get('jenis_barang') === 'tidak_habis_pakai' && $get('status') === 'Dikembalikan'
                //     ),


                Forms\Components\Select::make('status')
    ->label('Status')
    ->options(function (callable $get) {
        // Jika jenis barang habis pakai → hanya boleh "Dipinjam"
        if ($get('jenis_barang') === 'habis_pakai') {
            return [
                'Dipinjam' => 'Dipinjam',
            ];
        }

        // Jika tidak habis pakai → boleh dikembalikan
        return [
            'Dipinjam' => 'Dipinjam',
            'Dikembalikan' => 'Dikembalikan',
        ];
    })
    ->default('Dipinjam')
    ->required(),




                // Forms\Components\Select::make('status')
                //     ->label('Status')
                //     ->options(function (callable $get) {
                //         if ($get('jenis_barang') === 'habis_pakai') {
                //             return [
                //                 'Dipinjam' => 'Dipinjam',
                //             ];
                //         }

                //         return [
                //             'Dipinjam'    => 'Dipinjam',
                //             'Dikembalikan' => 'Dikembalikan',
                //         ];
                //     })
                //     ->default('Dipinjam')
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('nama_peminjam')->label('Nama Peminjam')->searchable(), Tables\Columns\TextColumn::make('jenis_barang')->label('Jenis')->sortable(), Tables\Columns\TextColumn::make('barang_id')->label('Barang')->getStateUsing(fn($record) => $record->jenis_barang === 'habis_pakai' ? BarangHabisPakai::find($record->barang_id)?->nama_barang : BarangTidakHabisPakai::find($record->barang_id)?->nama_barang), Tables\Columns\TextColumn::make('jumlah')->label('Jumlah'), Tables\Columns\TextColumn::make('satuan')->label('Satuan'), Tables\Columns\TextColumn::make('kelas')->label('Kelas'), Tables\Columns\TextColumn::make('tanggal_pinjam')->date()->label('Tanggal Pinjam'), Tables\Columns\TextColumn::make('jam_pinjam')->label('Jam Pinjam')->dateTime('H:i'), Tables\Columns\TextColumn::make('jam_kembali')->label('Jam Kembali')->dateTime('H:i'), Tables\Columns\TextColumn::make('kondisi_kembali')->label('Kondisi Kembali')->formatStateUsing(fn($state) => match ($state) { 'baik' => 'Baik', 'kurang_baik' => 'Kurang Baik', 'tidak_baik' => 'Tidak Baik', default => '-', }), Tables\Columns\BadgeColumn::make('status')->colors(['warning' => 'Dipinjam', 'success' => 'Dikembalikan',]),])->filters([Tables\Filters\SelectFilter::make('jenis_barang')->label('Jenis Barang')->options(['habis_pakai' => 'Habis Pakai', 'tidak_habis_pakai' => 'Tidak Habis Pakai',]), Tables\Filters\SelectFilter::make('status')->label('Status')->options(['Dipinjam' => 'Dipinjam', 'Dikembalikan' => 'Dikembalikan',]),])->actions([Tables\Actions\EditAction::make()->visible(fn($record) => $record->status !== 'Dikembalikan'), Tables\Actions\DeleteAction::make(),])->bulkActions([Tables\Actions\DeleteBulkAction::make(),]);
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // hanya tampilkan peminjaman hari ini
        return parent::getEloquentQuery()
            ->whereDate('tanggal_pinjam', now()->toDateString());
    }

    // public static function canViewAny(): bool
// {
//     // return auth()->user()->hasAnyRole(['Kepala', 'Admin Peminjaman Barang', 'admin']);
//     return true;
// }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['Kepala', 'Admin Peminjaman Barang']);
    }


    // public static function canCreate(): bool
// {
//     // return auth()->user()->can('create_peminjaman');
//     return true;
// }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['Kepala', 'Admin Peminjaman Barang']);
    }


    public static function canEdit($record): bool
    {
        // return auth()->user()->can('approve_peminjaman');
        return true;
    }

    public static function canDelete($record): bool
    {
        return true; // biasanya data peminjaman tidak dihapus
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}
