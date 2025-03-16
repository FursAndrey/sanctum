<?php

namespace Tests\Unit\TimeCalculator;

use App\Actions\TimeCalculator\generateEnterExitAction;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class EnterExitGenerationTest extends TestCase
{
    public function test_generated_enter_exit_is_array(): void
    {
        $controllTime = Carbon::now();
        $enterExit = (new generateEnterExitAction)($controllTime);

        $this->assertIsArray($enterExit);
    }

    public function test_structure_array(): void
    {
        $controllTime = Carbon::now();
        $enterExit = (new generateEnterExitAction)($controllTime);

        $this->assertCount(2, $enterExit);
        $this->assertArrayHasKey('enter', $enterExit);
        $this->assertArrayHasKey('exit', $enterExit);

        $this->assertIsObject($enterExit['enter']);
        $this->assertIsObject($enterExit['exit']);

        $this->assertEquals("Carbon\Carbon", get_class($enterExit['enter']));
        $this->assertEquals("Carbon\Carbon", get_class($enterExit['exit']));

        $this->assertGreaterThan($enterExit['enter']->format('H:i'), $enterExit['exit']->format('H:i'));
    }
}
