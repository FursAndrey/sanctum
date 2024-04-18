<?php

namespace App\Actions\Calculator;

class calculateInomFactory
{
    private static $types = [
        1 => 'App\\Actions\\Calculator\\calculateEngineInomAction',
        2 => 'App\\Actions\\Calculator\\calculateTermInomAction',
        3 => 'App\\Actions\\Calculator\\calculateWeldingInomAction',
    ];

    public static function make(int $type): ?calculateInomInterface
    {
        if (isset(self::$types[$type])) {
            return new (self::$types[$type])();
        } else {
            return null;
        }
    }
}
