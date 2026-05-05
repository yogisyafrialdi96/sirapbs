<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use SensitiveParameter;

class Login extends BaseLogin
{
    /**
     * Override field email bawaan menjadi NIP / Email.
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('NIP / Email')
            ->placeholder('Masukkan NIP atau Email')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * Tentukan credentials: jika input mengandung '@' pakai email, selain itu pakai NIP.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        $login = $data['email'];

        $field = str_contains($login, '@') ? 'email' : 'nip';

        return [
            $field     => $login,
            'password' => $data['password'],
        ];
    }
}
