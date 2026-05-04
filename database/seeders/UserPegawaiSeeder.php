<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserPegawaiSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Format: [nama, nip, kode_dept (nama dept), kode_unit, kode_jabatan]
         *
         * Unit ditentukan berdasarkan jabatan:
         *   - Jabatan HR → unit HR (HR-GS dept)
         *   - Jabatan GS/operasional → unit GS (HR-GS dept)
         *   - Jabatan Keuangan/Pajak → unit KEUANGAN (MUAMALAT dept)
         */
        $pegawai = [
            // nama,          nip,       unit_kode,  jabatan_kode
            ['Dewinta',       '215841',  'HR',        'MGR-HR'],
            ['Betha',         '211881',  'HR',        'STAF-HR-PAT'],
            ['Murni',         '219964',  'HR',        'STAF-HR-REK'],
            ['Yogi',          '219968',  'HR',        'STAF-ITA'],
            ['Imam',          '205701',  'GS',        'STAF-PEME'],
            ['Afnir',         '219821',  'GS',        'KOOR-GS'],
            ['Wandres',       '220871',  'GS',        'STAF-MAINT'],
            ['Siswandoyo',    '209842',  'GS',        'STAF-ITH'],
            ['Maisyarah',     '209861',  'KEUANGAN',  'KOOR-KEU'],
            ['Nanada',        '220972',  'KEUANGAN',  'STAF-KEU'],
            ['Hambali',       '222971',  'KEUANGAN',  'STAF-PAJ'],
            ['Donni',         '924031',  'GS',        'STAF-EKS'],
            ['Roum',          '925921',  'HR',        'STAF-HR-ADM'],
            ['Roby',          '925952',  'GS',        'DRIVER'],
            ['Suci',          '925972',  'KEUANGAN',  'STAF-KEU'],
            ['Sondi',         '922841',  'GS',        'STAF-PROC'],
            ['Ichsan',        '922932',  'GS',        'STAF-PROC'],
            ['Rohiman',       '205721',  'GS',        'STAF-KEB'],
        ];

        foreach ($pegawai as [$nama, $nip, $unitKode, $jabatanKode]) {
            $unitKerja = UnitKerja::where('kode', $unitKode)->first();
            $jabatan   = Jabatan::where('kode', $jabatanKode)->first();

            User::firstOrCreate(
                ['nip' => $nip],
                [
                    'name'          => $nama,
                    'email'         => $nip . '@ykpi.or.id',
                    'password'      => bcrypt($nip),
                    'unit_kerja_id' => $unitKerja?->id,
                    'jabatan_id'    => $jabatan?->id,
                    'role'          => 'pegawai',
                    'is_active'     => true,
                ]
            );
        }
    }
}
