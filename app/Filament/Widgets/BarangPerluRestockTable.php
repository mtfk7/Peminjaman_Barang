<?php

namespace App\Filament\Widgets;

use App\Models\BarangHabisPakai;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class BarangPerluRestockTable extends BaseWidget
{
    protected static ?string $heading = 'Barang Habis Pakai yang Perlu Restock';

    protected function getTableQuery(): Builder
    {
        return BarangHabisPakai::query()
            ->whereColumn('total_stok', '<=', 'batas_minimum');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nama_barang')->label('Nama Barang'),
            Tables\Columns\TextColumn::make('total_stok')->label('Stok Sekarang'),
            Tables\Columns\TextColumn::make('batas_minimum')->label('Batas Minimum'),
        ];
    }
}
