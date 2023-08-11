<?php
namespace App\Actions\Telegram;

use App\Services\TelegramService;

class sendTelegramTextAction
{
    public function __invoke(string $message): void
    {
        $administartion_group_id = config('telegram.administartion_group_id');
        $telegramService = new TelegramService();
        $telegramService->sendMessage($administartion_group_id, $message);
    }
}