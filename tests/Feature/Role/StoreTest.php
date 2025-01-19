<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
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

    public function test_title_attribute_is_required_for_storing_role()
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

        $newRole = [
            'discription' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field is required.',
            ]);
    }

    public function test_title_attribute_is_string_for_storing_role()
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

        $newRole = [
            'title' => ['some array'],
            'discription' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field must be a string.',
            ]);
    }

    public function test_title_attribute_is_unique_for_storing_role()
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

        $newRole = [
            'title' => 'Admin',
            'discription' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title has already been taken.',
            ]);
    }

    public function test_title_attribute_is_max_100_chars_for_storing_role()
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

        $newRole = [
            'title' => Str::random(101),
            'discription' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field must not be greater than 100 characters.',
            ]);
    }

    public function test_discription_attribute_is_required_for_storing_role()
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

        $newRole = [
            'title' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response
            ->assertStatus(422)
            ->assertInvalid('discription')
            ->assertJsonValidationErrors([
                'discription' => 'The discription field is required.',
            ]);
    }

    public function test_discription_attribute_is_string_for_storing_role()
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

        $newRole = [
            'title' => 'some text',
            'discription' => ['some array'],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response
            ->assertStatus(422)
            ->assertInvalid('discription')
            ->assertJsonValidationErrors([
                'discription' => 'The discription field must be a string.',
            ]);
    }

    public function test_discription_attribute_is_max_200_chars_for_storing_role()
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

        $newRole = [
            'title' => 'some text',
            'discription' => Str::random(201),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response
            ->assertStatus(422)
            ->assertInvalid('discription')
            ->assertJsonValidationErrors([
                'discription' => 'The discription field must not be greater than 200 characters.',
            ]);
    }

    public function test_a_role_can_not_be_stored_by_unauthorised_user(): void
    {
        $role = [
            'title' => 'some text',
            'discription' => 'some text',
        ];
        $response = $this->post('/api/roles', $role);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('roles', 0);
        $this->assertDatabaseMissing('roles', $role);
    }

    public function test_a_role_can_be_stored_by_admin_user(): void
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

        $newRole = [
            'title' => 'some text',
            'discription' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response->assertStatus(201);
        $this->assertDatabaseCount('roles', 2);
        $this->assertDatabaseHas('roles', $newRole);
    }

    public function test_a_role_can_not_be_stored_by_not_admin_user(): void
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_dmin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $newRole = [
            'title' => 'some text',
            'discription' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/roles', $newRole);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('roles', 1);
        $this->assertDatabaseMissing('roles', $newRole);
    }
}
