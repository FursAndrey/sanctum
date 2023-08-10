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
        $administartion_group_id = config('telegram.administartion_group_id');
        $admin_id = config('telegram.admin_id');
        $bot_token = config('telegram.bot_token');
        var_dump($administartion_group_id);
        var_dump($admin_id);
        var_dump($bot_token);
        
        $message = 'message from command line';
        $telegramService = new TelegramService();
        $telegramService->sendMessage($administartion_group_id, $message);
        $telegramService->sendMessage($admin_id, $message);
    }
}
