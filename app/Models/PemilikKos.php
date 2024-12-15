<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemilikKos extends Model
{
    use HasFactory;

    public $table = 'pemilik_kos';

    protected $guarded = [];
}
