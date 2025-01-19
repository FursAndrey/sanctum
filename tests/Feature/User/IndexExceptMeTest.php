<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexExceptMeTest extends TestCase
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

    public function test_can_not_return_users_for_chat_for_unauthorised_user(): void
    {
        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/users/exceptMe');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_return_users_for_chat_for_authorised_user(): void
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_Admin',
                'discription' => 'Creator of another site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $otherUsers = User::factory(2)->create();

        $expectedJson = $otherUsers->map(function ($otherUser) {
            return [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
            ];
        })->toArray();

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users/exceptMe');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => $expectedJson,
            ]
        );
    }
}
