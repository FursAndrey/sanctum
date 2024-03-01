<?php

namespace App\Jobs;

use App\Actions\checkEnvIsProdAction;
use App\Events\SendUnreadableMessagesCountEvent;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\PrivateTelegram;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreMessageStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Message $message;

    private array $chatIds;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message, string $chatIds)
    {
        $this->message = $message;

        $chatIdsArray = explode('-', $chatIds);
        $this->chatIds = $chatIdsArray;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->chatIds as $key => $user_id) {
            $messageUser = [
                'user_id' => $user_id,
                'chat_id' => $this->message->chat_id,
                'message_id' => $this->message->id,
            ];

            //если сообщение моё - оно прочитано
            if ((int) auth()->user()->id === (int) $user_id) {
                $messageUser['is_read'] = true;
            }

            MessageUser::create($messageUser);

            //отправляем пользователям через сокет
            $countMessages = MessageUser::where('chat_id', '=', $this->message->chat_id)
                ->where('user_id', '=', $user_id)
                ->where('is_read', false)
                ->count();
            broadcast(new SendUnreadableMessagesCountEvent($countMessages, $this->message->chat_id, $user_id, $this->message))->toOthers();

            if ((new checkEnvIsProdAction)(config('app.env'))) {
                $telegramChatId = PrivateTelegram::where('user_id', '=', $user_id)->get('private_chat_id')->first()?->private_chat_id;

                if (is_null($telegramChatId)) {
                    continue;
                }

                $message = 'В чате byfirst.xyz вам отправлено следующее сообщение: '. $this->message->body;
                $telegramService = new TelegramService();
                $telegramService->sendMessage($telegramChatId, $message);
            }
        }
    }
}
