<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
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

    public function test_can_not_return_user_by_id_for_unauthorised_user()
    {
        $user = User::factory()->create();

        //тестируемый запрос от имени пользователя
        $response = $this->get('/api/users/'.$user->id);
        
        $response->assertStatus(401);
        $response->assertJson(
            [
                "message"=>"Unauthenticated."
            ]
        );
    }
    
    public function test_can_not_return_user_by_id_for_not_admin_user()
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

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users/'.$user->id);
        
        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                "message"=>"This action is unauthorized."
            ]
        );
    }
    
    public function test_can_return_user_by_id_for_admin_user()
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

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users/'.$user->id);
        
        $response->assertStatus(200);
        $response->assertJson(
            [
                'data'=>[
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "created" => $user->created,
                    "roles" => [
                        [
                            "id" => $role->id,
                            "title" => $role->title,
                            "discription" => $role->discription,
                        ]
                    ],
                ]
            ]
        );
    }
}
