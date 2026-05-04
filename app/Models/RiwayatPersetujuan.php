<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPersetujuan extends Model
{
    protected $fillable = [
        'pengajuan_rapbs_id',
        'user_id',
        'status',
        'catatan',
    ];

    public function pengajuanRapbs()
    {
        return $this->belongsTo(PengajuanRapbs::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
