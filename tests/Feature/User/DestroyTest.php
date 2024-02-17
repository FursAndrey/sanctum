<?php

namespace Tests\Feature\User;

use App\Models\BanChat;
use App\Models\BanComment;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DestroyTest extends TestCase
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

    public function test_user_has_role_and_can_be_deleted_by_admin_user()
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

        //подготовка юзера к удалению
        $deletingUser = User::factory()->create();
        $deletingUserArray = [
            'id' => $deletingUser->id,
            'name' => $deletingUser->name,
            'email' => $deletingUser->email,
        ];
        $deletingUser->roles()->sync($role->id);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/users/'.$deletingUser->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', $deletingUserArray);
    }

    public function test_user_without_bans_can_be_deleted_by_admin_user()
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

        //подготовка юзера к удалению
        $deletingUser = User::factory()->create();
        $deletingUserArray = [
            'id' => $deletingUser->id,
            'name' => $deletingUser->name,
            'email' => $deletingUser->email,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/users/'.$deletingUser->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', $deletingUserArray);
    }

    public function test_user_with_bans_can_be_deleted_by_admin_user()
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

        //подготовка юзера к удалению
        $deletingUser = User::factory()->create();
        $deletingUserArray = [
            'id' => $deletingUser->id,
            'name' => $deletingUser->name,
            'email' => $deletingUser->email,
        ];
        BanChat::create(['user_id' => $deletingUser->id]);
        BanComment::create(['user_id' => $deletingUser->id]);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/users/'.$deletingUser->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', $deletingUserArray);
    }

    public function test_a_role_can_not_be_deleted_by_not_admin_user()
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

        //подготовка юзера к удалению
        $deletingUser = User::factory()->create();
        $deletingUserArray = [
            'id' => $deletingUser->id,
            'name' => $deletingUser->name,
            'email' => $deletingUser->email,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/users/'.$deletingUser->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('users', $deletingUserArray);
    }

    public function test_a_role_can_not_be_deleted_by_unauthorised_user()
    {
        //подготовка юзера к удалению
        $deletingUser = User::factory()->create();
        $deletingUserArray = [
            'id' => $deletingUser->id,
            'name' => $deletingUser->name,
            'email' => $deletingUser->email,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->delete('/api/users/'.$deletingUser->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('users', $deletingUserArray);
    }

    public function test_user_has_chat_and_can_be_deleted_by_admin_user()
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

        //подготовка юзера к удалению
        $deletingUser = User::factory()->create();
        $deletingUserArray = [
            'id' => $deletingUser->id,
            'name' => $deletingUser->name,
            'email' => $deletingUser->email,
        ];

        //создание чата
        $chat = [
            'title' => null,
            'users' => $user->id.'-'.$deletingUser->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $deletingUser->id]);

        //добавление сообщения
        $message1 = Message::create([
            'user_id' => $deletingUser->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);
        MessageUser::create([
            'user_id' => $deletingUser->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message1->id,
        ]);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/users/'.$deletingUser->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', $deletingUserArray);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('chats', 0);
        $this->assertDatabaseCount('chat_users', 0);
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_users', 0);
    }
}
