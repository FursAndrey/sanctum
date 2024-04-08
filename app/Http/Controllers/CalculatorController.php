<?php

namespace App\Http\Controllers;

use App\Actions\Calculator\calculateInomFactory;
use App\Actions\Calculator\calculateTotalAction;
use App\Http\Requests\Calculator\SendItemsRequest;

class CalculatorController extends Controller
{
    public function getCalc(SendItemsRequest $request)
    {
        $data = $request->validated();

        foreach ($data['items'] as $key => $item) {
            $calculator = calculateInomFactory::make($item['type']);
            $data['items'][$key]['i'] = $calculator($item['p'], $item['cos'], $item['kpd']);
        }

        $total = (new calculateTotalAction())($data['items']);

        return response()->json(
            [
                'total' => $total,
                'items' => $data['items'],
            ]
        );
    }
}
