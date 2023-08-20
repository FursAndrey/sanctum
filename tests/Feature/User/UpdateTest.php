<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    
    public function test_roles_attribute_is_required_for_updating_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => ''
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $newRole);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('roles')
            ->assertJsonValidationErrors([
                'roles' => 'The roles field is required.'
            ]);
    }

    public function test_roles_attribute_is_aray_for_updating_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => 'string'
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $newRole);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('roles')
            ->assertJsonValidationErrors([
                'roles' => 'The roles field must be an array.'
            ]);
    }

    public function test_role_id_attribute_is_required_for_updating_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => [
                [
                    'title' => 'title',
                ]
            ]
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $newRole);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('roles.0.id')
            ->assertJsonValidationErrors([
                "roles.0.id" => "The roles.0.id field is required."
            ]);
    }

    public function test_role_id_attribute_is_integer_for_updating_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => [
                [
                    'id' => 'id',
                ]
            ]
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $newRole);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('roles.0.id')
            ->assertJsonValidationErrors([
                "roles.0.id" => "The roles.0.id field must be an integer."
            ]);
    }

    public function test_role_id_attribute_is_exists_for_updating_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => [
                [
                    'id' => $role->id*10,
                ]
            ]
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $newRole);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('roles.0.id')
            ->assertJsonValidationErrors([
                "roles.0.id" => "The selected roles.0.id is invalid."
            ]);
    }

    public function test_user_can_be_updated_by_admin_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $anotherRole = Role::create(
            [
                'title' => 'not_Admin',
                'discription' => 'not Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => [
                [
                    'id' => $role->id,
                ],
                [
                    'id' => $anotherRole->id,
                ]
            ]
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $newRole);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas(
            'role_user', 
            [
                'user_id' => $updatingUser->id,
                'role_id' => $role->id,
            ]
        );
        $this->assertDatabaseHas(
            'role_user', 
            [
                'user_id' => $updatingUser->id,
                'role_id' => $anotherRole->id,
            ]
        );
    }

    public function test_user_can_not_be_updated_by_not_admin_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $anotherRole = Role::create(
            [
                'title' => 'test',
                'discription' => 'not Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => [
                [
                    'id' => $role->id,
                ],
                [
                    'id' => $anotherRole->id,
                ]
            ]
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $newRole);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                "message"=>"This action is unauthorized."
            ]
        );
        $this->assertDatabaseHas(
            'role_user', 
            [
                'user_id' => $updatingUser->id,
                'role_id' => $role->id,
            ]
        );
        $this->assertDatabaseMissing(
            'role_user', 
            [
                'user_id' => $updatingUser->id,
                'role_id' => $anotherRole->id,
            ]
        );
    }

    public function test_user_can_not_be_updated_by_unauthorised_user()
    {
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $anotherRole = Role::create(
            [
                'title' => 'test',
                'discription' => 'not Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create();
        $updatingUser->roles()->sync($role->id);

        $newRole = [
            'roles' => [
                [
                    'id' => $role->id,
                ],
                [
                    'id' => $anotherRole->id,
                ]
            ]
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->put('/api/users/'.$updatingUser->id, $newRole);
        
        $response->assertStatus(401);
        $response->assertJson(
            [
                "message" => "Unauthenticated."
            ]
        );
        $this->assertDatabaseHas(
            'role_user', 
            [
                'user_id' => $updatingUser->id,
                'role_id' => $role->id,
            ]
        );
        $this->assertDatabaseMissing(
            'role_user', 
            [
                'user_id' => $updatingUser->id,
                'role_id' => $anotherRole->id,
            ]
        );
    }

    public function test_tg_name_must_be_a_string()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $newTgName = [
            'tg_name' => 123,
            'roles' => [
                [
                    'id' => $role->id,
                ]
            ]
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$user->id, $newTgName);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('tg_name')
            ->assertJsonValidationErrors([
                "tg_name" => "The tg name field must be a string."
            ]);
    }
    
    public function test_tg_name_not_have_more_than_100_symbols()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $newTgName = [
            'tg_name' => Str::random(101),
            'roles' => [
                [
                    'id' => $role->id,
                ]
            ]
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$user->id, $newTgName);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('tg_name')
            ->assertJsonValidationErrors([
                "tg_name" => "The tg name field must not be greater than 100 characters."
            ]);
    }

    public function test_tg_name_must_be_unique()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create(['tg_name'=>'tg_name']);
        $user->roles()->sync($role->id);

        //подготовка юзера к обновлению
        $updatingUser = User::factory()->create(['tg_name'=>'test1']);
        $updatingUser->roles()->sync($role->id);

        $forUpdate = [
            'tg_name' => 'tg_name',
            'roles' => [
                [
                    'id' => $role->id,
                ]
            ]
        ];
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$updatingUser->id, $forUpdate);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('tg_name')
            ->assertJsonValidationErrors([
                "tg_name" => "The tg name has already been taken."
            ]);
    }

    public function test_tg_name_may_not_be_unique_for_same_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $anotherRole = Role::create(
            [
                'title' => 'not_Admin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create(['tg_name'=>'tg_name']);
        $user->roles()->sync($role->id);
        
        $forUpdate = [
            'tg_name' => 'tg_name',
            'roles' => [
                [
                    'id' => $role->id,
                ],
                [
                    'id' => $anotherRole->id,
                ]
            ]
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/users/'.$user->id, $forUpdate);
        
        $response->assertStatus(200);
    }
}
