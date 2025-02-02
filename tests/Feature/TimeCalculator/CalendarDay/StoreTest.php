<?php

namespace Tests\Feature\TimeCalculator\CalendarDay;

use App\Models\Calendar;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
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

    public function test_a_calendar_day_can_not_be_stored_by_unauthorised_user(): void
    {
        $calendarDay = [
            'calendar_id' => Str::random(10),
        ];
        $response = $this->post('/api/calendarDays', $calendarDay);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('calendar_days', 0);
        $this->assertDatabaseMissing('calendar_days', $calendarDay);
    }

    public function test_a_calendar_day_can_not_be_stored_by_not_admin_user(): void
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('calendar_days', 0);
        $this->assertDatabaseMissing('calendar_days', $calendarDay);
    }

    public function test_calendar_id_attribute_is_required_for_storing_calendar_day()
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

        $calendarDay = [
            'calendar_id1' => Str::random(10),
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('calendar_id')
            ->assertJsonValidationErrors([
                'calendar_id' => 'The calendar id field is required.',
            ]);
    }

    public function test_calendar_id_attribute_is_integer_for_storing_calendar_day()
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

        $calendarDay = [
            'calendar_id' => Str::random(10),
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('calendar_id')
            ->assertJsonValidationErrors([
                'calendar_id' => 'The calendar id field must be an integer.',
            ]);
    }

    public function test_calendar_id_attribute_exists_in_calendars_table_for_storing_calendar_day()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id + mt_rand(1, 7),
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('calendar_id')
            ->assertJsonValidationErrors([
                'calendar_id' => 'The selected calendar id is invalid.',
            ]);
    }

    public function test_month_day_id_attribute_is_required_for_storing_calendar_day()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('month_day_id')
            ->assertJsonValidationErrors([
                'month_day_id' => 'The month day id field is required.',
            ]);
    }

    public function test_month_day_id_attribute_is_integer_for_storing_calendar_day()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => Str::random(10),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('month_day_id')
            ->assertJsonValidationErrors([
                'month_day_id' => 'The month day id field must be an integer.',
            ]);
    }

    public function test_work_start_attribute_is_required_for_storing_calendar_day()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('work_start')
            ->assertJsonValidationErrors([
                'work_start' => 'The work start field is required.',
            ]);
    }

    public function test_work_end_attribute_is_required_for_storing_calendar_day()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('work_end')
            ->assertJsonValidationErrors([
                'work_end' => 'The work end field is required.',
            ]);
    }

    public function test_lunch_start_attribute_is_required_for_storing_calendar_day()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('lunch_start')
            ->assertJsonValidationErrors([
                'lunch_start' => 'The lunch start field is required.',
            ]);
    }

    public function test_lunch_end_attribute_is_required_for_storing_calendar_day()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('lunch_end')
            ->assertJsonValidationErrors([
                'lunch_end' => 'The lunch end field is required.',
            ]);
    }

    public function test_work_start_attribute_is_time_format_his_for_storing_calendar()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => '55:86:70',
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('work_start')
            ->assertJsonValidationErrors([
                'work_start' => 'The work start field must match the format H:i:s.',
            ]);
    }

    public function test_work_end_attribute_is_time_format_his_for_storing_calendar()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => '55:86:70',
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('work_end')
            ->assertJsonValidationErrors([
                'work_end' => 'The work end field must match the format H:i:s.',
            ]);
    }

    public function test_lunch_start_attribute_is_time_format_his_for_storing_calendar()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => '55:86:70',
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('lunch_start')
            ->assertJsonValidationErrors([
                'lunch_start' => 'The lunch start field must match the format H:i:s.',
            ]);
    }

    public function test_lunch_end_attribute_is_time_format_his_for_storing_calendar()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => '55:86:70',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('lunch_end')
            ->assertJsonValidationErrors([
                'lunch_end' => 'The lunch end field must match the format H:i:s.',
            ]);
    }

    public function test_control_start_attribute_is_time_format_his_for_storing_calendar()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'control_start' => 1,
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('control_start')
            ->assertJsonValidationErrors([
                'control_start' => 'The control start field must match the format H:i:s.',
            ]);
    }

    public function test_control_end_attribute_is_time_format_his_for_storing_calendar()
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'control_end' => '55:86:70',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response
            ->assertStatus(422)
            ->assertInvalid('control_end')
            ->assertJsonValidationErrors([
                'control_end' => 'The control end field must match the format H:i:s.',
            ]);
    }

    public function test_a_calendar_day_can_be_stored_by_admin_user(): void
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
        $createdCalendar = Calendar::create($calendar);

        $calendarDay = [
            'calendar_id' => $createdCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'control_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'control_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/calendarDays', $calendarDay);

        $response->assertStatus(201);
        $this->assertDatabaseCount('calendar_days', 1);
        $this->assertDatabaseHas('calendar_days', $calendarDay);
    }
}
