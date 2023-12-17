<?php

namespace App\Events;

use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\OtherUsersMessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendUnreadableMessagesCountEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $countMessages;
    private int $chatId;
    private int $userId;
    private Message $message;
    /**
     * Create a new event instance.
     */
    public function __construct(int $countMessages, int $chatId, int $userId, Message $message)
    {
        $this->countMessages = $countMessages;
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-channel-' . $this->userId),
        ];
    }
    
    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message-status';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'countMessages' => $this->countMessages,
            'chatId' => $this->chatId,
            'message' => MessageResource::make($this->message)->resolve(),
        ];
    }
}
