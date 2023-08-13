<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Post\createPostWithPreviewAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function telegramCallback(Request $request)
    {
        $event = $request->all();
        Log::info($event);

        if (
            isset($event['message']) 
            && is_array($event['message'])
            && isset($event['message']['chat'])
            && is_array($event['message']['chat'])
            && isset($event['message']['chat']['id'])
            && is_array($event['message']['chat']['id'])
            && $event['message']['chat']['id'] == config('telegram.admin_id')
            && isset($event['text']) 
            && $event['text'] == 'create-random-post'
        ) {
            //создать рандомный пост
            (new createPostWithPreviewAction)();
        }
    }
}
