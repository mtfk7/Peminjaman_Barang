<?php
// app/Filament/Resources/BarangTidakHabisPakaiResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangTidakHabisPakaiResource\Pages;
use App\Models\BarangTidakHabisPakai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Rules\UniqueKodeBarang;

class BarangTidakHabisPakaiResource extends Resource
{
    protected static ?string $model = BarangTidakHabisPakai::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';
    protected static ?string $navigationLabel = 'Barang Tidak Habis Pakai';
    protected static ?string $navigationGroup = 'Data Barang';
    protected static ?string $modelLabel = 'Barang Tidak Habis Pakai';
    protected static ?string $pluralModelLabel = 'Barang Tidak Habis Pakai';
    protected static ?int $navigationSort = 4;




    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_barang')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('kode_barang')
                    ->label('Kode Barang')
                    ->required()
                    ->rule(fn ($record) => new UniqueKodeBarang($record?->id, 'tidak')),

                // ✅ Tambahkan kondisi stok
                Forms\Components\TextInput::make('stok_baik')
                    ->label('Stok Baik')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('stok_kurang_baik')
                    ->label('Stok Kurang Baik')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('stok_tidak_baik')
                    ->label('Stok Tidak Baik')
                    ->numeric()
                    ->default(0),

                // ✅ Total stok readonly (otomatis dihitung di model)
                Forms\Components\TextInput::make('total_stok')
                    ->label('Total Stok')
                    ->disabled()
                    ->dehydrated(false), // tidak dikirim dari form, biar dihitung otomatis di model

                Forms\Components\Select::make('satuan')
                    ->label('Satuan Barang')
                    ->options([
                        'pcs' => 'Pcs',
                        'unit' => 'Unit',
                        'box' => 'Box',
                        'meter' => 'Meter',
                    ])
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('tahun_perolehan')
                    ->label('Tahun Perolehan')
                    ->numeric()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_barang')->label('Kode Barang')->searchable(),
                Tables\Columns\TextColumn::make('nama_barang')->label('Nama Barang')->searchable(),
                Tables\Columns\TextColumn::make('stok_baik')->label('Baik'),
                Tables\Columns\TextColumn::make('stok_kurang_baik')->label('Kurang Baik'),
                Tables\Columns\TextColumn::make('stok_tidak_baik')->label('Tidak Baik'),
                Tables\Columns\TextColumn::make('total_stok')->label('Total Stok')->sortable(),
                Tables\Columns\TextColumn::make('satuan')->label('Satuan'),
                Tables\Columns\TextColumn::make('tahun_perolehan')->label('Tahun Perolehan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Tidak ada data barang')
            ->emptyStateDescription('Belum ada data barang yang ditambahkan.')
            ->emptyStateIcon('heroicon-o-inbox');
    }

    public static function canViewAny(): bool
{
    return auth()->user()->hasAnyRole(['Kepala', 'Admin Persediaan Barang']);
}

// public static function canCreate(): bool
// {
//     return auth()->user()->can('create_barang_tidak_habis');
// }

// public static function canEdit($record): bool
// {
//     // return auth()->user()->can('edit_barang_tidak_habis');
//     return true;
// }

// public static function canDelete($record): bool
// {
//     // return auth()->user()->can('delete_barang_tidak_habis');
//     return true;
// }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangTidakHabisPakais::route('/'),
            'create' => Pages\CreateBarangTidakHabisPakai::route('/create'),
            'edit' => Pages\EditBarangTidakHabisPakai::route('/{record}/edit'),
        ];
    }
}
