<?php

namespace Tests\Feature\TimeCalculator\Holyday;

use App\Models\Holyday;
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

    public function test_can_not_return_holydays_for_unauthorised_user(): void
    {
        // тестируемый запрос от имени пользователя
        $response = $this->get('/api/holydays');

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
    }

    public function test_can_not_return_holydays_for_not_admin_user(): void
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
        $response = $this->actingAs($user)->get('/api/holydays');

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
    }

    public function test_can_return_holydays_for_admin_user(): void
    {
        $holyday1 = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $indexHolyday1 = Holyday::create($holyday1);
        $holyday2 = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $indexHolyday2 = Holyday::create($holyday2);

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
        $response = $this->actingAs($user)->get('/api/holydays');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'data' => [
                    [
                        'id' => $indexHolyday1->id,
                        'holyday' => $indexHolyday1->holyday->format('Y-m-d'),
                    ],
                    [
                        'id' => $indexHolyday2->id,
                        'holyday' => $indexHolyday2->holyday->format('Y-m-d'),
                    ],
                ],
            ]
        );
    }
}
