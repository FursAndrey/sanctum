<?php

namespace Tests\Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(
            [
                'accept' => 'application/json',
            ]
        );
    }

    public function test_a_comment_can_be_deleted_by_admin_user()
    {
        // создание пользователя и присвоение ему роли
        $roleAdmin = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $userAdmin = User::factory()->create();
        $userAdmin->roles()->sync($roleAdmin->id);

        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'bot',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();
        $comment = Comment::factory(1)->create()->first();

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];
        $response = $this->actingAs($userAdmin)->delete('/api/comments/'.$comment->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('comments', $oldComment);
    }

    public function test_a_comment_can_not_be_deleted_by_not_admin_user()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'bot',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();
        $comment = Comment::factory(1)->create()->first();

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];
        $response = $this->actingAs($user)->delete('/api/comments/'.$comment->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('comments', $oldComment);
    }

    public function test_a_comment_can_not_be_deleted_by_unauthorised_user()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'bot',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();
        $comment = Comment::factory(1)->create()->first();

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];
        $response = $this->delete('/api/comments/'.$comment->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('comments', $oldComment);
    }
}
