<?php

namespace App\Observers;

use App\Actions\Telegram\sendTelegramTextAction;
use App\Models\Post;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $hostname = env("APP_URL", "http://byfirst.xyz");
        $id = $post->id;
        $message = "<a href='$hostname/post/$id'>Добавлен новый пост.</a>";
        
        (new sendTelegramTextAction)($message);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "restored" event.
     */
    public function restored(Post $post): void
    {
        //
    }

    /**
     * Handle the Post "force deleted" event.
     */
    public function forceDeleted(Post $post): void
    {
        //
    }
}
