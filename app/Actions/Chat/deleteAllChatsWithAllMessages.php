<?php

namespace App\Actions\Chat;

use App\Models\User;

class deleteAllChatsWithAllMessages
{
    public function __invoke(User $user): void
    {
        foreach ($user->chats as $chat) {
            (new deleteOneChatWithAllMessages())($chat);
        }
    }
}
