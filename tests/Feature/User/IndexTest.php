<?php

namespace Tests\Feature\User;

use App\Models\BanChat;
use App\Models\BanComment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
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

    public function test_can_not_return_users_for_unauthorised_user(): void
    {
        //тестируемый запрос от имени пользователя
        $response = $this->get('/api/users');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_return_users_for_not_admin_user(): void
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

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users');

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_can_return_users_for_admin_user(): void
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

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'created' => $user->created,
                        'ban_chat' => null,
                        'ban_comment' => null,
                        'roles' => [
                            [
                                'id' => $role->id,
                                'title' => $role->title,
                                'discription' => $role->discription,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public function test_can_return_users_for_admin_user_if_ban_one_of_them(): void
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

        $user2 = User::factory()->create();
        $user2->roles()->sync($role->id);
        $ban_chat = BanChat::create(['user_id' => $user2->id]);
        $ban_comment = BanComment::create(['user_id' => $user2->id]);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'created' => $user->created,
                        'ban_chat' => null,
                        'ban_comment' => null,
                        'roles' => [
                            [
                                'id' => $role->id,
                                'title' => $role->title,
                                'discription' => $role->discription,
                            ],
                        ],
                    ],
                    [
                        'id' => $user2->id,
                        'name' => $user2->name,
                        'email' => $user2->email,
                        'created' => $user2->created,
                        'ban_chat' => $ban_chat->created_at->format('d.m.Y H:i:s'),
                        'ban_comment' => $ban_comment->created_at->format('d.m.Y H:i:s'),
                        'roles' => [
                            [
                                'id' => $role->id,
                                'title' => $role->title,
                                'discription' => $role->discription,
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
