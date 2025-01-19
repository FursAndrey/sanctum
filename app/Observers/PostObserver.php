<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $hostname = config('app.url');
        $id = $post->id;
        $finalUrl = "$hostname/post/$id";
        // Log::info($post);
        $message = "<b>Опубликован новый пост.</b>\n<a href='$finalUrl'>$finalUrl</a>";

        $administartion_group_id = config('telegram.administartion_group_id');
        $telegramService = new TelegramService;
        $telegramService->sendMessage($administartion_group_id, $message);
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
