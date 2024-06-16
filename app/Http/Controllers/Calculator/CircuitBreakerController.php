<?php

namespace App\Http\Controllers\Calculator;

use App\Http\Controllers\Controller;
use App\Http\Requests\CircuitBreaker\StoreRequest;
use App\Http\Resources\CircuitBreakerResource;
use App\Models\CircuitBreaker;

class CircuitBreakerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', CircuitBreaker::class);

        $circuit_breakers = CircuitBreaker::all();

        return CircuitBreakerResource::collection($circuit_breakers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('create', CircuitBreaker::class);

        $data = $request->validated();
        $circuit_breaker = CircuitBreaker::create($data);

        return new CircuitBreakerResource($circuit_breaker);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CircuitBreaker $circuitBreaker)
    {
        $this->authorize('delete', $circuitBreaker);

        $circuitBreaker->delete();

        return response()->noContent();
    }
}
