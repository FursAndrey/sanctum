<?php

namespace App\Http\Controllers\TimeCalculator\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeCalculator\CalendarDay\StoreRequest;
use App\Http\Resources\TimeCalculator\CalendarDayResource;
use App\Models\CalendarDay;
use Illuminate\Http\Request;

class CalendarDayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', CalendarDay::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('create', CalendarDay::class);

        $data = $request->validated();
        $calendarDay = CalendarDay::create($data);

        return new CalendarDayResource($calendarDay);
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarDay $calendarDay)
    {
        $this->authorize('view', $calendarDay);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarDay $calendarDay)
    {
        $this->authorize('update', $calendarDay);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarDay $calendarDay)
    {
        $this->authorize('delete', $calendarDay);

        $calendarDay->delete();

        return response()->noContent();
    }
}
