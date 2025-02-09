<?php

namespace Tests\Feature\TimeCalculator\Holyday;

use App\Models\Holyday;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DestroyTest extends TestCase
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

    public function test_a_holyday_can_not_be_deleted_by_unauthorised_user()
    {
        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $delHolyday = Holyday::create($holyday);

        // тестируемый запрос от имени пользователя
        $response = $this->delete('/api/holydays/'.$delHolyday->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('holydays', $holyday);
    }

    public function test_a_holyday_can_not_be_deleted_by_not_admin_user()
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

        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $delHolyday = Holyday::create($holyday);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/holydays/'.$delHolyday->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('holydays', $holyday);
    }

    public function test_a_holyday_can_be_deleted_by_admin_user()
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

        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $delHolyday = Holyday::create($holyday);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/holydays/'.$delHolyday->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('holydays', $holyday);
    }
}
