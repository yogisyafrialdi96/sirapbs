<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $fillable = ['kode', 'nama', 'keterangan'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
