<?php

namespace App\Actions\Calculator;

class calculateEngineInomAction implements calculateInomInterface
{
    public function __invoke(float $Pnom, float $cos, float $kpd): float
    {
        $Inom = round($Pnom / (sqrt(3) * 0.38 * $cos * $kpd), 2);

        return $Inom;
    }
}
