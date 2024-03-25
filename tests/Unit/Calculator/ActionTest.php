<?php

namespace Tests\Unit\Calculator;

use App\Actions\Calculator\calculateInomAction;
use App\Actions\Calculator\calculateTotalAction;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function test_calculate_Inom_action(): void
    {
        $expected = 2.34;
        $this->assertEquals($expected, (new calculateInomAction())(1.54));
        $expected = 19.05;
        $this->assertEquals($expected, (new calculateInomAction())(12.54));
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