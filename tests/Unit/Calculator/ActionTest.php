<?php

namespace Tests\Unit\Calculator;

use App\Actions\Calculator\calculateEngineInomAction;
use App\Actions\Calculator\calculateInomFactory;
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
        $expected = 3.66;
        $this->assertEquals($expected, (new calculateEngineInomAction())(1.54, 0.8, 0.8));
        $expected = 29.77;
        $this->assertEquals($expected, (new calculateEngineInomAction())(12.54, 0.8, 0.8));
    }

    public function test_calculate_factory(): void
    {
        $calculator = calculateInomFactory::make(1);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateInomInterface', $calculator);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateEngineInomAction', $calculator);

        $calculator = calculateInomFactory::make(2);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateInomInterface', $calculator);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateTermInomAction', $calculator);
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
