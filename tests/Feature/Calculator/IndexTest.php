<?php

namespace Tests\Feature\Calculator;

use App\Models\CircuitBreaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_items_is_required(): void
    {
        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator');

        $response
            ->assertStatus(422)
            ->assertInvalid('items')
            ->assertJsonValidationErrors([
                'items' => 'The items field is required.',
            ]);
    }

    public function test_items_must_be_array(): void
    {
        $items = [
            'items' => 'title',
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items')
            ->assertJsonValidationErrors([
                'items' => 'The items field must be an array.',
            ]);
    }

    public function test_items_0_must_be_array(): void
    {
        $items = [
            'items' => [
                'title',
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0')
            ->assertJsonValidationErrors([
                'items.0' => 'The items.0 field must be an array.',
            ]);
    }

    public function test_items_0_p_is_required(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'cos' => 1,
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.p')
            ->assertJsonValidationErrors([
                'items.0.p' => 'The items.0.p field is required.',
            ]);
    }

    public function test_items_0_num_is_required(): void
    {
        $items = [
            'items' => [
                0 => [
                    'p' => 1,
                    'cos' => 1,
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.num')
            ->assertJsonValidationErrors([
                'items.0.num' => 'The items.0.num field is required.',
            ]);
    }

    public function test_items_0_cos_is_required(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.cos')
            ->assertJsonValidationErrors([
                'items.0.cos' => 'The items.0.cos field is required.',
            ]);
    }

    public function test_items_0_pv_is_required(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'cos' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.pv')
            ->assertJsonValidationErrors([
                'items.0.pv' => 'The items.0.pv field is required.',
            ]);
    }

    public function test_items_0_type_is_required(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'pv' => 1,
                    'cos' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.type')
            ->assertJsonValidationErrors([
                'items.0.type' => 'The items.0.type field is required.',
            ]);
    }

    public function test_items_0_num_must_be_integer(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1.54,
                    'p' => 1,
                    'cos' => 1,
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.num')
            ->assertJsonValidationErrors([
                'items.0.num' => 'The items.0.num field must be an integer.',
            ]);
    }

    public function test_items_0_type_must_be_integer(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'type' => [1],
                    'cos' => 1,
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.type')
            ->assertJsonValidationErrors([
                'items.0.type' => 'The items.0.type field must be an integer.',
            ]);
    }

    public function test_items_0_p_must_be_numeric(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => [1],
                    'cos' => 1,
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.p')
            ->assertJsonValidationErrors([
                'items.0.p' => 'The items.0.p field must be a number.',
            ]);
    }

    public function test_items_0_cos_must_be_numeric(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'cos' => [1],
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.cos')
            ->assertJsonValidationErrors([
                'items.0.cos' => 'The items.0.cos field must be a number.',
            ]);
    }

    public function test_items_0_pv_must_be_numeric(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'cos' => 1,
                    'pv' => [1],
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.pv')
            ->assertJsonValidationErrors([
                'items.0.pv' => 'The items.0.pv field must be a number.',
            ]);
    }

    public function test_items_0_cos_must_be_more_than_0001(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'cos' => 0.0001,
                    'kpd' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.cos')
            ->assertJsonValidationErrors([
                'items.0.cos' => 'The items.0.cos field must be at least 0.001.',
            ]);
    }

    public function test_items_0_pv_must_be_more_than_0001(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'cos' => 1,
                    'pv' => 0.0001,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.pv')
            ->assertJsonValidationErrors([
                'items.0.pv' => 'The items.0.pv field must be at least 0.001.',
            ]);
    }

    public function test_items_0_cos_must_be_less_than_1(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'cos' => 1.0001,
                    'pv' => 1,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.cos')
            ->assertJsonValidationErrors([
                'items.0.cos' => 'The items.0.cos field must not be greater than 1.',
            ]);
    }

    public function test_items_0_pv_must_be_less_than_1(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'cos' => 1,
                    'pv' => 1.0001,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.pv')
            ->assertJsonValidationErrors([
                'items.0.pv' => 'The items.0.pv field must not be greater than 1.',
            ]);
    }

    public function test_items_0_type_must_be_from_expected_array(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1,
                    'type' => rand(10, 100),
                    'cos' => 1,
                    'pv' => 0.8,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.type')
            ->assertJsonValidationErrors([
                'items.0.type' => 'The selected items.0.type is invalid.',
            ]);
    }

    public function test_items_correct_format(): void
    {
        CircuitBreaker::create(['nominal' => 6]);
        CircuitBreaker::create(['nominal' => 16]);
        CircuitBreaker::create(['nominal' => 25]);
        CircuitBreaker::create(['nominal' => 40]);
        CircuitBreaker::create(['nominal' => 80]);
        CircuitBreaker::create(['nominal' => 100]);

        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'type' => 1,
                    'p' => 1.54,
                    'cos' => 0.8,
                    'pv' => 1,
                ],
                1 => [
                    'num' => 12,
                    'type' => 1,
                    'p' => 12.54,
                    'cos' => 0.95,
                    'pv' => 1,
                ],
                2 => [
                    'num' => 3,
                    'type' => 2,
                    'p' => 10,
                    'cos' => 1,
                    'pv' => 1,
                ],
                3 => [
                    'num' => 4,
                    'type' => 3,
                    'p' => 38,
                    'cos' => 1,
                    'pv' => 0.4,
                ],
            ],
        ];

        // тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $expectedJson = [
            'total' => [
                'count' => count($items['items']),
                'Psum' => 63.13,
                'breakerNominal' => 80,
                'Isum' => round(2.92 + 20.06 + 15.19 + 36.51, 2),
            ],
            'items' => [
                0 => [
                    'num' => $items['items'][0]['num'],
                    'p' => $items['items'][0]['p'],
                    'cos' => $items['items'][0]['cos'],
                    'pv' => $items['items'][0]['pv'],
                    'type' => $items['items'][0]['type'],
                    'i' => 2.92,
                    'breakerNominal' => 6,
                ],
                1 => [
                    'num' => $items['items'][1]['num'],
                    'p' => $items['items'][1]['p'],
                    'cos' => $items['items'][1]['cos'],
                    'pv' => $items['items'][1]['pv'],
                    'type' => $items['items'][1]['type'],
                    'i' => 20.06,
                    'breakerNominal' => 25,
                ],
                2 => [
                    'num' => $items['items'][2]['num'],
                    'p' => $items['items'][2]['p'],
                    'cos' => $items['items'][2]['cos'],
                    'pv' => $items['items'][2]['pv'],
                    'type' => $items['items'][2]['type'],
                    'i' => 15.19,
                    'breakerNominal' => 16,
                ],
                3 => [
                    'num' => $items['items'][3]['num'],
                    'p' => $items['items'][3]['p'],
                    'cos' => $items['items'][3]['cos'],
                    'pv' => $items['items'][3]['pv'],
                    'type' => $items['items'][3]['type'],
                    'i' => 36.51,
                    'breakerNominal' => 40,
                ],
            ],
        ];

        $response->assertStatus(200);
        $response->assertJson($expectedJson);
    }
}
