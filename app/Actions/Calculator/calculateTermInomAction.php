<?php

namespace App\Actions\Calculator;

class calculateTermInomAction implements calculateInomInterface
{
    public function __invoke(float $Pnom, float $cos = 1, float $kpd = 1): float
    {
        $Inom = round($Pnom / (sqrt(3) * 0.38), 2);

        return $Inom;
    }
}
