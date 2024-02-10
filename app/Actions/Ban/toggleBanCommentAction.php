<?php

namespace App\Actions\Ban;

use App\Models\BanComment;

class toggleBanCommentAction
{
    public function __invoke(int $userId, bool $hasBanComment): void
    {
        $banComment = BanComment::where('user_id', '=', $userId)->first();
        if ((bool) $hasBanComment === true && is_null($banComment)) {
            BanComment::create(['user_id' => $userId]);
        } elseif ((bool) $hasBanComment === false && ! is_null($banComment)) {
            $banComment->delete();
        }
    }
}
