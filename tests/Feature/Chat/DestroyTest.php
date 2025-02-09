<?php

namespace Tests\Feature\Chat;

use App\Models\BanChat;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
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

    public function test_can_not_delete_chat_by_id_for_unauthorised_user()
    {
        // создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        // тестируемый запрос от имени пользователя
        $response = $this->delete('/api/chats/'.$createdChat->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('chats', 1);
    }

    public function test_can_not_delete_chat_by_id_for_not_owner_user()
    {
        // создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user3)->delete('/api/chats/'.$createdChat->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'You are not the owner of this chat.',
            ]
        );
        $this->assertDatabaseCount('chats', 1);
    }

    public function test_can_delete_chat_by_id_for_owher_user()
    {
        // создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->delete('/api/chats/'.$createdChat->id);

        $response->assertStatus(204);
        $this->assertDatabaseCount('chats', 0);
    }

    public function test_can_not_delete_chat_by_id_for_owher_user_if_has_ban()
    {
        // создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        BanChat::create(['user_id' => $user1->id]);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->delete('/api/chats/'.$createdChat->id);

        $response->assertStatus(423);
        $response->assertJsonFragment(
            [
                'message' => 'You can\'t do this. You are blocked.',
            ]
        );
        $this->assertDatabaseCount('chats', 1);
    }

    public function test_delete_chat_for_invalid_chat_id()
    {
        // создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->delete('/api/chats/'.($createdChat->id * 10));

        $response->assertStatus(404);
        $response->assertJson(
            [
                'message' => 'No query results for model [App\\Models\\Chat] '.($createdChat->id * 10),
            ]
        );
        $this->assertDatabaseCount('chats', 1);
    }
}
