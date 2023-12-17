<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('user-channel-{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('store-message-channel-{chat_id}', function ($user, $chat_id) {
    $allowedChats = $user->chats->pluck('id')->toArray();

    return in_array($chat_id, $allowedChats);
});
