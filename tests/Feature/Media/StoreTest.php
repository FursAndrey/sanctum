<?php

namespace Tests\Feature\Media;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_a_post_with_an_img_can_not_be_stored_by_unauthorised_user(): void
    {
        $post = [
            'title' => 'some text',
            'body' => 'some text',
        ];

        $response = $this->post('/api/posts2', $post);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_a_post_with_an_img_can_not_be_stored_by_not_admin_user(): void
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

        $post = [
            'title' => 'some text',
            'body' => 'some text',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/posts2', $post);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_a_post_with_an_image_can_be_stored_by_admin_user(): void
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

        // подготовка тестового изображения
        $image = UploadedFile::fake()->image('avatar.jpg');

        $post = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                $image,
            ],
        ];

        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('posts', 0);

        $this->actingAs($user)->post('/api/posts2', $post);

        $this->assertDatabaseCount('media', 1);

        unset($post['imgs']);
        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $post);
    }

    public function test_can_not_store_more_than_one_img()
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

        // подготовка тестового изображения
        $image = UploadedFile::fake()->image('avatar.jpg');
        $image2 = UploadedFile::fake()->image('avatar2.jpg');

        $post = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                $image,
                $image2,
            ],
        ];

        $response = $this->actingAs($user)->post('/api/posts2', $post);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The imgs field must contain 1 items.',
            ]
        );
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('posts', 0);
    }

    public function test_can_not_store_empty_imgs_array()
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

        $post = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [],
        ];

        $response = $this->actingAs($user)->post('/api/posts2', $post);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The imgs field must contain 1 items.',
            ]
        );
        $this->assertDatabaseCount('media', 0);
        $this->assertDatabaseCount('posts', 0);
    }
}
