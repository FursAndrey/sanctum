<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_id',
        'month_day_id',
        'work_start',
        'work_end',
        'lunch_start',
        'lunch_end',
        'control_start',
        'control_end',
    ];

    protected $table = 'calendar_days';

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class, 'calendar_id', 'id');
    }
}
