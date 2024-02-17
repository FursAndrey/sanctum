<?php

namespace Tests\Feature\Comment;

use App\Models\BanComment;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
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

    public function test_post_id_attribute_is_required_for_storing_comment()
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

        Post::factory(1)->create()->first();

        $comment = [
            'body' => 'some text',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response
            ->assertStatus(422)
            ->assertInvalid('post_id')
            ->assertJsonValidationErrors([
                'post_id' => 'The post id field is required.',
            ]);
    }

    public function test_post_id_attribute_is_not_string_for_storing_comment()
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

        Post::factory(1)->create()->first();

        $comment = [
            'body' => 'some text',
            'post_id' => 'some text',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response
            ->assertStatus(422)
            ->assertInvalid('post_id')
            ->assertJsonValidationErrors([
                'post_id' => 'The post id field must be an integer.',
            ]);
    }

    public function test_post_id_attribute_is_integer_but_not_exists_table_for_storing_comment()
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
        $postId = $post->id * 10;

        $comment = [
            'body' => 'some text',
            'post_id' => $postId,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response
            ->assertStatus(422)
            ->assertInvalid('post_id')
            ->assertJsonValidationErrors([
                'post_id' => 'The selected post id is invalid.',
            ]);
    }

    public function test_post_id_attribute_is_integer_and_exists_table_for_storing_comment()
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

        $comment = [
            'body' => 'some text',
            'post_id' => $post->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response->assertStatus(201);
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', $comment);
    }

    public function test_body_attribute_is_required_for_storing_comment()
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

        $comment = [
            'post_id' => $post->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response
            ->assertStatus(422)
            ->assertInvalid('body')
            ->assertJsonValidationErrors([
                'body' => 'The body field is required.',
            ]);
    }

    public function test_body_attribute_is_not_string_for_storing_comment()
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

        $comment = [
            'body' => ['The body field is required.'],
            'post_id' => $post->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response
            ->assertStatus(422)
            ->assertInvalid('body')
            ->assertJsonValidationErrors([
                'body' => 'The body field must be a string.',
            ]);
    }

    public function test_a_comment_can_not_be_stored_by_unauthorised_user(): void
    {
        $comment = [];
        $response = $this->post('/api/comments', $comment);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('comments', 0);
        $this->assertDatabaseMissing('comments', $comment);
    }

    public function test_comment_can_be_storing_by_not_admin_user_without_ban_comment()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = Post::factory(1)->create()->first();

        $comment = [
            'body' => 'some text',
            'post_id' => $post->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response->assertStatus(201);
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', $comment);
    }

    public function test_comment_can_not_be_storing_by_not_admin_user_with_ban_comment()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        BanComment::create(['user_id' => $user->id]);

        $post = Post::factory(1)->create()->first();

        $comment = [
            'body' => 'some text',
            'post_id' => $post->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $comment);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('comments', 0);
        $this->assertDatabaseMissing('comments', $comment);
    }

    public function test_comment_can_have_answer()
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

        $answer = [
            'body' => 'some text',
            'post_id' => $post->id,
            'parent_id' => $comment->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $answer);

        $response->assertStatus(201);
        $this->assertDatabaseCount('comments', 2);
        $this->assertDatabaseHas('comments', $answer);
    }

    public function test_parent_id_attribute_is_not_integer_for_storing_comment()
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

        $answer = [
            'body' => 'some text',
            'post_id' => $post->id,
            'parent_id' => 'qwert',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $answer);

        $response
            ->assertStatus(422)
            ->assertInvalid('parent_id')
            ->assertJsonValidationErrors([
                'parent_id' => 'The parent id field must be an integer.',
            ]);
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseMissing('comments', $answer);
    }

    public function test_parent_id_attribute_is_not_exists_in_table_for_storing_comment()
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

        $answer = [
            'body' => 'some text',
            'post_id' => $post->id,
            'parent_id' => $comment->id * 10,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments', $answer);

        $response
            ->assertStatus(422)
            ->assertInvalid('parent_id')
            ->assertJsonValidationErrors([
                'parent_id' => 'The selected parent id is invalid.',
            ]);
        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseMissing('comments', $answer);
    }
}
