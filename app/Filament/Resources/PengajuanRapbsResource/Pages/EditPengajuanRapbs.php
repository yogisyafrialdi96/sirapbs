<?php

namespace App\Filament\Resources\PengajuanRapbsResource\Pages;

use App\Filament\Resources\PengajuanRapbsResource;
use App\Models\PengajuanRapbs;
use App\Models\RiwayatPersetujuan;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanRapbs extends EditRecord
{
    protected static string $resource = PengajuanRapbsResource::class;

    protected function getHeaderActions(): array
    {
        /** @var PengajuanRapbs $record */
        $record = $this->getRecord();

        return [
            Actions\Action::make('ajukan')
                ->label('Ajukan')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Ajukan RAPBS')
                ->modalDescription('Pastikan semua item kebutuhan sudah dimasukkan.')
                ->visible(fn () => $this->getRecord()->status === 'draft')
                ->action(function (): void {
                    $record = $this->getRecord();
                    if ($record->items()->doesntExist()) {
                        Notification::make()->danger()
                            ->title('Belum ada item')
                            ->body('Tambahkan minimal satu item kebutuhan sebelum mengajukan.')
                            ->send();
                        return;
                    }
                    // Reset status review item
                    $record->items()->update(['status' => 'pending', 'catatan_reviewer' => null]);
                    $record->update(['status' => 'diajukan', 'tanggal_pengajuan' => now()]);
                    RiwayatPersetujuan::create([
                        'pengajuan_rapbs_id' => $record->id,
                        'user_id'            => auth()->id(),
                        'status'             => 'diajukan',
                    ]);
                    $this->refreshFormData(['status', 'tanggal_pengajuan']);
                    Notification::make()->success()->title('Pengajuan berhasil dikirim.')->send();
                }),

            Actions\Action::make('setujui')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->schema([
                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Persetujuan')->rows(3)->nullable(),
                ])
                ->requiresConfirmation()
                ->modalHeading('Setujui Pengajuan')
                ->visible(fn () => $this->getRecord()->status === 'diajukan' && auth()->user()?->isAdmin())
                ->action(function (array $data): void {
                    $record = $this->getRecord();
                    $record->update(['status' => 'disetujui', 'catatan' => $data['catatan'] ?? null]);
                    RiwayatPersetujuan::create([
                        'pengajuan_rapbs_id' => $record->id,
                        'user_id'            => auth()->id(),
                        'status'             => 'disetujui',
                        'catatan'            => $data['catatan'] ?? null,
                    ]);
                    $this->refreshFormData(['status', 'catatan']);
                    Notification::make()->success()->title('Pengajuan disetujui.')->send();
                }),

            Actions\Action::make('revisi')
                ->label('Minta Revisi')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->schema([
                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Revisi')->required()->rows(3),
                ])
                ->modalHeading('Minta Revisi')
                ->visible(fn () => $this->getRecord()->status === 'diajukan' && auth()->user()?->isAdmin())
                ->action(function (array $data): void {
                    $record = $this->getRecord();
                    $record->update(['status' => 'direvisi', 'catatan' => $data['catatan']]);
                    RiwayatPersetujuan::create([
                        'pengajuan_rapbs_id' => $record->id,
                        'user_id'            => auth()->id(),
                        'status'             => 'direvisi',
                        'catatan'            => $data['catatan'],
                    ]);
                    $this->refreshFormData(['status', 'catatan']);
                    Notification::make()->warning()->title('Pengajuan dikembalikan untuk direvisi.')->send();
                }),

            Actions\Action::make('tolak')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->schema([
                    Forms\Components\Textarea::make('catatan')
                        ->label('Alasan Penolakan')->required()->rows(3),
                ])
                ->modalHeading('Tolak Pengajuan')
                ->visible(fn () => $this->getRecord()->status === 'diajukan' && auth()->user()?->isAdmin())
                ->action(function (array $data): void {
                    $record = $this->getRecord();
                    $record->update(['status' => 'ditolak', 'catatan' => $data['catatan']]);
                    RiwayatPersetujuan::create([
                        'pengajuan_rapbs_id' => $record->id,
                        'user_id'            => auth()->id(),
                        'status'             => 'ditolak',
                        'catatan'            => $data['catatan'],
                    ]);
                    $this->refreshFormData(['status', 'catatan']);
                    Notification::make()->danger()->title('Pengajuan ditolak.')->send();
                }),

            Actions\Action::make('ajukan_ulang')
                ->label('Ajukan Ulang')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Ajukan Ulang RAPBS')
                ->visible(fn () => $this->getRecord()->status === 'direvisi')
                ->action(function (): void {
                    $record = $this->getRecord();
                    // Reset status review semua item
                    $record->items()->update(['status' => 'pending', 'catatan_reviewer' => null]);
                    $record->update(['status' => 'diajukan', 'tanggal_pengajuan' => now()]);
                    RiwayatPersetujuan::create([
                        'pengajuan_rapbs_id' => $record->id,
                        'user_id'            => auth()->id(),
                        'status'             => 'diajukan',
                        'catatan'            => 'Diajukan ulang setelah revisi.',
                    ]);
                    $this->refreshFormData(['status', 'tanggal_pengajuan']);
                    Notification::make()->success()->title('Pengajuan berhasil dikirim ulang.')->send();
                }),

            Actions\DeleteAction::make()
                ->visible(fn () => in_array($this->getRecord()->status, ['draft', 'ditolak'])),
        ];
    }
}
