<?php

namespace Tests\Unit\Calculator;

use App\Actions\Calculator\calculateEngineInomAction;
use App\Actions\Calculator\calculateInomFactory;
use App\Actions\Calculator\calculateTermInomAction;
use App\Actions\Calculator\calculateTotalAction;
use App\Actions\Calculator\calculateWeldingInomAction;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function test_calculate_inom_action(): void
    {
        $expected = 2.34;
        $this->assertEquals($expected, (new calculateTermInomAction)(Pnom: 1.54));
        $expected = 19.05;
        $this->assertEquals($expected, (new calculateTermInomAction)(Pnom: 12.54));
    }

    public function test_calculate_engine_inom_action(): void
    {
        $expected = 2.92;
        $this->assertEquals($expected, (new calculateEngineInomAction)(Pnom: 1.54, cos: 0.8));
        $expected = 23.82;
        $this->assertEquals($expected, (new calculateEngineInomAction)(Pnom: 12.54, cos: 0.8));
    }

    public function test_calculate_welding_inom_action(): void
    {
        $expected = 36.51;
        $this->assertEquals($expected, (new calculateWeldingInomAction)(Pnom: 38, pv: 0.4));
        $expected = 2.09;
        $this->assertEquals($expected, (new calculateWeldingInomAction)(Pnom: 1.54, pv: 0.8));
    }

    public function test_calculate_factory(): void
    {
        $calculator = calculateInomFactory::make(1);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateInomInterface', $calculator);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateEngineInomAction', $calculator);

        $calculator = calculateInomFactory::make(2);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateInomInterface', $calculator);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateTermInomAction', $calculator);

        $calculator = calculateInomFactory::make(3);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateInomInterface', $calculator);
        $this->assertInstanceOf('App\\Actions\\Calculator\\calculateWeldingInomAction', $calculator);

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
                'cos' => 1,
            ],
            1 => [
                'num' => 12,
                'p' => 12.54,
                'i' => 23.82,
                'cos' => 0.8,
            ],
            2 => [
                'num' => 2,
                'p' => 38,
                'i' => 36.51,
                'cos' => 1,
            ],
        ];
        $expected = [
            'count' => 3,
            'Psum' => 55.22,
            'Isum' => 62.67,
        ];
        $this->assertEquals($expected, (new calculateTotalAction)($items));
    }
}
