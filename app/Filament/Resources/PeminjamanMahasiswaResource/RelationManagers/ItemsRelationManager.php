<?php

namespace App\Filament\Resources\PeminjamanMahasiswaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\BarangHabisPakai;
use App\Models\BarangTidakHabisPakai;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items Peminjaman';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('barang_id')
                    ->label('ID Barang')
                    ->required(),
                Forms\Components\TextInput::make('nama_barang')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis_barang')
                    ->label('Jenis Barang')
                    ->options([
                        'habis_pakai' => 'Habis Pakai',
                        'tidak_habis_pakai' => 'Tidak Habis Pakai',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('satuan')
                    ->label('Satuan')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'dikembalikan' => 'Dikembalikan',
                    ])
                    ->required(),
                Forms\Components\TimePicker::make('jam_kembali')
                    ->label('Jam Kembali')
                    ->seconds(false)
                    ->visible(fn ($record) => $record && $record->jam_kembali),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_barang')
            ->columns([
                Tables\Columns\TextColumn::make('nama_barang')
                    ->label('Nama Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_barang')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'habis_pakai' => 'warning',
                        'tidak_habis_pakai' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'habis_pakai' => 'Habis Pakai',
                        'tidak_habis_pakai' => 'Tidak Habis Pakai',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($record) => $record->jumlah . ' ' . $record->satuan),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'info' => 'dikembalikan',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'dikembalikan' => 'Dikembalikan',
                        default => $state
                    }),
                Tables\Columns\TextColumn::make('jam_kembali')
                    ->label('Jam Kembali')
                    ->formatStateUsing(fn ($state) => $state ? date('H:i', strtotime($state)) : '-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian Barang')
                    ->modalDescription('Apakah Anda yakin ingin mengembalikan barang ini? Stok akan ditambahkan kembali dan jam kembali akan otomatis terisi. (Hanya untuk barang tidak habis pakai)')
                    ->action(function ($record) {
                        if ($record->status !== 'approved') {
                            throw new \Exception('Hanya item yang sudah disetujui yang bisa dikembalikan.');
                        }
                        
                        if ($record->jenis_barang === 'habis_pakai') {
                            throw new \Exception('Barang habis pakai tidak perlu dikembalikan.');
                        }

                        $record->update([
                            'status' => 'dikembalikan',
                            'jam_kembali' => now()->format('H:i:s'),
                        ]);
                    })
                    ->visible(fn ($record) => $record->status === 'approved' && $record->jenis_barang === 'tidak_habis_pakai'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}





