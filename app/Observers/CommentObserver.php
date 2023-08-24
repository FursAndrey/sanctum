<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\TelegramService;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        $hostname = config('app.url');
        $id = $comment->post_id;
        $finalUrl = "$hostname/post/$id";
        // Log::info($post);
        $message = "К посту <b>опубликован новый комментарий.</b>\n<a href='$finalUrl'>$finalUrl</a>";
        
        $administartion_group_id = config('telegram.administartion_group_id');
        $telegramService = new TelegramService();
        $telegramService->sendMessage($administartion_group_id, $message);
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        //
    }
}
