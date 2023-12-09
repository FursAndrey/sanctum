<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\MessageUser;
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
            MessageUser::create([
                'user_id' => $user_id,
                'chat_id' => $this->message->chat_id,
                'message_id' => $this->message->id,
            ]);

            //будет отправлен пользователям через сокет
            $countMessages = MessageUser::where('chat_id', '=', $this->message->chat_id)
                ->where('user_id', '=', $user_id)
                ->where('is_read', false)
                ->count();
        }
    }
}
