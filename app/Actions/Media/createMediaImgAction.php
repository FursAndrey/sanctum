<?php
namespace App\Actions\Media;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

class createMediaImgAction 
{
    public function __invoke(Post $post, string $dir = 'preview', int $width = 500, int $height = 500): void
    {
        $name = $dir . '/' . Str::random(40) . '.jpg';

        Storage::disk('public')->put(
            $name,
            file_get_contents("https://loremflickr.com/$width/$height/animals")
        );

        $img = 'http://sanctum/storage/'.$name;
        $post->addMediaFromUrl($img)->toMediaCollection('preview');
        
        Storage::disk('public')->delete($img);
    }
}