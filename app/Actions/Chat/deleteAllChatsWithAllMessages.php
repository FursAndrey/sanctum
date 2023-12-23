<?php

namespace App\Actions\Chat;

use App\Models\MessageUser;
use App\Models\User;

class deleteAllChatsWithAllMessages
{
    public function __invoke(User $user): void
    {
        foreach ($user->chats as $chat) {
            foreach ($chat->messages as $message) {
                MessageUser::where('message_id', '=', $message->id)->delete();
                $message->delete();
            }
            $chat->users()->detach();
            $chat->delete();
        }
    }
}
