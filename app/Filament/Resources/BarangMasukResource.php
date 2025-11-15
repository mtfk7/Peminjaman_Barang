<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangMasukResource\Pages;
use App\Models\BarangMasuk;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class BarangMasukResource extends Resource
{
    protected static ?string $model = BarangMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = 'Barang Masuk';
    protected static ?string $navigationLabel = 'Barang Masuk';
    protected static ?int $navigationSort = 4;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Pilih jenis barang
                Forms\Components\Select::make('jenis_barang')
                    ->label('Jenis Barang')
                    ->options([
                        'habis' => 'Barang Habis Pakai',
                        'tidak_habis' => 'Barang Tidak Habis Pakai',
                    ])
                    ->reactive()
                    ->required(),

                // Pilih barang berdasarkan jenis
                Forms\Components\Select::make('barang_id')
                    ->label('Nama Barang')
                    ->options(function (callable $get) {
                        if ($get('jenis_barang') === 'tidak_habis') {
                            return BarangTidakHabisPakai::pluck('nama_barang', 'id');
                        }
                        return BarangHabisPakai::pluck('nama_barang', 'id');
                    })
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($get('jenis_barang') === 'tidak_habis') {
                            $barang = BarangTidakHabisPakai::find($state);
                        } else {
                            $barang = BarangHabisPakai::find($state);
                        }

                        if ($barang) {
                            $set('satuan', $barang->satuan);
                        } else {
                            $set('satuan', null);
                        }
                    })
                    ->required(),

                // Menampilkan satuan barang
                // Forms\Components\TextInput::make('satuan')
                //     ->label('Satuan')
                //     ->disabled()
                //     ->dehydrated(false),

                Forms\Components\TextInput::make('satuan')
                    ->label('Satuan')
                    ->disabled()
                    ->required(),


                // Input jumlah barang masuk
                Forms\Components\TextInput::make('jumlah_masuk')
                    ->label('Jumlah Masuk')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                // Input tanggal masuk
                Forms\Components\DatePicker::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->default(now())
                    ->required(),

                // Input keterangan opsional
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // Jenis barang
                Tables\Columns\TextColumn::make('jenis_barang')
                    ->label('Jenis Barang')
                    ->badge()
                    ->color(fn ($state) => $state === 'habis' ? 'warning' : 'info'),

                // Nama barang ambil dari accessor di model BarangMasuk
                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->sortable()
                    ->searchable(),

                // Jumlah masuk
                Tables\Columns\TextColumn::make('jumlah_masuk')
                    ->label('Jumlah Masuk'),

                    // Satuan
                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),

                // Tanggal masuk
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->date('d M Y')
                    ->label('Tanggal Masuk'),

                // Keterangan
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->color('primary'),

                Tables\Actions\DeleteAction::make(),

                
            ])
            ->emptyStateHeading('Belum ada data barang masuk');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['Kepala', 'Admin Persediaan Barang']);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangMasuks::route('/'),
            'create' => Pages\CreateBarangMasuk::route('/create'),
            'edit' => Pages\EditBarangMasuk::route('/{record}/edit'),
        ];
    }
}
