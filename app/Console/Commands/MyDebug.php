<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class MyDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'my:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'My command for debug or develop';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        echo "Hello World!";
        
        $message = 'Доступ разрешен.';
        $keyboard = [
            [
                ['text' => 'Добавить 1 случайный пост', 'callback_data' => 'create-random-post'],
            ],
        ];
        
        $admin_id = config('telegram.admin_id');
        $telegramService = new TelegramService();
        $telegramService->sendMessage($admin_id, $message, $keyboard);
    }
}
