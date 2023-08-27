<?php

namespace Tests\Feature\Media;

use App\Actions\Media\testCreateMediaImgAction;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_a_post_with_an_img_can_not_be_updated_by_unauthorised_user(): void
    {
        $image = UploadedFile::fake()->image('avatar.jpg');

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                0 => $image,
            ]
        ];

        $oldPostModel = Post::factory(1)->create()->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $response = $this->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        unset($newPost['imgs']);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.'
            ]
        );
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);

        $this->assertDatabaseCount('media', 0);
    }

    public function test_a_post_with_an_img_can_not_be_updated_by_not_admin_user(): void
    {
        //создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_dmin',
                'discription' => 'Creator of this site',
                'created_at' => null,
                'updated_at' => null
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $image = UploadedFile::fake()->image('avatar2.jpg');

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;
        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                0 => $image,
            ],
            'deleted_preview' => [
                $oldMediaId
            ]
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $oldMediaCollection = $oldPostModel->getMedia('preview');

        $this->assertEquals(1, $oldMediaCollection->count());
        $oldMediaModel = $oldMediaCollection->first();

        $oldMedia = [
            'id' => $oldMediaModel->id,
            'uuid' => $oldMediaModel->uuid,
            'name' => $oldMediaModel->name,
            'updated_at' => $oldMediaModel->updated_at,
        ];
        unset($newPost['imgs']);
        unset($newPost['deleted_preview']);

        $response->assertStatus(403);
        $response->assertJson(
            [
                'message' => 'This action is unauthorized.'
            ]
        );
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);

        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', $oldMedia);
    }

    public function test_a_post_with_an_img_can_not_be_updated_by_admin_user(): void
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

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $image = UploadedFile::fake()->image('avatar2.jpg');

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                $image,
            ],
            'deleted_preview' => [
                $oldMediaId
            ]
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $newPostModel = Post::find($oldPostModel->id);
        $newMediaCollection = $newPostModel->getMedia('preview');

        $this->assertEquals(1, $newMediaCollection->count());
        $newMediaModel = $newMediaCollection->first();

        $newMedia = [
            'id' => $newMediaModel->id,
            'uuid' => $newMediaModel->uuid,
            'name' => $newMediaModel->name,
            'updated_at' => $newMediaModel->updated_at,
        ];

        unset($newPost['imgs']);
        unset($newPost['deleted_preview']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', $newPost);
        $this->assertDatabaseMissing('posts', $oldPost);

        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', $newMedia);
        $this->assertDatabaseMissing('media', ['id' => $oldMediaId]);
    }

    public function test_can_not_update_if_imgs_array_has_not_file()
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

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => ['qwerty']
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The imgs field must contain 0 items. (and 2 more errors)',
                'errors' => [
                    'imgs' => [
                        'The imgs field must contain 0 items.'
                    ],
                    'deleted_preview' => [
                        'The deleted preview field is required.'
                    ],
                    'imgs.0' => [
                        'The imgs.0 field must be a file.'
                    ]
                ]
            ]
        );

        unset($newPost['imgs']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', ['id' => $oldMediaId]);
    }

    public function test_can_not_update_if_imgs_is_not_array()
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

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => 'qwerty'
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The imgs field must be an array. (and 1 more error)',
                'errors' => [
                    'imgs' => [
                        'The imgs field must be an array.',
                        'The imgs field must contain 0 items.'
                    ],
                ]
            ]
        );

        unset($newPost['imgs']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', ['id' => $oldMediaId]);
    }

    public function test_can_not_update_if_imgs_has_one_file_but_deleted_preview_is_empty()
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

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $image = UploadedFile::fake()->image('avatar2.jpg');

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                $image,
            ],
            'deleted_preview' => []
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The imgs field must contain 0 items. (and 1 more error)',
                'errors' => [
                    'imgs' => [
                        'The imgs field must contain 0 items.'
                    ],
                    'deleted_preview' => [
                        'The deleted preview field is required.'
                    ],
                ]
            ]
        );

        unset($newPost['imgs']);
        unset($newPost['deleted_preview']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', ['id' => $oldMediaId]);
    }

    public function test_can_not_update_if_imgs_has_one_file_but_deleted_preview_has_invalid_id()
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

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $image = UploadedFile::fake()->image('avatar2.jpg');

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                $image,
            ],
            'deleted_preview' => [
                9999
            ]
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The selected deleted_preview.0 is invalid.',
                'errors' => [
                    'deleted_preview.0' => [
                        'The selected deleted_preview.0 is invalid.'
                    ],
                ]
            ]
        );

        unset($newPost['imgs']);
        unset($newPost['deleted_preview']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', ['id' => $oldMediaId]);
    }

    public function test_can_not_update_if_imgs_has_one_file_but_deleted_preview_has_not_int()
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

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $image = UploadedFile::fake()->image('avatar2.jpg');

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                $image,
            ],
            'deleted_preview' => [
                'qwerty'
            ]
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The deleted_preview.0 field must be an integer.',
                'errors' => [
                    'deleted_preview.0' => [
                        'The deleted_preview.0 field must be an integer.'
                    ],
                ]
            ]
        );

        unset($newPost['imgs']);
        unset($newPost['deleted_preview']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', ['id' => $oldMediaId]);
    }

    public function test_can_not_update_if_imgs_has_more_than_one_file()
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

        $oldPostModel = Post::factory(1)->create()->each(function ($post) {
            (new testCreateMediaImgAction())($post);
        })->first();

        $oldPost = [
            'title' => $oldPostModel->title,
            'body' => $oldPostModel->body,
        ];

        $image = UploadedFile::fake()->image('avatar2.jpg');
        $image2 = UploadedFile::fake()->image('avatar22.jpg');

        $oldMediaId = $oldPostModel->getMedia('preview')->first()->id;

        $newPost = [
            'title' => 'some text',
            'body' => 'some text',
            'imgs' => [
                $image,
                $image2,
            ],
            'deleted_preview' => [
                $oldMediaId
            ]
        ];

        $response = $this->actingAs($user)->patch('/api/posts2/'.$oldPostModel->id, $newPost);

        $response->assertStatus(422);
        $response->assertJsonFragment(
            [
                'message' => 'The imgs field must contain 1 items.',
                'errors' => [
                    'imgs' => [
                        'The imgs field must contain 1 items.'
                    ],
                ]
            ]
        );

        unset($newPost['imgs']);
        unset($newPost['deleted_preview']);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', $oldPost);
        $this->assertDatabaseMissing('posts', $newPost);
        $this->assertDatabaseCount('media', 1);
        $this->assertDatabaseHas('media', ['id' => $oldMediaId]);
    }
}