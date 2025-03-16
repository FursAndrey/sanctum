<?php

namespace Tests\Feature\TimeCalculator;

use App\Models\Calendar;
use App\Models\CalendarDay;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventGenerateTest extends TestCase
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

    public function test_can_not_generation_events_by_unauthorised_user(): void
    {
        $response = $this->get('/api/eventGenerate');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_generation_events_by_not_admin_user(): void
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/eventGenerate');

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_generation_events_by_admin_user(): void
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

        $user2 = User::factory()->create();
        $user2->roles()->sync($role->id);

        $calendarArray = [
            'title' => Str::random(10),
        ];
        $calendar = Calendar::create($calendarArray);

        $user->calendar_id = $calendar->id;
        $user->save();

        $calendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => 1,
            'work_start' => Carbon::now()->subSeconds(rand(60 * 60, 3 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->addSeconds(rand(3 * 60 * 60, 6 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->addSeconds(rand(2 * 60 * 60, 2.5 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->addSeconds(rand(2.5 * 60 * 60, 3 * 60 * 60))->format('H:i:s'),
        ];
        CalendarDay::create($calendarDay);

        $calendarDay = [
            'calendar_id' => $calendar->id,
            'month_day_id' => 2,
            'work_start' => Carbon::now()->subSeconds(rand(60 * 60, 3 * 60 * 60))->format('H:i:s'),
            'work_end' => Carbon::now()->addSeconds(rand(3 * 60 * 60, 6 * 60 * 60))->format('H:i:s'),
            'lunch_start' => Carbon::now()->addSeconds(rand(2 * 60 * 60, 2.5 * 60 * 60))->format('H:i:s'),
            'lunch_end' => Carbon::now()->addSeconds(rand(2.5 * 60 * 60, 3 * 60 * 60))->format('H:i:s'),
        ];
        CalendarDay::create($calendarDay);

        $createdEvent = [
            'user_id' => $user->id,
            'month_day_id' => 1,
            'event_type' => 'enter',
            'event_time' => Carbon::now()->subSeconds(rand(60, 60 * 60))->format('H:i:s'),
        ];
        Event::create($createdEvent);
        $createdEvent = [
            'user_id' => $user->id,
            'month_day_id' => 1,
            'event_type' => 'exit',
            'event_time' => Carbon::now()->subSeconds(rand(60 * 60, 2 * 60 * 60))->format('H:i:s'),
        ];
        Event::create($createdEvent);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/eventGenerate');

        $response->assertStatus(200);
    }
}
