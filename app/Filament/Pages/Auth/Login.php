<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Schema;
use SensitiveParameter;

class Login extends BaseLogin
{
    /**
     * Ganti field email dengan field NIP.
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
     * Tambah link tutorial di bawah form login.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                Html::make(
                    '<div style="text-align:center; margin-top:0.5rem;">'
                    . '<a href="' . route('tutorial.pengajuan') . '" target="_blank" '
                    . 'style="font-size:0.85rem; color:#3b82f6; text-decoration:none;">'
                    . '📋 Lihat Tutorial Cara Pengajuan RAPBS'
                    . '</a>'
                    . '</div>'
                ),
            ]);
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
