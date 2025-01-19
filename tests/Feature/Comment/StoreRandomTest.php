<?php

namespace Tests\Feature\Comment;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreRandomTest extends TestCase
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

    public function test_a_comment_can_be_stored_by_admin_user()
    {
        $posts = Post::factory(1)->create();
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create(['name' => 'testAdmin']);
        $user->roles()->sync($role->id);

        $roleBot = Role::create(
            [
                'title' => 'bot',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $userBot = User::factory()->create();
        $userBot->roles()->sync($roleBot->id);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments/storeRandomComment');

        $response->assertStatus(201);
        $this->assertDatabaseCount('comments', 1);
    }

    public function test_a_comment_can_not_be_stored_by_not_admin_user()
    {
        $posts = Post::factory(1)->create();
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $roleBot = Role::create(
            [
                'title' => 'bot',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $userBot = User::factory()->create();
        $userBot->roles()->sync($roleBot->id);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/comments/storeRandomComment');

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_a_comment_can_not_be_stored_by_unauthorised_user()
    {
        $posts = Post::factory(1)->create();
        $response = $this->post('/api/comments/storeRandomComment');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('comments', 0);
    }
}
