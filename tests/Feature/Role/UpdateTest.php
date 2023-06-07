<?php

namespace Tests\Feature\Role;

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
    
    public function test_discription_attribute_is_required_for_updating_role()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title'=>'Admin',
                'discription'=>'Creator of this site',
                'created_at'=>null,
                'updated_at'=>null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $oldRoleArray = [
            'title'=>'some text',
            'discription'=>'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);
        $newRole = [
            'discription'=>''
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/roles/'.$oldRole->id, $newRole);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('discription')
            ->assertJsonValidationErrors([
                'discription' => 'The discription field is required.'
            ]);
        $this->assertDatabaseHas('roles', $oldRoleArray);

        $newRole['title'] = $oldRoleArray['title'];

        $this->assertDatabaseMissing('roles', $newRole);
    }

    public function test_discription_attribute_is_max_200_chars_for_updating_role()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title'=>'Admin',
                'discription'=>'Creator of this site',
                'created_at'=>null,
                'updated_at'=>null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $oldRoleArray = [
            'title'=>'some text',
            'discription'=>'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);        
        $newRole = [
            'discription' => Str::random(201),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/roles/'.$oldRole->id, $newRole);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('discription')
            ->assertJsonValidationErrors([
                'discription' => 'The discription field must not be greater than 200 characters.'
            ]);
        $this->assertDatabaseHas('roles', $oldRoleArray);

        $newRole['title'] = $oldRoleArray['title'];

        $this->assertDatabaseMissing('roles', $newRole);
    }
    
    public function test_a_role_can_be_updated_by_admin_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title'=>'Admin',
                'discription'=>'Creator of this site',
                'created_at'=>null,
                'updated_at'=>null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $oldRoleArray = [
            'title'=>'some text',
            'discription'=>'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);
        $newRole = [
            'discription' => Str::random(150),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/roles/'.$oldRole->id, $newRole);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('roles', $newRole);

        $newRole['title'] = $oldRoleArray['title'];

        $this->assertDatabaseMissing('roles', $oldRoleArray);
    }

    public function test_a_role_can_not_be_updated_by_not_admin_user()
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title'=>'not_admin',
                'discription'=>'Creator of this site',
                'created_at'=>null,
                'updated_at'=>null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $oldRoleArray = [
            'title'=>'some text',
            'discription'=>'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);        
        $newRole = [
            'discription' => Str::random(150),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/roles/'.$oldRole->id, $newRole);
        
        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                "message"=>"This action is unauthorized."
            ]
        );
        $this->assertDatabaseHas('roles', $oldRoleArray);

        $newRole['title'] = $oldRoleArray['title'];

        $this->assertDatabaseMissing('roles', $newRole);
    }

    public function test_a_post_can_not_be_updated_by_unauthorised_user()
    {
        $oldRoleArray = [
            'title'=>'some text',
            'discription'=>'Creator of this site',
        ];
        $oldRole = Role::create($oldRoleArray);        
        $newRole = [
            'discription' => Str::random(150),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->put('/api/roles/'.$oldRole->id, $newRole);
        
        $response->assertStatus(401);
        $response->assertJson(
            [
                "message"=>"Unauthenticated."
            ]
        );
        $this->assertDatabaseHas('roles', $oldRoleArray);
        $this->assertDatabaseMissing('roles', $newRole);
    }
}
