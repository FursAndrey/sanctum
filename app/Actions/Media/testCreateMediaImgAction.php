<?php
namespace App\Actions\Media;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class testCreateMediaImgAction implements createMediaImgInterface
{
    public function __invoke(Post $post, string $dir = 'testImg'): void
    {
        $hostname = config('app.url');

        $files = Storage::disk('public')->files($dir);
        foreach ($files as $fileName) {
            $img = $hostname.Storage::url($fileName);
            $post->addMediaFromUrl($img)->toMediaCollection('preview');
        }
    }
}