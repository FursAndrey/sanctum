<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_return_posts_if_not_exist_posts(): void
    {
        $response = $this->get('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonFragment(["posts"=>[]]);
        $response->assertJsonFragment(
            [
                "meta"=>[
                    "current_page"=>1,
                    "from"=>null,
                    "last_page"=>1,
                    "path"=>"http://localhost/api/posts",
                    "per_page"=>10,
                    "to"=>null,
                    "total"=>0
                ]
            ]
        );
    }

    public function test_return_posts_if_exist_posts(): void
    {
        Post::factory(5)->create();
        $response = $this->get('/api/posts');
        
        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data'=>[
                    "posts"=>[
                        '*'=>[
                            'id',
                            'title',
                            'body'
                        ]
                    ],
                    "meta"=>[
                        "current_page",
                        "from",
                        "last_page",
                        "path",
                        "per_page",
                        "to",
                        "total"
                    ]
                ]
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
            ->assertStatus(302)
            ->assertRedirect()
            ->assertInvalid('title');
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
            ->assertStatus(302)
            ->assertRedirect()
            ->assertInvalid('body');
    }

    public function test_a_post_can_not_be_stored_without_image_by_unauthorised_user(): void
    {
        $post = [
            'title' => 'some text',
            'body' => 'some text',
        ];
        $response = $this->post('/api/posts', $post);
        
        $response->assertStatus(302);
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
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseMissing('posts', $post);
    }

    public function test_return_post_by_id()
    {
        $post = Post::factory(1)->create()->first();
        $response = $this->get('/api/posts/'.$post->id);
        
        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'data'=>[
                    'id',
                    'title',
                    'body'
                ]
            ]
        );
    }

    public function test_a_post_can_be_updated_by_admin_user()
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

        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/posts/'.$post->id, $newPost);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', $newPost);
        $this->assertDatabaseMissing('posts', $oldPost);
    }
    
    public function test_title_attribute_is_required_for_updating_post()
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

        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $newPost = [
            'title' => '',
            'body' => 'some text',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/posts/'.$post->id, $newPost);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }

    public function test_body_attribute_is_required_for_updating_post()
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

        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $newPost = [
            'title' => 'some text',
            'body' => '',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/posts/'.$post->id, $newPost);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }


    public function test_a_post_can_not_be_updated_by_not_admin_user()
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

        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/posts/'.$post->id, $newPost);
        
        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }

    public function test_a_post_can_not_be_updated_by_unauthorised_user()
    {
        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->put('/api/posts/'.$post->id, $newPost);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }

    public function test_a_post_can_be_deleted_by_admin_user()
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

        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/posts/'.$post->id);
        
        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', $oldPost);
    }

    public function test_a_post_can_not_be_deleted_by_not_admin_user()
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

        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/posts/'.$post->id);
        
        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', $oldPost);
    }

    public function test_a_post_can_not_be_deleted_by_unauthorised_user()
    {
        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $response = $this->delete('/api/posts/'.$post->id);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('posts', $oldPost);
    }
}
