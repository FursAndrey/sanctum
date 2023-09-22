<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'body',
        'parent_id',
    ];

    public function getPublishedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }
    
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
