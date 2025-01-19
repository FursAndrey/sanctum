<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CurrentUserTest extends TestCase
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

    public function test_get_current_user_by_admin_user()
    {
        // создание пользователя и присвоение ему роли
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/currentUser');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'roles' => [
                        $role->title,
                    ],
                ],
            ]
        );
    }

    public function test_get_current_user_by_not_admin_user()
    {
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/currentUser');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'roles' => [
                        $role->title,
                    ],
                ],
            ]
        );
    }

    public function test_get_current_user_by_unauthorised_user()
    {
        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/currentUser');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'name' => null,
                ],
            ]
        );
    }
}
