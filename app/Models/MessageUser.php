<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'user_id',
        'message_id',
        'is_read',
    ];

    protected $table = 'message_users';
}
