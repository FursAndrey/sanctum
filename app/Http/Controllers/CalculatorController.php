<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\SendItemsRequest;

class CalculatorController extends Controller
{
    public function getCalc(SendItemsRequest $request)
    {
        $data = $request->validated();

        $count = count($data['items']);
        $Psum = 0;
        $Isum = 0;
        foreach ($data['items'] as $key => $item) {
            $data['items'][$key]['i'] = round($item['p'] / (sqrt(3) * 0.38), 2);
            $Psum += $item['p'];
            $Isum += $data['items'][$key]['i'];
        }

        return response()->json(
            [
                'total' => [
                    'count' => $count,
                    'Psum' => round($Psum, 2),
                    'Isum' => round($Isum, 2),
                ],
                'items' => $data['items'],
            ]
        );
    }
}
