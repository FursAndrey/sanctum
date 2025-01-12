<?php

namespace App\Http\Controllers\TimeCalculator\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeCalculator\Holyday\StoreRequest;
use App\Http\Resources\TimeCalculator\HolydayResource;
use App\Models\Holyday;
use Illuminate\Http\Request;

class HolydayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Holyday::class);

        $roles = Holyday::get();

        return HolydayResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('create', Holyday::class);

        $data = $request->validated();
        $holyday = Holyday::create($data);

        return new HolydayResource($holyday);
    }

    /**
     * Display the specified resource.
     */
    public function show(Holyday $holyday)
    {
        $this->authorize('view', $holyday);
        /** не используется */
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Holyday $holyday)
    {
        $this->authorize('update', $holyday);
        /** не используется */
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Holyday $holyday)
    {
        $this->authorize('delete', $holyday);

        $holyday->delete();

        return response()->noContent();
    }
}
