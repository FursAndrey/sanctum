<?php

namespace Tests\Feature\TelegramIdChat;

use App\Actions\Telegram\updateChatIdAction;
use App\Models\PrivateTelegram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_set_chat_id_for_telegram_chat()
    {
        $user = User::factory()->create();

        $testChatId = 2065261178;

        $this->assertDatabaseCount('private_telegrams', 0);

        (new updateChatIdAction())($testChatId, $user->tg_name);

        $this->assertDatabaseCount('private_telegrams', 1);
        $this->assertDatabaseHas('private_telegrams', ['user_id' => $user->id, 'private_chat_id' => $testChatId]);
    }

    public function test_update_chat_id_for_telegram_chat()
    {
        $testChatIdOld = 2065261178;

        $user = User::factory()->create();
        PrivateTelegram::create(['user_id' => $user->id, 'private_chat_id' => $testChatIdOld]);

        $testChatIdNew = 2065261171;

        $this->assertDatabaseCount('private_telegrams', 1);
        $this->assertDatabaseHas('private_telegrams', ['user_id' => $user->id, 'private_chat_id' => $testChatIdOld]);
        $this->assertDatabaseMissing('private_telegrams', ['user_id' => $user->id, 'private_chat_id' => $testChatIdNew]);

        (new updateChatIdAction())($testChatIdNew, $user->tg_name);

        $this->assertDatabaseCount('private_telegrams', 1);
        $this->assertDatabaseHas('private_telegrams', ['user_id' => $user->id, 'private_chat_id' => $testChatIdNew]);
        $this->assertDatabaseMissing('private_telegrams', ['user_id' => $user->id, 'private_chat_id' => $testChatIdOld]);
    }

    public function test_not_update_chat_id_same_id()
    {
        $testChatIdOld = 2065261178;

        $user = User::factory()->create();
        PrivateTelegram::create(['user_id' => $user->id, 'private_chat_id' => $testChatIdOld]);

        $testChatIdNew = $testChatIdOld;

        $this->assertDatabaseCount('private_telegrams', 1);
        $this->assertDatabaseHas('private_telegrams', ['user_id' => $user->id, 'private_chat_id' => $testChatIdOld]);

        (new updateChatIdAction())($testChatIdNew, $user->tg_name);

        $this->assertDatabaseCount('private_telegrams', 1);
        $this->assertDatabaseHas('private_telegrams', ['user_id' => $user->id, 'private_chat_id' => $testChatIdOld]);
    }
}
