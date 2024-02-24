<?php

namespace App\Actions\Telegram;

use App\Models\PrivateTelegram;
use App\Models\User;

class updateChatIdAction
{
    public function __invoke(int $chatId, string $userName): void
    {
        $userId = User::where('tg_name', '=', $userName)->get('id')->map->id->first();

        $privateTelegram = PrivateTelegram::where('user_id', '=', $userId)->get()->first();

        if (is_null($privateTelegram)) {
            PrivateTelegram::create(['user_id' => $userId, 'private_chat_id' => $chatId]);
        } elseif ($privateTelegram->private_chat_id != $chatId) {
            $privateTelegram->update(['private_chat_id' => $chatId]);
        }
    }
}
