<?php

namespace Tests\Feature\Post;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    public function test_title_attribute_is_required_for_storing_post()
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

        $post = [
            'body' => 'some text',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/posts', $post);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field is required.'
            ]);
    }

    public function test_body_attribute_is_required_for_storing_post()
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

        $post = [
            'title' => 'some text',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/posts', $post);
        
        $response
            ->assertStatus(422)
            ->assertInvalid('body')
            ->assertJsonValidationErrors([
                'body' => 'The body field is required.'
            ]);
    }

    public function test_a_post_can_not_be_stored_without_image_by_unauthorised_user(): void
    {
        $post = [
            'title' => 'some text',
            'body' => 'some text',
        ];
        $response = $this->post('/api/posts', $post);
        
        $response->assertStatus(401);
        $response->assertJson(
            [
                "message"=>"Unauthenticated."
            ]
        );
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseMissing('posts', $post);
    }

    public function test_a_post_can_be_stored_without_image_by_admin_user(): void
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

        $post = [
            'title' => 'some text',
            'body' => 'some text',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/posts', $post);
        
        $response->assertStatus(201);
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $post);
    }

    public function test_a_post_can_not_be_stored_without_image_by_not_admin_user(): void
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title'=>'not_dmin',
                'discription'=>'Creator of this site',
                'created_at'=>null,
                'updated_at'=>null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $post = [
            'title' => 'some text',
            'body' => 'some text',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/posts', $post);
        
        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                "message"=>"This action is unauthorized."
            ]
        );
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseMissing('posts', $post);
    }
}
