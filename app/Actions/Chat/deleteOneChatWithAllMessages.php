<?php

namespace App\Actions\Chat;

use App\Models\Chat;
use App\Models\MessageUser;

class deleteOneChatWithAllMessages
{
    public function __invoke(Chat $chat): void
    {
        foreach ($chat->messages as $message) {
            MessageUser::where('message_id', '=', $message->id)->delete();
            $message->delete();
        }
        $chat->users()->detach();
        $chat->delete();
    }
}
