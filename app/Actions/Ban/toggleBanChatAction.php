<?php

namespace App\Actions\Ban;

use App\Models\BanChat;

class toggleBanChatAction
{
    public function __invoke(int $userId, bool $hasBanChat): void
    {
        $banChat = BanChat::where('user_id', '=', $userId)->first();
        if ((bool) $hasBanChat === true && is_null($banChat)) {
            BanChat::create(['user_id' => $userId]);
        } elseif ((bool) $hasBanChat === false && ! is_null($banChat)) {
            $banChat->delete();
        }
    }
}
