<?php

namespace Tests\Feature\MessageUser;

use App\Models\Chat;
use App\Models\ChatUser;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_message_id_attribute_is_required_for_change_message_status()
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
        $message = Message::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);

        $messageStatusForUser1 = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ];
        MessageUser::create($messageStatusForUser1);

        $unreadable = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => false,
        ];
        $readable = [
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => true,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->put('/api/messageUsers/'.$createdChat->id, ['message_id1' => $message->id]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'message_id' => 'The message id field is required.',
            ]);
        $this->assertDatabaseHas('message_users', $unreadable);
        $this->assertDatabaseMissing('message_users', $readable);
    }

    public function test_message_id_attribute_is_numeric_for_change_message_status()
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
        $message = Message::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);

        $messageStatusForUser1 = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ];
        MessageUser::create($messageStatusForUser1);

        $unreadable = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => false,
        ];
        $readable = [
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => true,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->put('/api/messageUsers/'.$createdChat->id, ['message_id' => [$message->id]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'message_id' => 'The message id field must be an integer.',
            ]);
        $this->assertDatabaseHas('message_users', $unreadable);
        $this->assertDatabaseMissing('message_users', $readable);
    }

    public function test_message_id_attribute_exists_for_change_message_status()
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
        $message = Message::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);

        $messageStatusForUser1 = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ];
        MessageUser::create($messageStatusForUser1);

        $unreadable = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => false,
        ];
        $readable = [
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => true,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->put('/api/messageUsers/'.$createdChat->id, ['message_id' => $message->id * 10]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'message_id' => 'The selected message id is invalid.',
            ]);
        $this->assertDatabaseHas('message_users', $unreadable);
        $this->assertDatabaseMissing('message_users', $readable);
    }

    public function test_message_status_can_change_if_message_id_attribute_is_correct()
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
        $message = Message::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);

        $messageStatusForUser1 = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ];
        MessageUser::create($messageStatusForUser1);

        $unreadable = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => false,
        ];
        $readable = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => true,
        ];

        $this->assertDatabaseHas('message_users', $unreadable);
        $this->assertDatabaseMissing('message_users', $readable);

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user1)->put('/api/messageUsers/'.$createdChat->id, ['message_id' => $message->id]);

        $response->assertStatus(204);
        $this->assertDatabaseHas('message_users', $readable);
        $this->assertDatabaseMissing('message_users', $unreadable);
    }

    public function test_message_status_can_not_be_updated_by_authorised_user_but_not_owner_of_chat()
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

        //создание сообщений для чатов
        $message = Message::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);

        $messageStatusForUser1 = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ];
        MessageUser::create($messageStatusForUser1);

        $unreadable = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => false,
        ];
        $readable = [
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => true,
        ];
        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user3)->put('/api/messageUsers/'.$createdChat->id, ['message_id' => $message->id]);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'You are not the owner of this chat.',
            ]
        );
        $this->assertDatabaseHas('message_users', $unreadable);
        $this->assertDatabaseMissing('message_users', $readable);
    }

    public function test_message_status_can_not_be_updated_by_unauthorised_user()
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

        //создание сообщений для чатов
        $message = Message::create([
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'body' => Str::random(30),
        ]);

        $messageStatusForUser1 = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
        ];
        MessageUser::create($messageStatusForUser1);

        $unreadable = [
            'user_id' => $user1->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => false,
        ];
        $readable = [
            'user_id' => $user2->id,
            'chat_id' => $createdChat->id,
            'message_id' => $message->id,
            'is_read' => true,
        ];
        //тестируемый запрос от имени пользователя
        $response = $this->put('/api/messageUsers/'.$createdChat->id, ['message_id' => $message->id]);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('message_users', $unreadable);
        $this->assertDatabaseMissing('message_users', $readable);
    }
}
