<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        $departemens = [
            ['kode' => 'BKMD',       'nama' => 'BKMD',       'keterangan' => 'Badan Koordinasi dan Manajemen Dakwah'],
            ['kode' => 'HR-GS',      'nama' => 'HR-GS',      'keterangan' => 'Human Resources & General Services'],
            ['kode' => 'MUAMALAT',   'nama' => 'MUAMALAT',   'keterangan' => 'Departemen Muamalat / Keuangan'],
            ['kode' => 'PENDIDIKAN', 'nama' => 'PENDIDIKAN',  'keterangan' => 'Departemen Pendidikan'],
        ];

        foreach ($departemens as $data) {
            Departemen::firstOrCreate(['kode' => $data['kode']], $data);
        }
    }
}
