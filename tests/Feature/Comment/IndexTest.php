<?php

namespace Tests\Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(
            [
                'accept' => 'application/json',
            ]
        );
    }

    public function test_return_comments_if_not_exist_comments(): void
    {
        $post = Post::factory(1)->create()->first();
        $response = $this->get('/api/comments/'.$post->id.'/0');

        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                'data' => [],
            ]
        );
    }

    public function test_return_comments_if_exist_comments(): void
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'bot',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();
        $comments = Comment::factory(3)->create();

        $expectedJson = $comments->map(function ($comment) use ($user) {
            return [
                'id' => $comment->id,
                'body' => $comment->body,
                'published' => $comment->published,
                'user' => $user->name,
                'answerCount' => 0,
                'likeCount' => 0,
                'is_liked' => false,
            ];
        })->toArray();
        $expectedJson = array_reverse($expectedJson);

        $response = $this->get('/api/comments/'.$post->id.'/0');

        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                'data' => $expectedJson,
            ]
        );
    }
}
