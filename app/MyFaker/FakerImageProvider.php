<?php

namespace App\MyFaker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FakerImageProvider extends Base
{
    public function loremflickr(string $dir = 'preview', int $width = 200, int $height = 200): string
    {
        $name = $dir . '/' . Str::random(40) . '.jpg';

        Storage::disk('public')->put(
            $name,
            file_get_contents("https://loremflickr.com/$width/$height/animals")
        );
        return $name;
    }
}
