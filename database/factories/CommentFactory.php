<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomPostId = Post::select('id')->inRandomOrder()->first()->id;

        $usersWithBotRole = Role::select('id')->with(['users'])->where('title', '=', 'bot')->first()->users->pluck('id');
        $randomIndex = rand(1, count($usersWithBotRole)) - 1;
        $randomUserId = $usersWithBotRole[$randomIndex];
        
        return [
            'post_id' => $randomPostId,
            'user_id' => $randomUserId,
            'body' => fake()->realText(500),
        ];
    }
}
