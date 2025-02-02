<?php

namespace Tests\Feature\TimeCalculator\CalendarDay;

use App\Models\Calendar;
use App\Models\CalendarDay;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
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

    public function test_a_calendar_day_can_not_be_updated_by_unauthorised_user()
    {
        $calendar = [
            'title' => Str::random(10),
        ];
        $calendar = Calendar::create($calendar);

        $oldCalendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];
        $oldCalendar = CalendarDay::create($oldCalendarDay);

        $newCalendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->put('/api/calendarDays/'.$oldCalendar->id, $newCalendarDay);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('calendar_days', $oldCalendarDay);
        $this->assertDatabaseMissing('calendar_days', $newCalendarDay);
    }

    public function test_a_calendar_day_can_not_be_updated_by_not_admin_user()
    {
        // создание пользователя и присвоение ему роли
        $role = Role::create(
            [
                'title' => 'not_admin',
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
        $calendar = Calendar::create($calendar);

        $oldCalendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];
        $oldCalendar = CalendarDay::create($oldCalendarDay);

        $newCalendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/calendarDays/'.$oldCalendar->id, $newCalendarDay);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('calendar_days', $oldCalendarDay);
        $this->assertDatabaseMissing('calendar_days', $newCalendarDay);
    }

    public function test_a_calendar_day_can_not_be_updated_by_admin_user()
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
        $calendar = Calendar::create($calendar);

        $oldCalendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];
        $oldCalendar = CalendarDay::create($oldCalendarDay);

        $newCalendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/calendarDays/'.$oldCalendar->id, $newCalendarDay);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('calendar_days', $oldCalendarDay);
        $this->assertDatabaseMissing('calendar_days', $newCalendarDay);
    }
}
