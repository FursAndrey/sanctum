<?php

namespace Tests\Feature\Post;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
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

    public function test_a_post_can_be_deleted_by_admin_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();

        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/posts/'.$post->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', $oldPost);
    }

    public function test_a_post_can_not_be_deleted_by_not_admin_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();

        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/posts/'.$post->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('posts', $oldPost);
    }

    public function test_a_post_can_not_be_deleted_by_unauthorised_user()
    {
        $post = Post::factory(1)->create()->first();

        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $response = $this->delete('/api/posts/'.$post->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('posts', $oldPost);
    }

    public function test_a_post_can_be_deleted_with_all_if_it_has_comments()
    {
        //создание пользователя и присвоение ему роли
        $botRole = Role::create(
            [
                'title' => 'bot',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $bot = User::factory()->create();
        $bot->roles()->sync($botRole->id);

        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();
        $comments = Comment::factory(3)->create();

        $oldComments = $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'body' => $comment->body,
                'post_id' => $comment->post_id,
                'user_id' => $comment->user_id,
                'parent_id' => 0,
            ];
        })->toArray();

        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/posts/'.$post->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', $oldPost);

        foreach ($oldComments as $key => $currentComment) {
            $this->assertDatabaseMissing('comments', $currentComment);
        }
    }
}
