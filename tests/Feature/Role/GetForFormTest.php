<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GetForFormTest extends TestCase
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

    public function test_can_not_return_roles_for_unauthorised_user(): void
    {
        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/roles/forForm');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_return_roles_for_not_admin_user(): void
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
        $response = $this->actingAs($user)->get('/api/roles/forForm');

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_can_return_roles_for_admin_user(): void
    {
        $role_not_admin = Role::create(
            [
                'title' => 'not_admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );

        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/roles/forForm');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => $role_not_admin->id,
                        'title' => $role_not_admin->title,
                        'discription' => $role_not_admin->discription,
                    ],
                    [
                        'id' => $role->id,
                        'title' => $role->title,
                        'discription' => $role->discription,
                    ],
                ],
            ]
        );
    }
}
