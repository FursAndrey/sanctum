<?php

namespace App\Actions\TimeCalculator;

use Carbon\Carbon;

class generateTimeAction
{
    public function __invoke(Carbon $controlTime): Carbon
    {
        $newTime = clone $controlTime;
        $newTime = $newTime->addSeconds(rand(5 * 60, 2 * 60 * 60));

        return $newTime;
    }
}
