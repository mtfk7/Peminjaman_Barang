<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangHabisPakaiResource\Pages;
use App\Models\BarangHabisPakai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Rules\UniqueKodeBarang;

class BarangHabisPakaiResource extends Resource
{
    protected static ?string $model = BarangHabisPakai::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';
    protected static ?string $navigationLabel = 'Barang Habis Pakai';
    protected static ?string $navigationGroup = 'Data Barang';
    protected static ?string $modelLabel = 'Barang Habis Pakai';
    protected static ?string $pluralModelLabel = 'Barang Habis Pakai';
    protected static ?int $navigationSort = 3;

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
                    ->rule(fn ($record) => new UniqueKodeBarang($record?->id, 'habis')),

                Forms\Components\TextInput::make('total_stok')
                    ->label('Total Stok')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Forms\Components\TextInput::make('batas_minimum')
                    ->label('Batas Minimum')
                    ->numeric()
                    ->minValue(0)
                    ->default(1)
                    ->required(),

                Forms\Components\Select::make('satuan')
                    ->label('Satuan Barang')
                    ->options([
                        'pcs' => 'Pcs',
                        'unit' => 'Unit',
                        'box' => 'Box',
                        'meter' => 'Meter',
                        'sheet' => 'Sheet',
                        'rim' => 'Rim',
                    ])
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_barang')
                    ->label('Kode Barang')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_stok')
                    ->label('Total Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) =>
                        $record->total_stok <= $record->batas_minimum ? 'danger' : 'success'
                    )
                    ->tooltip(fn ($record) =>
                        $record->total_stok <= $record->batas_minimum
                            ? '⚠️ Stok sudah mencapai batas minimum, harap restock!'
                            : 'Stok aman'
                    ),

                Tables\Columns\TextColumn::make('batas_minimum')
                    ->label('Batas Minimum'),

                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Update')
                    ->dateTime('d M Y')
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangHabisPakais::route('/'),
            'create' => Pages\CreateBarangHabisPakai::route('/create'),
            'edit' => Pages\EditBarangHabisPakai::route('/{record}/edit'),
        ];
    }
}
