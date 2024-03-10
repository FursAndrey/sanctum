<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_a_role_has_a_user_and_can_be_deleted_by_admin_user()
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

        $deletedRole = [
            'title' => 'some text',
            'discription' => Str::random(10),
        ];
        $delRole = Role::create($deletedRole);

        $anotherUser = User::factory()->create();
        $anotherUser->roles()->sync($delRole->id);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/roles/'.$delRole->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('roles', $deletedRole);
    }

    public function test_a_role_can_be_deleted_by_admin_user()
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

        $deletedRole = [
            'title' => 'some text',
            'discription' => Str::random(10),
        ];
        $delRole = Role::create($deletedRole);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/roles/'.$delRole->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('roles', $deletedRole);
    }

    public function test_a_role_can_not_be_deleted_by_not_admin_user()
    {
        //создание пользователя и присвоение ему роли
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

        $deletedRole = [
            'title' => 'some text',
            'discription' => Str::random(10),
        ];
        $delRole = Role::create($deletedRole);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/roles/'.$delRole->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('roles', $deletedRole);
    }

    public function test_a_role_can_not_be_deleted_by_unauthorised_user()
    {
        $deletedRole = [
            'title' => 'some text',
            'discription' => Str::random(10),
        ];
        $delRole = Role::create($deletedRole);

        //тестируемый запрос от имени пользователя
        $response = $this->delete('/api/roles/'.$delRole->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('roles', $deletedRole);
    }
}
