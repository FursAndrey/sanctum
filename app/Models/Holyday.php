<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holyday extends Model
{
    use HasFactory;

    protected $fillable = [
        'holyday',
    ];

    protected $table = 'holydays';
}
