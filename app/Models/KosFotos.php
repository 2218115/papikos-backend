<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KosFotos extends Model
{
    use HasFactory;
    
    public $table = 'kos_fotos';

    protected $guarded = [];
}
