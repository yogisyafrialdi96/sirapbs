<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static \UnitEnum|string|null $navigationGroup = 'Manajemen Pengguna';
    protected static ?string $navigationLabel = 'Pegawai';
    protected static ?string $modelLabel = 'Pegawai';
    protected static ?string $pluralModelLabel = 'Pegawai';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Akun')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap'),
                Forms\Components\TextInput::make('nip')
                    ->maxLength(30)
                    ->unique(ignoreRecord: true)
                    ->nullable()
                    ->label('NIP'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Email'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->label('Password')
                    ->helperText('Kosongkan jika tidak ingin mengubah password.'),
            ])->columns(2),

            Section::make('Data Kepegawaian')->schema([
                Forms\Components\Select::make('jabatan_id')
                    ->label('Jabatan')
                    ->options(fn () => Jabatan::orderBy('nama')->pluck('nama', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('unit_kerja_id')
                    ->label('Unit Kerja')
                    ->options(fn () => UnitKerja::with('departemen')
                        ->where('is_active', true)
                        ->orderBy('nama')
                        ->get()
                        ->mapWithKeys(fn ($u) => [$u->id => "{$u->departemen->nama} — {$u->nama}"])
                    )
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin'   => 'Admin',
                        'pegawai' => 'Pegawai',
                    ])
                    ->required()
                    ->default('pegawai'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->searchable()
                    ->sortable()
                    ->label('NIP'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\TextColumn::make('jabatan.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Jabatan'),
                Tables\Columns\TextColumn::make('unitKerja.nama')
                    ->searchable()
                    ->sortable()
                    ->label('Unit Kerja'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'danger'  => 'admin',
                        'primary' => 'pegawai',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin'   => 'Admin',
                        'pegawai' => 'Pegawai',
                        default   => $state,
                    })
                    ->label('Role'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jabatan_id')
                    ->label('Jabatan')
                    ->relationship('jabatan', 'nama'),
                Tables\Filters\SelectFilter::make('unit_kerja_id')
                    ->label('Unit Kerja')
                    ->relationship('unitKerja', 'nama'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin'   => 'Admin',
                        'pegawai' => 'Pegawai',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->id === auth()->id()),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['jabatan', 'unitKerja']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
