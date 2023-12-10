<?php

namespace Tests\Feature\Message;

use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_can_not_return_messages_for_unauthorised_user(): void
    {
        //тестируемый запрос от имени пользователя
        $response = $this->get('/api/messages/1');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_return_messages_for_authorised_user_but_not_owner_chat(): void
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

        //добавление сообщения
        $message = Message::create([
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);
        MessageUser::create([
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ]);
        MessageUser::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ]);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user3)->get('/api/messages/'.$createdChat->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'You are not the owner of this chat.',
            ]
        );
    }

    public function test_can_return_messages_for_owner_user(): void
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

        //добавление сообщения
        $message = Message::create([
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);
        MessageUser::create([
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ]);
        MessageUser::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ]);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->get('/api/messages/'.$createdChat->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(
            [
                'messages' => [
                    [
                        'id' => $message->id,
                        'body' => $message->body,
                        'chat_id' => $message->chat_id,
                        'user_name' => $user1->name,
                        'time' => $message->created_at->format('d.m.Y H:i:s'),
                        'is_owner' => true,
                    ],
                ],
                'lastPage' => 1,
            ]
        );
    }
}
