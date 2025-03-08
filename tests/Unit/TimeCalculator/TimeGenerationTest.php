<?php

namespace Tests\Unit\TimeCalculator;

use App\Actions\TimeCalculator\generateTimeAction;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class TimeGenerationTest extends TestCase
{
    public function test_generated_time_greater_than_controll_time(): void
    {
        $controllTime = Carbon::now();
        $newTime = (new generateTimeAction)($controllTime);

        $this->assertGreaterThan($controllTime->format('H:i'), $newTime->format('H:i'));
    }

    public function test_generated_time_is_object(): void
    {
        $controllTime = Carbon::now();
        $newTime = (new generateTimeAction)($controllTime);

        $this->assertIsObject($newTime);
    }
}
