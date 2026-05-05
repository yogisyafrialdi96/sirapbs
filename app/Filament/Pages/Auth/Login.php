<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use SensitiveParameter;

class Login extends BaseLogin
{
    /**
     * Ganti field email dengan field NIP.
     * Pengguna memasukkan NIP → dicari email-nya → dipakai untuk autentikasi.
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('NIP')
            ->placeholder('Masukkan NIP Anda')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['name' => 'nip']);
    }

    /**
     * Jika input terisi NIP (bukan email), cari email user berdasarkan NIP.
     * Mendukung login dengan NIP maupun email.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        $login = $data['email'] ?? '';

        // Jika bukan format email, anggap sebagai NIP → cari email-nya
        if (! filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('nip', $login)->first();
            $login = $user?->email ?? $login;
        }

        return [
            'email'    => $login,
            'password' => $data['password'],
        ];
    }
}
