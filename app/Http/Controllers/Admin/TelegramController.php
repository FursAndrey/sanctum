<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Comment\createRandomCommentAction;
use App\Actions\Post\createPostWithPreviewAction;
use App\Actions\Telegram\removeMenuAndItsCallActions;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TelegramController extends Controller
{
    public function telegramCallback(Request $request)
    {
        $event = $request->all();
        $adminTgNames = User::with(['roles'])->whereRelation('roles', 'title', '=', 'Admin')->get('tg_name')->map->tg_name->toArray();

        // Log::info($event);

        if (
            isset($event['callback_query'])
            && is_array($event['callback_query'])
        ) {
            $this->validate($request, [
                'callback_query' => [
                    'required',
                    'array',
                ],
                'callback_query.message' => [
                    'required',
                    'array',
                ],
                'callback_query.message.message_id' => [
                    'required',
                    'integer',
                ],
                'callback_query.message.chat' => [
                    'required',
                    'array',
                ],
                'callback_query.message.chat.id' => [
                    'required',
                    'integer',
                ],
                'callback_query.message.chat.username' => [
                    'required',
                    'string',
                    Rule::in($adminTgNames),
                ],
                'callback_query.data' => [
                    'required',
                    'string',
                ],
            ]);

            if (strripos($event['callback_query']['data'], 'create-random-post') !== false) {
                //создать рандомный пост
                (new createPostWithPreviewAction())();
                (new removeMenuAndItsCallActions())($event);
            } elseif (strripos($event['callback_query']['data'], 'create-random-comment') !== false) {
                //создать рандомный комментарий
                (new createRandomCommentAction())();
                (new removeMenuAndItsCallActions())($event);
            } elseif (strripos($event['callback_query']['data'], 'cancel') !== false) {
                //выйти из меню (без изменений)
                (new removeMenuAndItsCallActions())($event);
            }
        } else {
            $this->validate($request, [
                'message' => [
                    'required',
                    'array',
                ],
                'message.message_id' => [
                    'required',
                    'integer',
                ],
                'message.chat' => [
                    'required',
                    'array',
                ],
                'message.chat.id' => [
                    'required',
                    'integer',
                ],
                'message.chat.username' => [
                    'required',
                    'string',
                    Rule::in($adminTgNames),
                ],
                'message.text' => [
                    'required',
                    'string',
                ],
            ]);

            if ($event['message']['text'] == 'admin-control') {
                $message_id = $event['message']['message_id'].'-';
                //отправить кнопки управления
                $keyboard = [
                    [
                        ['text' => 'Добавить 1 случайный пост', 'callback_data' => $message_id.'create-random-post'],
                        ['text' => 'Добавить 1 случайный комментарий', 'callback_data' => $message_id.'create-random-comment'],
                    ],
                    [
                        ['text' => 'Отмена', 'callback_data' => $message_id.'cancel'],
                    ],
                ];
                $message = 'Доступ разрешен.';
                $admin_id = $event['message']['chat']['id'];

                $telegramService = new TelegramService();
                $telegramService->sendMessage($admin_id, $message, $keyboard);
            }
        }
    }
}
