<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Post\createPostWithPreviewAction;
use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function telegramCallback(Request $request)
    {
        $event = $request->all();
        $admin_id = config('telegram.admin_id');
        // Log::info($event);

        if (
            isset($event['message'])
            && is_array($event['message'])
            && isset($event['message']['chat'])
            && is_array($event['message']['chat'])
            && isset($event['message']['chat']['id'])
            && is_integer($event['message']['chat']['id'])
            && $event['message']['chat']['id'] == $admin_id
            && isset($event['message']['text'])
            && $event['message']['text'] == 'admin-control'
            && isset($event['message']['message_id'])
            && is_integer($event['message']['message_id'])
        ) {
            $message_id = $event['message']['message_id'].'-';
            //отправить кнопки управления
            $message = 'Доступ разрешен.';
            $keyboard = [
                [
                    ['text' => 'Добавить 1 случайный пост', 'callback_data' => $message_id.'create-random-post'],
                ],
            ];
            
            $telegramService = new TelegramService();
            $telegramService->sendMessage($admin_id, $message, $keyboard);
        } elseif (
            isset($event['callback_query'])
            && is_array($event['callback_query'])
            && isset($event['callback_query']['message'])
            && is_array($event['callback_query']['message'])
            && isset($event['callback_query']['message']['chat'])
            && is_array($event['callback_query']['message']['chat'])
            && isset($event['callback_query']['message']['chat']['id'])
            && is_integer($event['callback_query']['message']['chat']['id'])
            && isset($event['callback_query']['message']['message_id'])
            && is_integer($event['callback_query']['message']['message_id'])
            && $event['callback_query']['message']['chat']['id'] == $admin_id
            && isset($event['callback_query']['data'])
            && strripos($event['callback_query']['data'], 'create-random-post') !== false
        ) {
            //создать рандомный пост
            (new createPostWithPreviewAction)();
            $message_id = $event['callback_query']['message']['message_id'];
            
            $callback_data = explode('-', $event['callback_query']['data']);
            $id = $callback_data[0];
            
            $telegramService = new TelegramService();
            $telegramService->deleteMessage($admin_id, $message_id);
            $telegramService->deleteMessage($admin_id, $id);
        }
    }
}
