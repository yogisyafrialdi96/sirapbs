<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // HR-GS
            ['dept' => 'HR-GS',      'kode' => 'HR',          'nama' => 'HR',           'keterangan' => 'Human Resources'],
            ['dept' => 'HR-GS',      'kode' => 'GS',          'nama' => 'GS',           'keterangan' => 'General Services'],
            // MUAMALAT
            ['dept' => 'MUAMALAT',   'kode' => 'KEUANGAN',    'nama' => 'KEUANGAN',     'keterangan' => 'Unit Keuangan'],
            // PENDIDIKAN
            ['dept' => 'PENDIDIKAN', 'kode' => 'TKIT',        'nama' => 'TKIT',         'keterangan' => 'Taman Kanak-Kanak Islam Terpadu'],
            ['dept' => 'PENDIDIKAN', 'kode' => 'SDIT',        'nama' => 'SDIT',         'keterangan' => 'Sekolah Dasar Islam Terpadu'],
            ['dept' => 'PENDIDIKAN', 'kode' => 'SMPIT',       'nama' => 'SMPIT',        'keterangan' => 'SMP Islam Terpadu'],
            ['dept' => 'PENDIDIKAN', 'kode' => 'MTS',         'nama' => 'MTS',          'keterangan' => 'Madrasah Tsanawiyah'],
            ['dept' => 'PENDIDIKAN', 'kode' => 'SMAIT',       'nama' => 'SMAIT',        'keterangan' => 'SMA Islam Terpadu'],
            ['dept' => 'PENDIDIKAN', 'kode' => 'DIREKTORAT',  'nama' => 'DIREKTORAT',   'keterangan' => 'Direktorat Pendidikan'],
        ];

        foreach ($units as $data) {
            $departemen = Departemen::where('kode', $data['dept'])->first();
            if (! $departemen) {
                continue;
            }

            UnitKerja::firstOrCreate(
                ['kode' => $data['kode']],
                [
                    'departemen_id' => $departemen->id,
                    'nama'          => $data['nama'],
                    'keterangan'    => $data['keterangan'],
                    'is_active'     => true,
                ]
            );
        }
    }
}
