<?php

namespace App\Actions\Calculator;

class calculateInomAction implements calculateInomInterface
{
    public function __invoke(float $Pnom): float
    {
        $Inom = round($Pnom / (sqrt(3) * 0.38), 2);

        return $Inom;
    }
}
