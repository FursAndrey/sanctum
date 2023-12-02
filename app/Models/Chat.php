<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'users',
    ];

    protected $table = 'chats';

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user', 'chat_id', 'user_id');
    }

    public function chatWith()
    {
        return $this->hasOneThrough(User::class, ChatUser::class, 'chat_id', 'id', 'id', 'user_id')
            ->where('user_id', '!=', auth()->id());
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }

    public function unreadableMessages(): HasMany
    {
        return $this->hasMany(MessageStatus::class, 'chat_id', 'id')
            ->where('user_id', '=', auth()->id())
            ->where('is_read', false);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'chat_id', 'id')
            ->ofMany('id', 'MAX')
            ->with('user');
    }
}
