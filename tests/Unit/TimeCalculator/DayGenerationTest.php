<?php

namespace Tests\Unit\TimeCalculator;

use App\Actions\TimeCalculator\generateDayEventsAction;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DayGenerationTest extends TestCase
{
    public function test_generated_day_events(): void
    {
        $controllTimeS = Carbon::now();
        $controllTimeE = Carbon::now();
        $graph['start'] = $controllTimeS->subSeconds(3 * 60 * 60);
        $graph['end'] = $controllTimeE->addSeconds(5 * 60 * 60);

        $dayEvents = (new generateDayEventsAction)($graph);

        $this->assertIsArray($dayEvents);

        $countEvents = count($dayEvents);
        $this->assertEquals(0, $countEvents % 2);

        $this->assertEquals('enter', $dayEvents[0]['type']);
        $this->assertEquals('exit', end($dayEvents)['type']);
    }
}
