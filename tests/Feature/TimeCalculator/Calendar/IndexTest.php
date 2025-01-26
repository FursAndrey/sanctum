<?php

namespace Tests\Feature\TimeCalculator\Calendar;

use App\Models\Calendar;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IndexTest extends TestCase
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

    public function test_can_not_return_calendars_for_unauthorised_user(): void
    {
        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/calendars');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_return_calendars_for_not_admin_user(): void
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/calendars');

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_can_return_calendars_for_admin_user(): void
    {
        $calendar1 = [
            'title' => Str::random(10),
        ];
        $indexCalendar1 = Calendar::create($calendar1);
        $calendar2 = [
            'title' => Str::random(10),
        ];
        $indexCalendar2 = Calendar::create($calendar2);

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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->get('/api/calendars');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => $indexCalendar1->id,
                        'title' => $indexCalendar1->title,
                    ],
                    [
                        'id' => $indexCalendar2->id,
                        'title' => $indexCalendar2->title,
                    ],
                ],
            ]
        );
    }
}
