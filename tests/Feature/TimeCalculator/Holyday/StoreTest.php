<?php

namespace Tests\Feature\TimeCalculator\Holyday;

use App\Models\Holyday;
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

    public function test_a_holyday_can_not_be_stored_by_unauthorised_user(): void
    {
        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];
        $response = $this->post('/api/holydays', $holyday);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('holydays', 0);
        $this->assertDatabaseMissing('holydays', $holyday);
    }

    public function test_a_holyday_can_not_be_stored_by_not_admin_user(): void
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

        $holyday = [
            'holyday' => Carbon::today()->addDays(rand(1, 365)),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/holydays', $holyday);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('holydays', 0);
        $this->assertDatabaseMissing('holydays', $holyday);
    }

    public function test_holyday_attribute_is_required_for_storing_holyday()
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
            'holyday1' => Carbon::today()->addDays(rand(1, 365)),
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/holydays', $holyday);

        $response
            ->assertStatus(422)
            ->assertInvalid('holyday')
            ->assertJsonValidationErrors([
                'holyday' => 'The holyday field is required.',
            ]);
    }

    public function test_holyday_attribute_is_date_for_storing_holyday()
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
            'holyday' => ['some array'],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/holydays', $holyday);

        $response
            ->assertStatus(422)
            ->assertInvalid('holyday')
            ->assertJsonValidationErrors([
                'holyday' => 'The holyday field must be a valid date.',
            ]);
    }

    public function test_holyday_attribute_is_unique_for_storing_holyday()
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

        Holyday::create($holyday);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/holydays', $holyday);

        $response
            ->assertStatus(422)
            ->assertInvalid('holyday')
            ->assertJsonValidationErrors([
                'holyday' => 'The holyday has already been taken.',
            ]);
    }

    public function test_a_holyday_can_be_stored_by_admin_user(): void
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

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/holydays', $holyday);

        $response->assertStatus(201);
        $this->assertDatabaseCount('holydays', 1);
        $this->assertDatabaseHas('holydays', $holyday);
    }
}
