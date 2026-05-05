<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends EditProfile
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function getLabel(): string
    {
        return 'Profil Saya';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Foto Profil')
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Foto Profil')
                            ->image()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('avatars')
                            ->disk('public')
                            ->maxSize(2048)
                            ->helperText('Maks. 2 MB. Format: JPG, PNG, atau WEBP.')
                            ->avatar()
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(table: 'users', column: 'email', ignoreRecord: true),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),

                Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password.')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password Baru')
                            ->password()
                            ->revealable()
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation'),

                        TextInput::make('passwordConfirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password()
                            ->revealable()
                            ->required(fn ($get): bool => filled($get('password')))
                            ->visible(fn ($get): bool => filled($get('password')))
                            ->dehydrated(false)
                            ->autocomplete('new-password'),
                    ])->columns(2),
            ]);
    }

    /**
     * Remove currentPassword requirement (simplified profile).
     * Only dehydrate password when filled.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['currentPassword'], $data['passwordConfirmation']);

        return $data;
    }
}
