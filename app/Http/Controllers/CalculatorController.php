<?php

namespace App\Http\Controllers;

use App\Actions\Calculator\calculateInomFactory;
use App\Actions\Calculator\calculateTotalAction;
use App\Http\Requests\Calculator\SendItemsRequest;

class CalculatorController extends Controller
{
    /**
     *  @OA\Post(
     *      path="/api/calculator",
     *      summary="Страница калькулятора",
     *      tags={"calculator"},
     *
     *      @OA\RequestBody(
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(
     *
     *                      @OA\Property(property="items", type="array",
     *
     *                          @OA\Items(
     *
     *                              @OA\Property(property="num", type="integer", example=1),
     *                              @OA\Property(property="p", type="float", example=10),
     *                              @OA\Property(property="cos", type="float", example=1),
     *                              @OA\Property(property="pv", type="float", example=1),
     *                              @OA\Property(property="type", type="integer", example=1),
     *                          ),
     *                      ),
     *                  ),
     *              }
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="items", type="array",
     *
     *                  @OA\Items(
     *
     *                      @OA\Property(property="num", type="integer", example=1),
     *                      @OA\Property(property="p", type="float", example=10),
     *                      @OA\Property(property="cos", type="float", example=1),
     *                      @OA\Property(property="pv", type="float", example=1),
     *                      @OA\Property(property="type", type="integer", example=1),
     *                      @OA\Property(property="i", type="float", example=15.19),
     *                  ),
     *              ),
     *              @OA\Property(property="total", type="object",
     *                  @OA\Property(property="count", type="integer", example=2),
     *                  @OA\Property(property="p", type="float", example=10),
     *                  @OA\Property(property="i", type="float", example=15.19),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=422, description="Invalid params"),
     *  )
     */
    public function getCalc(SendItemsRequest $request)
    {
        $data = $request->validated();

        foreach ($data['items'] as $key => $item) {
            $calculator = calculateInomFactory::make($item['type']);
            $data['items'][$key]['i'] = $calculator($item['p'], $item['cos'], $item['pv']);
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
