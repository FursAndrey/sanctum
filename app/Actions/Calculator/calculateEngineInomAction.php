<?php

namespace App\Actions\Calculator;

class calculateEngineInomAction implements calculateInomInterface
{
    public function __invoke(float $Pnom): float
    {
        $Inom = round($Pnom / (sqrt(3) * 0.38 * 0.5), 2);

        return $Inom;
    }
}
