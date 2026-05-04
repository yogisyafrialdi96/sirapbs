<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['kode' => 'MGR-HR',       'nama' => 'Manager HR-GS'],
            ['kode' => 'KOOR-KEU',     'nama' => 'Koor. Keuangan'],
            ['kode' => 'KOOR-GS',      'nama' => 'Koor. General Services'],
            ['kode' => 'STAF-ITA',     'nama' => 'Staff IT Application'],
            ['kode' => 'STAF-ITH',     'nama' => 'Staff IT Hardware'],
            ['kode' => 'STAF-PROC',    'nama' => 'Staff Procurement'],
            ['kode' => 'STAF-MAINT',   'nama' => 'Staff Maintenance'],
            ['kode' => 'STAF-KEU',     'nama' => 'Staff Keuangan'],
            ['kode' => 'STAF-HR-REK',  'nama' => 'Staff HR sp. Rekrutmen'],
            ['kode' => 'STAF-HR-PAT',  'nama' => 'Staff HR sp. Kepatuhan'],
            ['kode' => 'STAF-HR-ADM',  'nama' => 'Staff HR sp. Adm. HR'],
            ['kode' => 'DRIVER',       'nama' => 'Driver'],
            ['kode' => 'STAF-EKS',     'nama' => 'Staff Ekspedisi'],
            ['kode' => 'STAF-PAJ',     'nama' => 'Staff Pajak'],
            ['kode' => 'STAF-PEME',    'nama' => 'Staff Pemeliharaan'],
            ['kode' => 'STAF-KEB',     'nama' => 'Staff Kebersihan'],
        ];

        foreach ($jabatans as $data) {
            Jabatan::firstOrCreate(['kode' => $data['kode']], $data);
        }
    }
}
