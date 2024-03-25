<?php

namespace App\Actions\Calculator;

class calculateTotalAction
{
    public function __invoke(array $items): array
    {
        $count = count($items);
        $Psum = 0;
        $Isum = 0;
        foreach ($items as $key => $item) {
            $Psum += $item['p'];
            $Isum += $items[$key]['i'];
        }

        return [
            'count' => $count,
            'Psum' => round($Psum, 2),
            'Isum' => round($Isum, 2),
        ];
    }
}
