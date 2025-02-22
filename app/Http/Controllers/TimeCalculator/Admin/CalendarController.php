<?php

namespace App\Http\Controllers\TimeCalculator\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeCalculator\Calendar\StoreRequest;
use App\Http\Resources\TimeCalculator\CalendarResource;
use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Calendar::class);

        $calendars = Calendar::get();

        return CalendarResource::collection($calendars);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('create', Calendar::class);

        $data = $request->validated();
        $calendar = Calendar::create($data);

        return new CalendarResource($calendar);
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        $this->authorize('view', $calendar);

        $calendar = Calendar::with(['calendarDays'])->find($calendar->id);

        return new CalendarResource($calendar);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Calendar $calendar)
    {
        $this->authorize('update', $calendar);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        $this->authorize('delete', $calendar);

        foreach ($calendar->users as $key => $user) {
            $user->calendar_id = null;
            $user->save();
        }
        foreach ($calendar->calendarDays as $key => $calendarDay) {
            $calendarDay->delete();
        }
        $calendar->delete();

        return response()->noContent();
    }
}
