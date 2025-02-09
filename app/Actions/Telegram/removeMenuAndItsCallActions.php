<?php

namespace App\Actions\Telegram;

use App\Services\TelegramService;

class removeMenuAndItsCallActions
{
    public function __invoke(array $event): void
    {
        $message_id = $event['callback_query']['message']['message_id'];

        $callback_data = explode('-', $event['callback_query']['data']);
        $id = $callback_data[0];
        $admin_id = $event['callback_query']['message']['chat']['id'];

        $telegramService = new TelegramService;
        $telegramService->deleteMessage($admin_id, $message_id);
        $telegramService->deleteMessage($admin_id, $id);
    }
}
