<?php

namespace Tests\Feature\User;

use App\Models\BanChat;
use App\Models\BanComment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShowTest extends TestCase
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

    public function test_can_not_return_user_by_id_for_unauthorised_user()
    {
        $user = User::factory()->create();

        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/users/'.$user->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_not_admin_user_can_see_himself_by_id()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users/'.$user->id);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
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
            ]
        );
    }

    public function test_not_admin_user_can_see_himself_by_id_if_ban()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);
        $ban_chat = BanChat::create(['user_id' => $user->id]);
        $ban_comment = BanComment::create(['user_id' => $user->id]);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users/'.$user->id);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created' => $user->created,
                    'ban_chat' => $ban_chat->created_at->format('d.m.Y H:i:s'),
                    'ban_comment' => $ban_comment->created_at->format('d.m.Y H:i:s'),
                    'has_ban_chat' => true,
                    'has_ban_comment' => true,
                    'roles' => [
                        [
                            'id' => $role->id,
                            'title' => $role->title,
                            'discription' => $role->discription,
                        ],
                    ],
                ],
            ]
        );
    }

    public function test_not_admin_user_can_not_see_another_user_by_id()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $anotherUser = User::factory()->create();
        $anotherUser->roles()->sync($role->id);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users/'.$anotherUser->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_can_return_user_by_id_for_admin_user()
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/users/'.$user->id);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created' => $user->created,
                    'ban_chat' => null,
                    'ban_comment' => null,
                    'has_ban_chat' => false,
                    'has_ban_comment' => false,
                    'roles' => [
                        [
                            'id' => $role->id,
                            'title' => $role->title,
                            'discription' => $role->discription,
                        ],
                    ],
                ],
            ]
        );
    }
}
