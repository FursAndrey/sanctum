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
        $this->assertEquals($expected, (new calculateTermInomAction())(Pnom: 1.54));
        $expected = 19.05;
        $this->assertEquals($expected, (new calculateTermInomAction())(Pnom: 12.54));
    }

    public function test_calculate_engine_Inom_action(): void
    {
        $expected = 2.92;
        $this->assertEquals($expected, (new calculateEngineInomAction())(Pnom: 1.54, cos: 0.8));
        $expected = 23.82;
        $this->assertEquals($expected, (new calculateEngineInomAction())(Pnom:12.54, cos: 0.8));
    }

    public function test_calculate_factory(): void
    {
        $calculator = calculateInomFactory::make(1);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateInomInterface', $calculator);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateEngineInomAction', $calculator);

        $calculator = calculateInomFactory::make(2);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateInomInterface', $calculator);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateTermInomAction', $calculator);

        $calculator = calculateInomFactory::make(99);
        $this->assertNull($calculator);
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
