<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use App\Filament\Resources\UserResource\Pages;
// use Filament\Pages\Page;
// use App\Models\BarangMasuk;
// use Barryvdh\DomPDF\Facade\Pdf;
// use Filament\Notifications\Notification;
use Filament\Facades\Filament;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen Akun';
    protected static ?string $navigationLabel = 'Data Pengguna';
     protected static ?int $navigationSort = 10;

    // ✅ PERBAIKI: gunakan Filament\Forms\Form
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_user')
                    ->label('Nama Lengkap')
                    ->required(),

                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                Forms\Components\TextInput::make('no_telp')
                    ->label('No. Telepon')
                    ->tel(),

                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'Kepala' => 'Kepala',
                        'Admin Persediaan Barang' => 'Admin Persediaan Barang',
                        'Admin Peminjaman Barang' => 'Admin Peminjaman Barang',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }

    // ✅ PERBAIKI: gunakan Filament\Tables\Table
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_user')->label('Nama'),
                Tables\Columns\TextColumn::make('username')->label('Username'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('role')->label('Role'),
                Tables\Columns\TextColumn::make('no_telp')->label('No. Telepon'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user->hasAnyRole(['Kepala']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
