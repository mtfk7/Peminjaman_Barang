<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanMahasiswaResource\Pages;
use App\Filament\Resources\PeminjamanMahasiswaResource\RelationManagers;
use App\Models\PeminjamanMahasiswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeminjamanMahasiswaResource extends Resource
{
    protected static ?string $model = PeminjamanMahasiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Peminjaman Mahasiswa';
    protected static ?string $navigationGroup = 'Peminjaman';
    protected static ?string $modelLabel = 'Peminjaman Mahasiswa';
    protected static ?string $pluralModelLabel = 'Peminjaman Mahasiswa';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Mahasiswa')
                    ->schema([
                        Forms\Components\TextInput::make('nama_mahasiswa')
                            ->label('Nama Pemilik Akun')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_peminjam')
                            ->label('Nama Peminjam')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nama orang yang akan meminjam (bisa berbeda dari pemilik akun)'),
                        Forms\Components\TextInput::make('nim')
                            ->label('NIM')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel()
                            ->required(),
                    ])->columns(2),

                // Data Barang sekarang menggunakan items relationship (lihat RelationManager)

                Forms\Components\Section::make('Informasi Peminjaman')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->default(now())
                            ->disabled(),
                        Forms\Components\TimePicker::make('jam_pinjam')
                            ->label('Jam Pinjam')
                            ->required()
                            ->seconds(false),
                        Forms\Components\TimePicker::make('jam_kembali')
                            ->label('Jam Kembali')
                            ->disabled()
                            ->dehydrated()
                            ->seconds(false)
                            ->helperText('Otomatis terisi saat barang dikembalikan')
                            ->visible(fn ($record) => $record && $record->jam_kembali),
                        Forms\Components\TextInput::make('mata_kuliah')
                            ->label('Mata Kuliah')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kelas')
                            ->label('Kelas')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('keperluan')
                            ->label('Keperluan')
                            ->columnSpanFull(),
                        // Status sekarang dihitung dari items (status_keseluruhan)
                        Forms\Components\Placeholder::make('status_keseluruhan')
                            ->label('Status Keseluruhan')
                            ->content(fn ($record) => $record ? match($record->status_keseluruhan) {
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'dikembalikan' => 'Dikembalikan',
                                default => $record->status_keseluruhan
                            } : '-'),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->visible(fn ($record) => $record && $record->status_keseluruhan === 'rejected')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('items'))
            ->columns([
                Tables\Columns\TextColumn::make('nama_mahasiswa')
                    ->label('Pemilik Akun')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_peminjam')
                    ->label('Nama Peminjam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Jumlah Items')
                    ->counts('items')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('items_summary')
                    ->label('Items')
                    ->formatStateUsing(function ($record) {
                        $items = $record->items;
                        if ($items->isEmpty()) {
                            return '-';
                        }
                        $summary = $items->take(2)->map(fn($item) => $item->nama_barang . ' (' . $item->jumlah . ' ' . $item->satuan . ')')->join(', ');
                        if ($items->count() > 2) {
                            $summary .= ' +' . ($items->count() - 2) . ' more';
                        }
                        return $summary;
                    })
                    ->wrap()
                    ->limit(50),
                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_pinjam')
                    ->label('Jam Pinjam')
                    ->formatStateUsing(fn ($record) => $record->jam_pinjam ? date('H:i', strtotime($record->jam_pinjam)) : '-'),
                Tables\Columns\TextColumn::make('mata_kuliah')
                    ->label('Mata Kuliah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status_keseluruhan')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->status_keseluruhan)
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('scan_qr')
                    ->label('Scan QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->url(fn () => url('/admin/qr-scanner'))
                    ->openUrlInNewTab(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status_filter')
                    ->label('Status')
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['value']) || $data['value'] === null) {
                            return $query;
                        }
                        // Filter berdasarkan status_keseluruhan
                        return $query->whereHas('items', function($q) use ($data) {
                            $status = $data['value'];
                            if ($status === 'pending') {
                                $q->where('status', 'pending');
                            } elseif ($status === 'approved') {
                                $q->where('status', 'approved');
                            } elseif ($status === 'rejected') {
                                $q->where('status', 'rejected');
                            } elseif ($status === 'dikembalikan') {
                                $q->where('status', 'dikembalikan');
                            }
                        });
                    })
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'dikembalikan' => 'Dikembalikan',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('kembalikan')
                    ->label('Kembalikan Semua')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian Barang')
                    ->modalDescription(fn ($record) => 'Apakah Anda yakin ingin mengembalikan semua barang tidak habis pakai? Stok akan ditambahkan kembali dan jam kembali akan otomatis terisi untuk setiap item.')
                    ->action(function ($record) {
                        if ($record->status_keseluruhan !== 'approved') {
                            throw new \Exception('Hanya peminjaman yang sudah disetujui yang bisa dikembalikan.');
                        }

                        // Update status semua items tidak habis pakai yang masih approved
                        $itemsToReturn = $record->items()
                            ->where('status', 'approved')
                            ->where('jenis_barang', 'tidak_habis_pakai')
                            ->get();

                        if ($itemsToReturn->isEmpty()) {
                            throw new \Exception('Tidak ada barang tidak habis pakai yang perlu dikembalikan.');
                        }

                        foreach ($itemsToReturn as $item) {
                            $item->update([
                                'status' => 'dikembalikan',
                                'jam_kembali' => now()->format('H:i:s'),
                            ]);
                        }
                    })
                    ->visible(fn ($record) => $record->status_keseluruhan === 'approved' && $record->items()->where('status', 'approved')->where('jenis_barang', 'tidak_habis_pakai')->exists()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['Kepala', 'Admin Peminjaman Barang']);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePeminjamanMahasiswas::route('/'),
        ];
    }
}
