<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    protected $fillable = ['departemen_id', 'kode', 'nama', 'keterangan', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
