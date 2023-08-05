<?php

namespace Database\Seeders;

use App\Actions\Media\createMediaImgAction;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Post::factory(20)->create();
        Post::factory(3)->create()->each(function ($post) {
            (new createMediaImgAction())($post);
        });
    }
}
