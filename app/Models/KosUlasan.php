<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KosUlasan extends Model
{
    use HasFactory;

    public $table = 'kos_ulasan';

    protected $guarded = [];

    public function kos() {
        return $this->belongsTo(Kos::class, 'id_kos', 'id');
    }

    public function pemberi_ulasan() {
        return $this->belongsTo(User::class, 'id_pemberi_ulasan', 'id');
    }

    public function balasan() {
        return $this->hasMany(KosUlasan::class, 'id_balasan', 'id');
    }
}
