<?php

namespace App\Actions;

class checkEnvIsProdAction
{
    public function __invoke(string $envName): bool
    {
        if ($envName === 'production') {
            return true;
        } else {
            return false;
        }
    }
}
