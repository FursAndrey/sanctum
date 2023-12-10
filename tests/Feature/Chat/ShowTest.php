<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShowTest extends TestCase
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

    public function test_can_not_return_chat_by_id_for_unauthorised_user()
    {
        //создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        //тестируемый запрос от имени пользователя
        $response = $this->get('/api/chats/'.$createdChat->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_return_chat_by_id_for_not_owner_user()
    {
        //создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        //создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user3)->get('/api/chats/'.$createdChat->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'You are not the owner of this chat.',
            ]
        );
    }

    public function test_can_return_chat_by_id_for_owher_user_without_messages()
    {
        //создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        $expectedJson = [
            'id' => $createdChat->id,
            'title' => 'With '.$user2->name,
            'users' => $user1->id.'-'.$user2->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->get('/api/chats/'.$createdChat->id);

        $response->assertStatus(200);
        //точное соответствие
        $response->assertExactJson($expectedJson);
    }

    public function test_can_return_chat_by_id_for_owher_user_with_messages()
    {
        //создание пользователей чатов
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //создание чата
        $chat = [
            'title' => null,
            'users' => $user1->id.'-'.$user2->id,
        ];
        $createdChat = Chat::create($chat);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user1->id]);
        ChatUser::create(['chat_id' => $createdChat->id, 'user_id' => $user2->id]);

        //создание сообщений для чатов
        $message1 = Message::create([
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);
        $message2 = Message::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);

        $expectedJson = [
            'id' => $createdChat->id,
            'title' => 'With '.$user2->name,
            'users' => $user1->id.'-'.$user2->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->get('/api/chats/'.$createdChat->id);

        $response->assertStatus(200);
        //точное соответствие
        $response->assertExactJson($expectedJson);
    }
}
