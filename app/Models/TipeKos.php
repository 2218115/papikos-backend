<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipeKos extends Model
{
    use HasFactory;
    
    public $table = 'tipe_kos';

    protected $guarded = [];
}
