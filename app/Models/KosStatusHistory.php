<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KosStatusHistory extends Model
{
    use HasFactory;

    public $table = 'kos_status_history';

    protected $guarded = [];
}