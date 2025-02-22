<?php

namespace Tests\Feature\TimeCalculator\unsetCalendarUser;

use App\Models\Calendar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UnsetTest extends TestCase
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

    public function test_can_not_unset_calendar_user_by_unauthorised_user(): void
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_dmin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);
    
        $calendarArray = [
            'title' => Str::random(10),
        ];
        $calendar = Calendar::create($calendarArray);

        $sync = [
            'user' => $user->id,
        ];
        $response = $this->post('/api/unsetCalendarUser', $sync);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_unset_calendar_user_by_not_admin_user(): void
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_dmin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $sync = [
            'user' => $user->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/unsetCalendarUser', $sync);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_user_id_attribute_is_required_for_unset_calendar_user()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $sync = [
            'user' => $user->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/unsetCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('user_id')
            ->assertJsonValidationErrors([
                'user_id' => 'The user id field is required.',
            ]);
    }

    public function test_user_id_attribute_is_integer_for_unset_calendar_user()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $sync = [
            'user_id' => [$user->id],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/unsetCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('user_id')
            ->assertJsonValidationErrors([
                'user_id' => 'The user id field must be an integer.',
            ]);
    }

    public function test_user_id_attribute_exists_for_unset_calendar_user()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $sync = [
            'user_id' => $user->id * 2,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/unsetCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('user_id')
            ->assertJsonValidationErrors([
                'user_id' => 'The selected user id is invalid.',
            ]);
    }

    public function test_calendar_user_can_unset_by_admin_user(): void
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'Admin',
                'discription' => Str::random(10),
                'created_at' => null,
                'updated_at' => null,
            ]
        );
        $user = User::factory()->create();
        $user->roles()->sync($role->id);

        $calendarArray = [
            'title' => Str::random(10),
        ];
        $calendar = Calendar::create($calendarArray);

        $user->calendar_id = $calendar->id;
        $user->save();

        $sync = [
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/unsetCalendarUser', $sync);

        $response->assertStatus(204);
        $sync['id'] = $sync['user_id'];
        unset($sync['user_id']);
        $this->assertDatabaseMissing('users', $sync);
    }
}
