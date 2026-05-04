<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanRapbs extends Model
{
    protected $table = 'pengajuan_rapbs';

    protected $fillable = [
        'kode_pengajuan',
        'user_id',
        'tahun_ajaran_id',
        'status',
        'catatan',
        'tanggal_pengajuan',
    ];

    protected function casts(): array
    {
        return ['tanggal_pengajuan' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function items()
    {
        return $this->hasMany(ItemPengajuan::class);
    }

    public function riwayatPersetujuans()
    {
        return $this->hasMany(RiwayatPersetujuan::class);
    }

    public function getTotalAnggaranAttribute(): float
    {
        return $this->items->sum('total_harga');
    }
}
