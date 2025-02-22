<?php

namespace Tests\Feature\TimeCalculator\setCalendarUser;

use App\Models\Calendar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SetTest extends TestCase
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

    public function test_can_not_set_calendar_user_by_unauthorised_user(): void
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
            'calendar' => $calendar->id,
        ];
        $response = $this->post('/api/setCalendarUser', $sync);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_set_calendar_user_by_not_admin_user(): void
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
            'calendar' => $calendar->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_user_id_attribute_is_required_for_set_calendar_user()
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

        $sync = [
            'calendar_id' => $calendar->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('user_id')
            ->assertJsonValidationErrors([
                'user_id' => 'The user id field is required.',
            ]);
    }

    public function test_calendar_id_attribute_is_required_for_set_calendar_user()
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
            'user_id' => $user->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('calendar_id')
            ->assertJsonValidationErrors([
                'calendar_id' => 'The calendar id field is required.',
            ]);
    }

    public function test_calendar_id_attribute_is_integer_for_set_calendar_user()
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
            'user_id' => $user->id,
            'calendar_id' => [123],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('calendar_id')
            ->assertJsonValidationErrors([
                'calendar_id' => 'The calendar id field must be an integer.',
            ]);
    }

    public function test_user_id_attribute_is_integer_for_set_calendar_user()
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

        $sync = [
            'user_id' => [$user->id],
            'calendar_id' => $calendar->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('user_id')
            ->assertJsonValidationErrors([
                'user_id' => 'The user id field must be an integer.',
            ]);
    }

    public function test_calendar_id_attribute_exists_for_set_calendar_user()
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

        $sync = [
            'user_id' => $user->id,
            'calendar_id' => $calendar->id * 2,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('calendar_id')
            ->assertJsonValidationErrors([
                'calendar_id' => 'The selected calendar id is invalid.',
            ]);
        $sync['id'] = $sync['user_id'];
        unset($sync['user_id']);
        $this->assertDatabaseMissing('users', $sync);
    }

    public function test_user_id_attribute_exists_for_set_calendar_user()
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

        $sync = [
            'user_id' => $user->id * 2,
            'calendar_id' => $calendar->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response
            ->assertStatus(422)
            ->assertInvalid('user_id')
            ->assertJsonValidationErrors([
                'user_id' => 'The selected user id is invalid.',
            ]);
        $sync['id'] = $sync['user_id'];
        unset($sync['user_id']);
        $this->assertDatabaseMissing('users', $sync);
    }

    public function test_calendar_user_can_set_by_admin_user(): void
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

        $sync = [
            'user_id' => $user->id,
            'calendar_id' => $calendar->id,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/setCalendarUser', $sync);

        $response->assertStatus(200);
        $sync['id'] = $sync['user_id'];
        unset($sync['user_id']);
        $this->assertDatabaseHas('users', $sync);
    }
}
