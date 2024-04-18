<?php

namespace App\Actions\Calculator;

interface calculateInomInterface
{
    public function __invoke(float $Pnom, float $cos, float $pv): float;
}
