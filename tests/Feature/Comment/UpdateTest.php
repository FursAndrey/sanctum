<?php

namespace Tests\Feature\Comment;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
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

    public function test_comment_can_be_updated_by_admin_user()
    {
        //создание пользователя и присвоение ему роли
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
        $comment = Comment::factory(1)->create()->first();

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];

        $newComment = [
            'body' => Str::random(100),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/comments/'.$comment->id, $newComment);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', $newComment);

        $this->assertDatabaseMissing('comments', $oldComment);
    }

    public function test_body_attribute_is_required_for_updating_comment()
    {
        //создание пользователя и присвоение ему роли
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
        $comment = Comment::factory(1)->create()->first();

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];

        $newComment = [
            'qwe',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($userAdmin)->put('/api/comments/'.$comment->id, $newComment);

        $response
            ->assertStatus(422)
            ->assertInvalid('body')
            ->assertJsonValidationErrors([
                'body' => 'The body field is required.',
            ]);
        $this->assertDatabaseHas('comments', $oldComment);
    }

    public function test_body_attribute_is_string_for_updating_comment()
    {
        //создание пользователя и присвоение ему роли
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
        $comment = Comment::factory(1)->create()->first();

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];

        $newComment = [
            'body' => ['qwerty'],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($userAdmin)->put('/api/comments/'.$comment->id, $newComment);

        $response
            ->assertStatus(422)
            ->assertInvalid('body')
            ->assertJsonValidationErrors([
                'body' => 'The body field must be a string.',
            ]);
        $this->assertDatabaseHas('comments', $oldComment);
    }

    public function test_comment_can_not_updated_by_unauthorised_user()
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
        $comment = Comment::factory(1)->create()->first();

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];

        $newComment = [
            'body' => Str::random(100),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->put('/api/comments/'.$comment->id, $newComment);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('comments', $oldComment);
        $this->assertDatabaseMissing('comments', $newComment);
    }

    public function test_comment_can_not_updated_by_another_authorised_user()
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
        $comment = Comment::factory(1)->create()->first();

        $anotherUser = User::factory()->create();
        $anotherUser->roles()->sync($role->id);

        $oldComment = [
            'body' => $comment->body,
            'post_id' => $post->id,
            'parent_id' => null,
        ];

        $newComment = [
            'body' => Str::random(100),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($anotherUser)->put('/api/comments/'.$comment->id, $newComment);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('comments', $oldComment);
        $this->assertDatabaseMissing('comments', $newComment);
    }
}
