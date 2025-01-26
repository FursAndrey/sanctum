<?php

namespace Tests\Feature\TimeCalculator\Calendar;

use App\Models\Calendar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
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

    public function test_a_calendar_can_not_be_stored_by_unauthorised_user(): void
    {
        $calendar = [
            'title' => Str::random(10),
        ];
        $response = $this->post('/api/calendars', $calendar);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('calendars', 0);
        $this->assertDatabaseMissing('calendars', $calendar);
    }

    public function test_a_calendar_can_not_be_stored_by_not_admin_user(): void
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

        $calendar = [
            'title' => Str::random(10),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendars', $calendar);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('calendars', 0);
        $this->assertDatabaseMissing('calendars', $calendar);
    }

    public function test_title_attribute_is_required_for_storing_calendar()
    {
        // создание пользователя и присвоение ему роли
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

        $calendar = [
            'title1' => Str::random(10),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendars', $calendar);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field is required.',
            ]);
    }

    public function test_title_attribute_is_string_for_storing_calendar()
    {
        // создание пользователя и присвоение ему роли
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

        $calendar = [
            'title' => ['some array'],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendars', $calendar);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field must be a string.',
            ]);
    }

    public function test_title_attribute_is_max_100_chars_for_storing_calendar()
    {
        // создание пользователя и присвоение ему роли
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

        $calendar = [
            'title' => Str::random(101),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendars', $calendar);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title field must not be greater than 100 characters.',
            ]);
    }

    public function test_title_attribute_is_unique_for_storing_calendar()
    {
        // создание пользователя и присвоение ему роли
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

        $calendar = [
            'title' => Str::random(10),
        ];

        Calendar::create($calendar);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendars', $calendar);

        $response
            ->assertStatus(422)
            ->assertInvalid('title')
            ->assertJsonValidationErrors([
                'title' => 'The title has already been taken.',
            ]);
    }

    public function test_a_calendar_can_be_stored_by_admin_user(): void
    {
        // создание пользователя и присвоение ему роли
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

        $calendar = [
            'title' => Str::random(10),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendars', $calendar);

        $response->assertStatus(201);
        $this->assertDatabaseCount('calendars', 1);
        $this->assertDatabaseHas('calendars', $calendar);
    }
}
