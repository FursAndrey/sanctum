<?php

namespace Tests\Feature\TimeCalculator\Calendar;

use App\Models\Calendar;
use App\Models\CalendarDay;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShowTest extends TestCase
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

    public function test_can_not_return_calendar_by_id_for_unauthorised_user()
    {
        $calendar = [
            'title' => Str::random(10),
        ];
        $showCalendar = Calendar::create($calendar);

        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/holydays/'.$showCalendar->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_return_calendar_by_id_for_not_admin_user()
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
        $showCalendar = Calendar::create($calendar);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/calendars/'.$showCalendar->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_can_return_calendar_without_calendar_days_by_id_for_admin_user()
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
        $showCalendar = Calendar::create($calendar);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/calendars/'.$showCalendar->id);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'id' => $showCalendar->id,
                    'title' => $showCalendar->title,
                ],
            ]
        );
    }
    public function test_can_return_calendar_with_calendar_days_by_id_for_admin_user()
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
        $showCalendar = Calendar::create($calendar);

        $calendarDayArray1 = [
            'calendar_id' => $showCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];
        CalendarDay::create($calendarDayArray1);

        $calendarDayArray2 = [
            'calendar_id' => $showCalendar->id,
            'month_day_id' => mt_rand(1, 7),
            'work_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->subSeconds(rand(0, 30 * 24 * 60 * 60))->format('H:i:s'),
        ];
        CalendarDay::create($calendarDayArray2);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/calendars/'.$showCalendar->id);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    'id' => $showCalendar->id,
                    'title' => $showCalendar->title,
                    'calendarDays' => [
                        $calendarDayArray1,
                        $calendarDayArray2,
                    ]
                ],
            ]
        );
    }
}
