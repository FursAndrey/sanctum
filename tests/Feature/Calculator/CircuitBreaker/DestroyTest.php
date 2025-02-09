<?php

namespace Tests\Feature\Calculator\CircuitBreaker;

use App\Models\CircuitBreaker;
use App\Models\Role;
use App\Models\User;
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

    public function test_a_circuit_breaker_can_be_deleted_by_admin_user()
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

        $circuitBreaker = [
            'nominal' => 10,
        ];
        $delCircuitBreaker = CircuitBreaker::create($circuitBreaker);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/circuitBreaker/'.$delCircuitBreaker->id);

        $response->assertStatus(204);
        $this->assertDatabaseCount('breakers', 0);
        $this->assertDatabaseMissing('breakers', $circuitBreaker);
    }

    public function test_a_circuit_breaker_can_not_be_deleted_by_not_admin_user()
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

        $circuitBreaker = [
            'nominal' => 10,
        ];
        $delCircuitBreaker = CircuitBreaker::create($circuitBreaker);

        // тестируемый запрос от имени пользователя
        $response = $this->actingAs($user)->delete('/api/circuitBreaker/'.$delCircuitBreaker->id);

        $response->assertStatus(403);
        $response->assertJsonFragment(
            [
                'message' => 'This action is unauthorized.',
            ]
        );
        $this->assertDatabaseHas('breakers', $circuitBreaker);
    }

    public function test_a_circuit_breaker_can_not_be_deleted_by_unauthorised_user()
    {
        $circuitBreaker = [
            'nominal' => 10,
        ];
        $delCircuitBreaker = CircuitBreaker::create($circuitBreaker);

        // тестируемый запрос от имени пользователя
        $response = $this->delete('/api/circuitBreaker/'.$delCircuitBreaker->id);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'message' => 'Unauthenticated.',
            ]
        );
        $this->assertDatabaseHas('breakers', $circuitBreaker);
    }
}
