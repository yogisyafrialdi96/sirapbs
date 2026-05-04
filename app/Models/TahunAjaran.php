<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_aktif',
        'dibuka_oleh',
        'dibuka_pada',
        'ditutup_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_aktif' => 'boolean',
            'dibuka_pada' => 'datetime',
            'ditutup_pada' => 'datetime',
        ];
    }

    public function dibukOleh()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

    public function pengajuanRapbs()
    {
        return $this->hasMany(PengajuanRapbs::class);
    }
}
