<?php

namespace App\Actions\Calculator\CircuitBreaker;

use App\Models\CircuitBreaker;

class getInomAction
{
    public function __invoke(float $Inom): float
    {
        $Inom = CircuitBreaker::select('nominal')->where('nominal', '>=', $Inom)->orderBy('nominal', 'ASC')->first()->toArray()['nominal'];

        return $Inom;
    }
}
