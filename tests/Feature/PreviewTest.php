<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PreviewTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_an_image_can_not_be_stored_by_unauthorised_user(): void
    {
        Storage::fake('public');
        $image = File::create('test_img.jpg');

        $response = $this->post('/api/preview', ['image' => $image]);
        
        $response->assertStatus(302);
    }

    public function test_an_image_is_file_for_stored(): void
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

        $image = 'test_img.jpg';

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/preview', ['image' => $image]);
        
        $response
            ->assertStatus(302)
            ->assertRedirect()
            ->assertInvalid('image');
    }

    public function test_an_image_can_be_stored_by_admin_user(): void
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

        //подготовка тестового изображения
        Storage::fake('public');
        $image = File::create('test_img.jpg');

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/preview', ['image' => $image]);
        
        $response->assertStatus(201);
        $this->assertDatabaseCount('previews', 1);
        $this->assertDatabaseHas(
            'previews', 
            [
                'path' => 'preview/'.$image->hashName(), 
                'post_id' => NULL, 
                'user_id' => $user->id
            ]
        );
        Storage::disk('public')->assertExists('preview/'.$image->hashName());
    }

    public function test_an_image_can_be_stored_by_not_admin_user(): void
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

        //подготовка тестового изображения
        Storage::fake('public');
        $image = File::create('test_img.jpg');

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/preview', ['image' => $image]);
        
        $response->assertStatus(403);
        $this->assertDatabaseCount('previews', 0);
        $this->assertDatabaseMissing(
            'previews', 
            [
                'path' => 'preview/'.$image->hashName(), 
                'post_id' => NULL, 
                'user_id' => $user->id
            ]
        );
        Storage::disk('public')->assertMissing('preview/'.$image->hashName());
    }
    
    public function test_a_post_with_an_image_can_be_stored_by_admin_user(): void
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

        //подготовка тестового изображения
        Storage::fake('public');
        $image = File::create('test_img.jpg');

        //сохраняем изображение, что бы тестировать отправку поста с изображением
        $response = $this->actingAs($user)->post('/api/preview', ['image' => $image]);
        $this->assertDatabaseCount('previews', 1);
        $this->assertDatabaseHas(
            'previews', 
            [
                'path' => 'preview/'.$image->hashName(), 
                'post_id' => NULL, 
                'user_id' => $user->id
            ]
        );
        Storage::disk('public')->assertExists('preview/'.$image->hashName());
        
        $post = [
            'title' => 'some text',
            'body' => 'some text',
            'image_id' => $response->original->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/posts', $post);
        unset($post['image_id']);

        $response->assertStatus(201);
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $post);
    }
    
    public function test_a_post_with_an_image_can_be_updated_by_admin_user(): void
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

        //подготовка тестового изображения
        Storage::fake('public');
        $image = File::create('test_img.jpg');

        //сохраняем изображение, что бы тестировать отправку поста с изображением
        $response = $this->actingAs($user)->post('/api/preview', ['image' => $image]);
        $this->assertDatabaseCount('previews', 1);
        $this->assertDatabaseHas(
            'previews', 
            [
                'path' => 'preview/'.$image->hashName(), 
                'post_id' => NULL, 
                'user_id' => $user->id
            ]
        );
        Storage::disk('public')->assertExists('preview/'.$image->hashName());
        
        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'image_id' => $response->original->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/posts/'.$post->id, $newPost);
        unset($newPost['image_id']);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', $newPost);
        $this->assertDatabaseMissing('posts', $oldPost);
    }
    
    public function test_image_id_exists_for_storing_post_by_admin_user(): void
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

        //подготовка тестового изображения
        Storage::fake('public');
        $image = File::create('test_img.jpg');

        //сохраняем изображение, что бы тестировать отправку поста с изображением
        $response = $this->actingAs($user)->post('/api/preview', ['image' => $image]);
        $this->assertDatabaseCount('previews', 1);
        $this->assertDatabaseHas(
            'previews', 
            [
                'path' => 'preview/'.$image->hashName(), 
                'post_id' => NULL, 
                'user_id' => $user->id
            ]
        );
        Storage::disk('public')->assertExists('preview/'.$image->hashName());
        $image_id = (int)$response->original->id * 10;
        
        $post = [
            'title' => 'some text',
            'body' => 'some text',
            'image_id' => $image_id,
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/posts', $post);
        unset($post['image_id']);

        $response->assertStatus(302);
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseMissing('posts', $post);
    }
    
    public function test_image_id_exists_for_updating_by_admin_user(): void
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

        //подготовка тестового изображения
        Storage::fake('public');
        $image = File::create('test_img.jpg');

        //сохраняем изображение, что бы тестировать отправку поста с изображением
        $response = $this->actingAs($user)->post('/api/preview', ['image' => $image]);
        $this->assertDatabaseCount('previews', 1);
        $this->assertDatabaseHas(
            'previews', 
            [
                'path' => 'preview/'.$image->hashName(), 
                'post_id' => NULL, 
                'user_id' => $user->id
            ]
        );
        Storage::disk('public')->assertExists('preview/'.$image->hashName());
        
        $post = Post::factory(1)->create()->first();
        
        $oldPost = [
            'title' => $post->title,
            'body' => $post->body,
        ];
        $image_id = (int)$response->original->id * 10;
        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'image_id' => $image_id,
        ];
        
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/posts/'.$post->id, $newPost);
        unset($newPost['image_id']);
        
        $response->assertStatus(302);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }
}
