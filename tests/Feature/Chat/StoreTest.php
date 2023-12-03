<?php

namespace Tests\Feature\Chat;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_users_attribute_is_required_for_creating_chat()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'other user',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $chat = [
            'title' => '2',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/chats', $chat);

        $response
            ->assertStatus(422)
            ->assertInvalid('users')
            ->assertJsonValidationErrors([
                'users' => 'The users field is required.',
            ]);
    }

    public function test_users_attribute_must_be_an_array_for_creating_chat()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'other user',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $chat = [
            'title' => '2',
            'users' => '2',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/chats', $chat);

        $response
            ->assertStatus(422)
            ->assertInvalid('users')
            ->assertJsonValidationErrors([
                'users' => 'The users field must be an array.',
            ]);
    }

    public function test_users_attribute_exists_for_creating_chat()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'other user',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $chat = [
            'title' => '2',
            'users' => [1],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/chats', $chat);

        $response
            ->assertStatus(422)
            ->assertInvalid('users.0')
            ->assertJsonValidationErrors([
                'users.0' => 'The selected users.0 is invalid.',
            ]);
    }

    public function test_title_attribute_is_not_required_for_creating_chat()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'other user',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $anotherUser = User::factory()->create();
        $chat = [
            'title' => null,
            'users' => [$anotherUser->id],
        ];

        $expectedJson = [
            'title' => 'With '.$anotherUser->name,
            'users' => $user->id.'-'.$anotherUser->id,
            'last_message' => null,
            'unreadable_messages_count' => null,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/chats', $chat);

        $response->assertStatus(200);
        $response->assertJson($expectedJson);
        $this->assertDatabaseCount('chats', 1);
    }

    public function test_title_attribute_must_be_string_for_creating_chat()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'other user',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $anotherUser = User::factory()->create();
        $chat = [
            'title' => [null],
            'users' => [$anotherUser->id],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/chats', $chat);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field must be a string.',
            ]);
    }

    public function test_title_attribute_have_max_150_chars_for_creating_chat()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'notAdmin',
                'discription' => 'other user',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $anotherUser = User::factory()->create();
        $chat = [
            'title' => Str::random(151),
            'users' => [$anotherUser->id],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/chats', $chat);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field must not be greater than 150 characters.',
            ]);
    }

    public function test_a_chat_can_not_be_created_by_unauthorised_user(): void
    {
        $chat = [
            'title' => 'some text',
            'users' => 2,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->post('/api/chats', $chat);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('chats', 0);
    }
}
