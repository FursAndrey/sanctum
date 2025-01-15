<?php

namespace Tests\Feature\TimeCalculator\Holyday;

use App\Models\Holyday;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(
            [
                'accept' => 'application/json',
            ]
        );
    }

    public function test_a_holyday_can_not_be_updated_by_unauthorised_user()
    {
        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $oldHolyday = Holyday::create($holyday);

        $newHolyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->put('/api/holydays/'.$oldHolyday->id, $newHolyday);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('holydays', $holyday);
        $this->assertDatabaseMissing('holydays', $newHolyday);
    }

    public function test_a_holyday_can_not_be_updated_by_not_admin_user()
    {
        //создание пользователя и присвоение ему роли
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

        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $oldHolyday = Holyday::create($holyday);

        $newHolyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/holydays/'.$oldHolyday->id, $newHolyday);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('holydays', $holyday);
        $this->assertDatabaseMissing('holydays', $newHolyday);
    }

    public function test_a_holyday_can_not_be_updated_by_admin_user()
    {
        //создание пользователя и присвоение ему роли
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

        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $oldHolyday = Holyday::create($holyday);

        $newHolyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->put('/api/holydays/'.$oldHolyday->id, $newHolyday);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('holydays', $holyday);
        $this->assertDatabaseMissing('holydays', $newHolyday);
    }
}
