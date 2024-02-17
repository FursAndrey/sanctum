<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BanChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    protected $table = 'ban_chats';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
