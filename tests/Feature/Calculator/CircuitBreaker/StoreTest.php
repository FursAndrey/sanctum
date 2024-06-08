<?php

namespace Tests\Feature\Calculator\CircuitBreaker;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
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

    public function test_nominal_attribute_is_required_for_storing_circuit_breaker()
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

        $circuit_breaker = [];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/circuitBreaker', $circuit_breaker);

        $response
            ->assertStatus(422)
            ->assertInvalid('nominal')
            ->assertJsonValidationErrors([
                'nominal' => 'The nominal field is required.',
            ]);
    }

    public function test_nominal_attribute_must_be_a_number_for_storing_circuit_breaker()
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

        $circuit_breaker = [
            'nominal' => 'erte',
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/circuitBreaker', $circuit_breaker);

        $response
            ->assertStatus(422)
            ->assertInvalid('nominal')
            ->assertJsonValidationErrors([
                'nominal' => 'The nominal field must be a number.',
            ]);
    }

    public function test_nominal_attribute_must_be_more_1_for_storing_role()
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

        $circuit_breaker = [
            'nominal' => 0,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/circuitBreaker', $circuit_breaker);

        $response
            ->assertStatus(422)
            ->assertInvalid('nominal')
            ->assertJsonValidationErrors([
                'nominal' => 'The nominal field must be at least 1.',
            ]);
    }

    public function test_nominal_attribute_must_be_less_999_for_storing_role()
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

        $circuit_breaker = [
            'nominal' => 4000.1,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/circuitBreaker', $circuit_breaker);

        $response
            ->assertStatus(422)
            ->assertInvalid('nominal')
            ->assertJsonValidationErrors([
                'nominal' => 'The nominal field must not be greater than 4000.',
            ]);
    }

    public function test_a_circuit_breaker_can_not_be_stored_by_unauthorised_user(): void
    {
        $circuit_breaker = [
            'nominal' => 10,
        ];
        $response = $this->post('/api/circuitBreaker', $circuit_breaker);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseCount('breakers', 0);
        $this->assertDatabaseMissing('breakers', $circuit_breaker);
    }

    public function test_a_circuit_breaker_can_not_be_stored_by_not_admin_user(): void
    {
        //создание пользователя и присвоение ему роли
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

        $circuit_breaker = [
            'nominal' => 10,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/circuitBreaker', $circuit_breaker);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseCount('breakers', 0);
        $this->assertDatabaseMissing('breakers', $circuit_breaker);
    }

    public function test_a_circuit_breaker_can_be_stored_by_admin_user(): void
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

        $circuit_breaker = [
            'nominal' => 10,
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->post('/api/circuitBreaker', $circuit_breaker);

        $response->assertStatus(201);
        $this->assertDatabaseCount('breakers', 1);
        $this->assertDatabaseHas('breakers', $circuit_breaker);
    }
}
