<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preview extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'post_id',
        'user_id',
    ];

    public function getUrlAttribute(): string
    {
        return url('storage/' . $this->path);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

}
