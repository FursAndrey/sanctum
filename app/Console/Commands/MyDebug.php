<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
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
        
        $q = User::with(['roles'])->whereRelation('roles', 'title', '=', 'Admin')->get('tg_name')->map->tg_name->toArray();

        dump($q);

        dump(in_array('test', $q));
        dump(in_array('test3', $q));
        // $message = 'Доступ разрешен.';
        // $keyboard = [
        //     [
        //         ['text' => 'Добавить 1 случайный пост', 'callback_data' => 'create-random-post'],
        //     ],
        // ];
        
        // $admin_id = config('telegram.admin_id');
        // $telegramService = new TelegramService();
        // $telegramService->sendMessage($admin_id, $message, $keyboard);
    }
}
