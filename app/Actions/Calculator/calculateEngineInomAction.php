<?php

namespace App\Actions\Calculator;

class calculateEngineInomAction implements calculateInomInterface
{
    public function __invoke(float $Pnom, float $cos, float $pv = 1): float
    {
        $Inom = round($Pnom / (sqrt(3) * 0.38 * $cos), 2);

        return $Inom;
    }
}
