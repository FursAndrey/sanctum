<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_title_attribute_is_required_for_updating_post()
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
        $response = $this->actingAs($user)->patch('/api/posts2/'.$post->id, $newPost);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'The title field is required.'
            ]);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }

    public function test_body_attribute_is_required_for_updating_post()
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
        $response = $this->actingAs($user)->patch('/api/posts2/'.$post->id, $newPost);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'body' => 'The body field is required.'
            ]);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }

    public function test_a_post_can_be_updated_by_admin_user()
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
        $response = $this->actingAs($user)->patch('/api/posts2/'.$post->id, $newPost);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', $newPost);
        $this->assertDatabaseMissing('posts', $oldPost);
    }

    public function test_a_post_can_not_be_updated_by_not_admin_user()
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
        $response = $this->actingAs($user)->patch('/api/posts2/'.$post->id, $newPost);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.'
            ]
        );
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
        $response = $this->patch('/api/posts2/'.$post->id, $newPost);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
    }
}