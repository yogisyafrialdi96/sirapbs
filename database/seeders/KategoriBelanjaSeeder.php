<?php

namespace Database\Seeders;

use App\Models\KategoriBelanjas;
use Illuminate\Database\Seeder;

class KategoriBelanjaSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['kode' => 'ATK',       'nama' => 'Alat Tulis Kantor (ATK)',              'deskripsi' => 'Pena, kertas, tinta printer, map, dan perlengkapan tulis lainnya'],
            ['kode' => 'IT',        'nama' => 'Perlengkapan IT & Komputer',           'deskripsi' => 'Komputer, laptop, printer, aksesoris, dan perangkat IT lainnya'],
            ['kode' => 'FURN',      'nama' => 'Furnitur & Peralatan Kantor',          'deskripsi' => 'Meja, kursi, lemari, dan perabot kantor lainnya'],
            ['kode' => 'DIDIK',     'nama' => 'Peralatan Pembelajaran',               'deskripsi' => 'Proyektor, papan tulis, alat peraga, dan media pembelajaran'],
            ['kode' => 'BUKU',      'nama' => 'Buku & Referensi Pendidikan',          'deskripsi' => 'Buku teks, buku referensi, modul, dan materi ajar'],
            ['kode' => 'SERAGAM',   'nama' => 'Seragam & Pakaian Dinas',              'deskripsi' => 'Seragam siswa, seragam guru, dan pakaian dinas karyawan'],
            ['kode' => 'KONSUMSI',  'nama' => 'Konsumsi & Katering',                  'deskripsi' => 'Makanan dan minuman untuk rapat, kegiatan, dan snack kantor'],
            ['kode' => 'GEDUNG',    'nama' => 'Pemeliharaan Gedung & Bangunan',       'deskripsi' => 'Pengecatan, perbaikan, dan pemeliharaan fisik gedung sekolah'],
            ['kode' => 'KEND',      'nama' => 'Pemeliharaan Kendaraan',               'deskripsi' => 'Service, suku cadang, dan perawatan kendaraan operasional'],
            ['kode' => 'UTILITAS',  'nama' => 'Utilitas (Listrik, Air, Gas)',         'deskripsi' => 'Tagihan listrik, air bersih, dan gas'],
            ['kode' => 'TELKOM',    'nama' => 'Internet & Telekomunikasi',            'deskripsi' => 'Langganan internet, pulsa, dan biaya telepon'],
            ['kode' => 'HONOR',     'nama' => 'Honorarium & Insentif',                'deskripsi' => 'Honor narasumber, guru ekskul, dan insentif kegiatan'],
            ['kode' => 'PERDIN',    'nama' => 'Perjalanan Dinas',                     'deskripsi' => 'Transportasi, akomodasi, dan uang harian perjalanan dinas'],
            ['kode' => 'DIKLAT',    'nama' => 'Pelatihan & Pengembangan SDM',         'deskripsi' => 'Workshop, seminar, pelatihan, dan sertifikasi guru/karyawan'],
            ['kode' => 'EKSKUL',    'nama' => 'Kegiatan Kesiswaan & Ekstrakurikuler', 'deskripsi' => 'Perlengkapan dan biaya operasional kegiatan ekstrakurikuler'],
            ['kode' => 'OLAH-SEN',  'nama' => 'Sarana Olahraga & Kesenian',           'deskripsi' => 'Peralatan olahraga, alat musik, dan perlengkapan seni'],
            ['kode' => 'BERSIH',    'nama' => 'Peralatan Kebersihan & Sanitasi',      'deskripsi' => 'Alat kebersihan, sabun, disinfektan, dan kebutuhan sanitasi'],
            ['kode' => 'SEHAT',     'nama' => 'Obat-obatan & Kesehatan',              'deskripsi' => 'Obat P3K, vitamin, dan perlengkapan UKS'],
            ['kode' => 'KEAMANAN',  'nama' => 'Perlengkapan Keamanan',                'deskripsi' => 'CCTV, kunci, alarm, dan peralatan keamanan lainnya'],
            ['kode' => 'ADMIN',     'nama' => 'Administrasi & Percetakan',            'deskripsi' => 'Fotokopi, jilid, cetak sertifikat, dan biaya administrasi lainnya'],
        ];

        foreach ($kategoris as $data) {
            KategoriBelanjas::firstOrCreate(
                ['kode' => $data['kode']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
