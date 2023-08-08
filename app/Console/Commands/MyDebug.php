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
        // $str = '{"ok":true,"result":[{"update_id":265854950,
        //     "message":{"message_id":2,"from":{"id":2065261178,"is_bot":false,"first_name":"\u0410\u043d\u0434\u0440\u0435\u0439","username":"Mittrawnuru0d0","language_code":"ru"},"chat":{"id":2065261178,"first_name":"\u0410\u043d\u0434\u0440\u0435\u0439","username":"Mittrawnuru0d0","type":"private"},"date":1691426945,"text":"hello bot"}},{"update_id":265854951,
        //     "my_chat_member":{"chat":{"id":-940469459,"title":"byfirst_group","type":"group","all_members_are_administrators":false},"from":{"id":2065261178,"is_bot":false,"first_name":"\u0410\u043d\u0434\u0440\u0435\u0439","username":"Mittrawnuru0d0","language_code":"ru"},"date":1691431096,"old_chat_member":{"user":{"id":6110916071,"is_bot":true,"first_name":"byfirst","username":"byfirst_bot"},"status":"left"},"new_chat_member":{"user":{"id":6110916071,"is_bot":true,"first_name":"byfirst","username":"byfirst_bot"},"status":"member"}}},{"update_id":265854952,
        //     "message":{"message_id":4,"from":{"id":2065261178,"is_bot":false,"first_name":"\u0410\u043d\u0434\u0440\u0435\u0439","username":"Mittrawnuru0d0","language_code":"ru"},"chat":{"id":-940469459,"title":"byfirst_group","type":"group","all_members_are_administrators":true},"date":1691431096,"new_chat_participant":{"id":6110916071,"is_bot":true,"first_name":"byfirst","username":"byfirst_bot"},"new_chat_member":{"id":6110916071,"is_bot":true,"first_name":"byfirst","username":"byfirst_bot"},"new_chat_members":[{"id":6110916071,"is_bot":true,"first_name":"byfirst","username":"byfirst_bot"}]}}]}';
        // $arr = json_decode($str, true);
        // print_r($arr);
        $administartion_group_id = config('services.telegram.administartion_group_id');
        $admin_id = config('services.telegram.admin_id');
        $message = 'message from command line';
        // echo config('services.telegram.bot_token');
        // echo "\n";
        // echo config('services.telegram.admin_id');
        // echo "\n";
        // echo config('services.telegram.administartion_group_id');
        var_dump($administartion_group_id);
        var_dump($admin_id);
        // $telegramService = new TelegramService();
        // $telegramService->sendMessage($administartion_group_id, $message);
        // $telegramService->sendMessage($admin_id, $message);
    }
}
