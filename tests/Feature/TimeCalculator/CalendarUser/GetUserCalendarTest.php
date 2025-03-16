<?php

namespace Tests\Feature\TimeCalculator\CalendarUser;

use App\Models\Calendar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GetUserCalendarTest extends TestCase
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

    public function test_can_not_get_user_calendar_by_unauthorised_user(): void
    {
        $response = $this->get('/api/getUserCalendar/1');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_get_user_calendar_by_not_admin_user(): void
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
        $response = $this->actingAs($user)->get('/api/getUserCalendar/'.$user->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_get_user_calendar_by_admin_user(): void
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/getUserCalendar/'.$user->id);

        $response->assertStatus(200);
        $response->assertJson(['calendar_id' => $calendar->id]);
    }

    public function test_get_null_user_calendar_by_admin_user(): void
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/getUserCalendar/'.$user->id);

        $response->assertStatus(200);
        $response->assertJson(['calendar_id' => null]);
    }
}
