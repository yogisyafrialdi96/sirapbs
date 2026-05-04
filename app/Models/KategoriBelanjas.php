<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBelanjas extends Model
{
    protected $table = 'kategori_belanjas';

    protected $fillable = ['kode', 'nama', 'deskripsi', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function itemPengajuans()
    {
        return $this->hasMany(ItemPengajuan::class, 'kategori_belanja_id');
    }
}
