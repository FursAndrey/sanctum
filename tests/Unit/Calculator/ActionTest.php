<?php

namespace Tests\Unit\Calculator;

use App\Actions\Calculator\calculateEngineInomAction;
use App\Actions\Calculator\calculateTermInomAction;
use App\Actions\Calculator\calculateTotalAction;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function test_calculate_Inom_action(): void
    {
        $expected = 2.34;
        $this->assertEquals($expected, (new calculateTermInomAction())(1.54));
        $expected = 19.05;
        $this->assertEquals($expected, (new calculateTermInomAction())(12.54));
    }

    public function test_calculate_engine_Inom_action(): void
    {
        $expected = 4.68;
        $this->assertEquals($expected, (new calculateEngineInomAction())(1.54));
        $expected = 38.11;
        $this->assertEquals($expected, (new calculateEngineInomAction())(12.54));
    }

    public function test_calculate_total_action(): void
    {
        $items = [
            0 => [
                'num' => 1,
                'p' => 1.54,
                'i' => 2.34,
            ],
            1 => [
                'num' => 12,
                'p' => 12.54,
                'i' => 19.05,
            ],
        ];
        $expected = [
            'count' => 2,
            'Psum' => 14.08,
            'Isum' => 21.39,
        ];
        $this->assertEquals($expected, (new calculateTotalAction())($items));
    }
}
