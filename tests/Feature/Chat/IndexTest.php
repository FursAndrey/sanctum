<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndexTest extends TestCase
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

    public function test_can_not_return_chats_for_unauthorised_user(): void
    {
        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/chats');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_return_chats_for_authorised_user(): void
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
                'discription' => 'Creator of other site',
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        // создание других пользователей чатов
        $anotherUser1 = User::factory()->create();
        $anotherUser2 = User::factory()->create();

        // создание чатов
        $chat1 = [
            'title' => null,
            'users' => $user->id.'-'.$anotherUser1->id,
        ];
        $createdChat1 = Chat::create($chat1);
        ChatUser::create(['chat_id' => $createdChat1->id, 'user_id' => $user->id]);
        ChatUser::create(['chat_id' => $createdChat1->id, 'user_id' => $anotherUser1->id]);

        $chat2 = [
            'title' => null,
            'users' => $user->id.'-'.$anotherUser2->id,
        ];
        $createdChat2 = Chat::create($chat2);
        ChatUser::create(['chat_id' => $createdChat2->id, 'user_id' => $user->id]);
        ChatUser::create(['chat_id' => $createdChat2->id, 'user_id' => $anotherUser2->id]);

        // создание сообщений для чатов
        $message1 = Message::create([
            'user_id' => $user->id,
            'chat_id' => $createdChat1->id,
            'body' => Str::random(30),
        ]);
        $message2 = Message::create([
            'user_id' => $anotherUser2->id,
            'chat_id' => $createdChat2->id,
            'body' => Str::random(30),
        ]);

        /** чаты сортируются в порядке создания сообщенй, новые сообщения поднимают чат выше в списке */
        $expectedJson = [
            [
                'title' => 'With '.$anotherUser2->name,
                'users' => $user->id.'-'.$anotherUser2->id,
                'last_message' => [
                    'id' => $message2->id,
                    'body' => $message2->body,
                    'chat_id' => $message2->chat_id,
                    'user_name' => $message2->user->name,
                    'time' => $message2->created_at->format('d.m.Y H:i:s'),
                    'is_owner' => false,
                ],
                'unreadable_messages_count' => 0,
            ],
            [
                'title' => 'With '.$anotherUser1->name,
                'users' => $user->id.'-'.$anotherUser1->id,
                'last_message' => [
                    'id' => $message1->id,
                    'body' => $message1->body,
                    'chat_id' => $message1->chat_id,
                    'user_name' => $message1->user->name,
                    'time' => $message1->created_at->format('d.m.Y H:i:s'),
                    'is_owner' => true,
                ],
                'unreadable_messages_count' => 0,
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/chats');

        $response->assertStatus(200);
        $this->assertDatabaseCount('chats', 2);
        $response->assertJson($expectedJson);
    }
}
