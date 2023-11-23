<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Preview>
 */
class PreviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $posts = Post::factory(1)->create();

        return [
            'path' => $this->faker->loremflickr('preview'),
            'post_id' => $posts->first()->id,
            'user_id' => Role::where('title', '=', 'Admin')->first()->users()->first()->id,
        ];
    }
}
