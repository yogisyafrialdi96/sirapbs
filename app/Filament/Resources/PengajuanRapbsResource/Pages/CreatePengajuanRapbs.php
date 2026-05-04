<?php

namespace App\Filament\Resources\PengajuanRapbsResource\Pages;

use App\Filament\Resources\PengajuanRapbsResource;
use App\Models\PengajuanRapbs;
use App\Models\TahunAjaran;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePengajuanRapbs extends CreateRecord
{
    protected static string $resource = PengajuanRapbsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Validate: one submission per user per tahun ajaran
        $exists = PengajuanRapbs::where('user_id', $data['user_id'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->exists();

        if ($exists) {
            Notification::make()
                ->danger()
                ->title('Duplikasi Pengajuan')
                ->body('Pegawai ini sudah memiliki pengajuan untuk tahun ajaran yang dipilih.')
                ->send();

            $this->halt();
        }

        return DB::transaction(function () use ($data): PengajuanRapbs {
            $tahunAjaran = TahunAjaran::findOrFail($data['tahun_ajaran_id']);
            $namaSlug    = str_replace('/', '-', $tahunAjaran->nama);

            // Safe sequential code generation with pessimistic lock
            $lastCode = PengajuanRapbs::where('kode_pengajuan', 'like', "RAPBS-{$namaSlug}-%")
                ->lockForUpdate()
                ->orderByDesc('kode_pengajuan')
                ->value('kode_pengajuan');

            $sequence = 1;
            if ($lastCode) {
                $parts    = explode('-', $lastCode);
                $sequence = ((int) end($parts)) + 1;
            }

            $data['kode_pengajuan'] = sprintf('RAPBS-%s-%04d', $namaSlug, $sequence);
            $data['status']         = 'draft';

            return PengajuanRapbs::create($data);
        });
    }
}
