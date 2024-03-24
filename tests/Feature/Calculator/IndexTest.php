<?php

namespace Tests\Feature\Calculator;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
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

    public function test_items_is_required(): void
    {
        //тестируемый запрос от имени пользователя
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

        //тестируемый запрос от имени пользователя
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

        //тестируемый запрос от имени пользователя
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
                ],
            ],
        ];

        //тестируемый запрос от имени пользователя
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
                ],
            ],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.num')
            ->assertJsonValidationErrors([
                'items.0.num' => 'The items.0.num field is required.',
            ]);
    }

    public function test_items_0_num_must_be_integer(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1.54,
                    'p' => 1,
                ],
            ],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.num')
            ->assertJsonValidationErrors([
                'items.0.num' => 'The items.0.num field must be an integer.',
            ]);
    }

    public function test_items_0_p_must_be_numeric(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => [1],
                ],
            ],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $response
            ->assertStatus(422)
            ->assertInvalid('items.0.p')
            ->assertJsonValidationErrors([
                'items.0.p' => 'The items.0.p field must be a number.',
            ]);
    }

    public function test_items_correct_format(): void
    {
        $items = [
            'items' => [
                0 => [
                    'num' => 1,
                    'p' => 1.54,
                ],
                1 => [
                    'num' => 12,
                    'p' => 12.54,
                ],
            ],
        ];

        //тестируемый запрос от имени пользователя
        $response = $this->post('/api/calculator', $items);

        $expectedJson = [
            'total' => [
                'count' => count($items['items']),
                'Psum' => round($items['items'][0]['p'] + $items['items'][1]['p'], 2),
                'Isum' => round(2.34 + 19.05, 2),
            ],
            'items' => [
                0 => [
                    'num' => $items['items'][0]['num'],
                    'p' => $items['items'][0]['p'],
                    'i' => 2.34,
                ],
                1 => [
                    'num' => $items['items'][1]['num'],
                    'p' => $items['items'][1]['p'],
                    'i' => 19.05,
                ],
            ],
        ];

        $response->assertStatus(200);
        $response->assertJson($expectedJson);
    }
}
