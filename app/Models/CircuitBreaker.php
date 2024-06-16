<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CircuitBreaker extends Model
{
    use HasFactory;

    protected $table = 'breakers';

    protected $fillable = [
        'nominal',
    ];
}
