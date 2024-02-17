<?php

namespace Tests\Feature\Message;

use App\Models\BanChat;
use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
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

    public function test_a_message_can_not_be_stored_by_unauthorised_user(): void
    {
        $chat_id = mt_rand(1, 9);
        $message = [
            'body' => Str::random(30),
            'chat_id' => $chat_id,
        ];
        $response = $this->post('/api/messages/'.$chat_id, $message);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseMissing('messages', $message);
    }

    public function test_a_message_can_not_be_stored_by_not_owner_user()
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

        $message = [
            'body' => Str::random(30),
            'chat_id' => Str::random(1),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user3)->post('/api/messages/'.$createdChat->id, $message);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'You are not the owner of this chat.',
            ]
        );
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseMissing('messages', $message);
    }

    public function test_body_attribute_is_required_for_storing_message()
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

        $message = [
            'chat_id' => $createdChat->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.$createdChat->id, $message);

        $response
            ->assertStatus(422)
            ->assertInvalid('body')
            ->assertJsonValidationErrors([
                'body' => 'The body field is required.',
            ]);
    }

    public function test_title_attribute_is_string_for_storing_message()
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

        $message = [
            'body' => [$createdChat->id],
            'chat_id' => $createdChat->id,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.$createdChat->id, $message);

        $response
            ->assertStatus(422)
            ->assertInvalid('body')
            ->assertJsonValidationErrors([
                'body' => 'The body field must be a string.',
            ]);
    }

    public function test_chat_id_attribute_is_required_for_storing_message()
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

        $message = [
            'body' => Str::random(30),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.$createdChat->id, $message);

        $response
            ->assertStatus(422)
            ->assertInvalid('chat_id')
            ->assertJsonValidationErrors([
                'chat_id' => 'The chat id field is required.',
            ]);
    }

    public function test_chat_id_attribute_is_integer_for_storing_message()
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

        $message = [
            'body' => Str::random(30),
            'chat_id' => [$createdChat->id],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.$createdChat->id, $message);

        $response
            ->assertStatus(422)
            ->assertInvalid('chat_id')
            ->assertJsonValidationErrors([
                'chat_id' => 'The chat id field must be an integer.',
            ]);
    }

    public function test_chat_id_attribute_exists_for_storing_message()
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

        $message = [
            'body' => Str::random(30),
            'chat_id' => $createdChat->id * 10,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.$createdChat->id, $message);

        $response
            ->assertStatus(422)
            ->assertInvalid('chat_id')
            ->assertJsonValidationErrors([
                'chat_id' => 'The selected chat id is invalid.',
            ]);
    }

    public function test_send_correct_message_for_storing_message()
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

        $message = [
            'body' => Str::random(30),
            'chat_id' => $createdChat->id,
        ];

        $expectedJson = [
            'body' => $message['body'],
            'chat_id' => $message['chat_id'],
            'user_name' => $user1->name,
            'is_owner' => true,
        ];

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_users', 0);
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.$createdChat->id, $message);

        $response->assertStatus(200);
        $response->assertJson($expectedJson);
        $this->assertDatabaseCount('messages', 1);
        $this->assertDatabaseCount('message_users', 2);
    }

    public function test_send_correct_message_for_storing_if_user_has_ban_chat()
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

        BanChat::create(['user_id' => $user1->id]);
        $message = [
            'body' => Str::random(30),
            'chat_id' => $createdChat->id,
        ];

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_users', 0);
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.$createdChat->id, $message);

        $response->assertStatus(423);
        $response->assertJsonFragment(
            [
                'message' => 'You can\'t do this. You are blocked.',
            ]
        );
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_users', 0);
    }

    public function test_send_correct_message_for_invalid_chat()
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

        $message = [
            'body' => Str::random(30),
            'chat_id' => $createdChat->id,
        ];

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_users', 0);
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->post('/api/messages/'.($createdChat->id * 10), $message);

        $response->assertStatus(404);
        $response->assertJson(
            [
                'message' => 'No query results for model [App\\Models\\Chat] '.($createdChat->id * 10),
            ]
        );
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_users', 0);
    }
}
