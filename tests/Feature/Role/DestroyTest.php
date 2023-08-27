<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $oldRoleArray = [
            'title' => 'some text',
            'discription' => 'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/roles/'.$oldRole->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('roles', $oldRoleArray);
    }

    public function test_a_role_can_not_be_deleted_by_not_admin_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $oldRoleArray = [
            'title' => 'some text',
            'discription' => 'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/roles/'.$oldRole->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('roles', $oldRoleArray);
    }

    public function test_a_role_can_not_be_deleted_by_unauthorised_user()
    {
        $oldRoleArray = [
            'title' => 'some text',
            'discription' => 'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);

        //тестируемый запрос от имени пользователя
        $response = $this->delete('/api/roles/'.$oldRole->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('roles', $oldRoleArray);
    }
}
