<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    use HasFactory;

    public $table = 'kos';

    protected $guarded = [];

    public function tipe_kos()
    {
        return $this->belongsTo(TipeKos::class, 'id_tipe_kos', 'id');
    }

    public function fasilitas_kos()
    {
        return $this->hasMany(KosFasilitas::class, 'id_kos', 'id');
    }

    public function peraturan_kos()
    {
        return $this->hasMany(KosPeraturan::class, 'id_kos', 'id');
    }

    public function fotos()
    {
        return $this->hasMany(KosFotos::class, 'id_kos', 'id');
    }

    public function history_status()
    {
        return $this->hasMany(KosStatusHistory::class, 'id_kos', 'id');
    }

    public function ulasan()
    {
        return $this->hasMany(KosUlasan::class, 'id_kos', 'id');
    }

    public function current_status()
    {
        return $this->hasOne(KosStatusHistory::class, 'id_kos', 'id')
            ->latestOfMany();
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'id_pemilik', 'id');
    }
}
