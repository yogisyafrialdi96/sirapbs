<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPengajuan extends Model
{
    protected $fillable = [
        'pengajuan_rapbs_id',
        'kategori_belanja_id',
        'nama_item',
        'deskripsi',
        'spesifikasi',
        'satuan',
        'volume',
        'harga_satuan',
        'total_harga',
        'status',
        'catatan_reviewer',
    ];

    protected function casts(): array
    {
        return [
            'volume' => 'decimal:2',
            'harga_satuan' => 'decimal:2',
            'total_harga' => 'decimal:2',
        ];
    }

    public function pengajuanRapbs()
    {
        return $this->belongsTo(PengajuanRapbs::class);
    }

    public function kategoriBelanjas()
    {
        return $this->belongsTo(KategoriBelanjas::class, 'kategori_belanja_id');
    }
}
