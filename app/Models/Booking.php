<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $table = 'booking';

    protected $guarded = [];

    public function pemesan()
    {
        return $this->belongsTo(User::class, 'id_pemesan', 'id');
    }

    public function status()
    {
        return $this->belongsTo(BookingStatus::class, 'id_status', 'id');
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class, 'id_kos', 'id');
    }
}
