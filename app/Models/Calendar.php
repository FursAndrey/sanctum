<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    protected $table = 'calendars';
    
    public function calendarDays(): HasMany
    {
        return $this->hasMany(CalendarDay::class);
    }
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
