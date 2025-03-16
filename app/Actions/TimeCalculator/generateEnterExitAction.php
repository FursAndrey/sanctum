<?php

namespace App\Actions\TimeCalculator;

use Carbon\Carbon;

class generateEnterExitAction
{
    public function __invoke(Carbon $controlTime): array
    {
        $enter = clone $controlTime;
        $enter = (new generateTimeAction)($enter);

        $exit = clone $enter;
        $exit = (new generateTimeAction)($exit);

        return [
            'enter' => $enter,
            'exit' => $exit,
        ];
    }
}
