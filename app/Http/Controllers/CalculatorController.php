<?php

namespace App\Http\Controllers;

use App\Actions\Calculator\calculateInomAction;
use App\Actions\Calculator\calculateTotalAction;
use App\Http\Requests\Calculator\SendItemsRequest;

class CalculatorController extends Controller
{
    public function getCalc(SendItemsRequest $request)
    {
        $data = $request->validated();

        foreach ($data['items'] as $key => $item) {
            $data['items'][$key]['i'] = (new calculateInomAction())($item['p']);
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