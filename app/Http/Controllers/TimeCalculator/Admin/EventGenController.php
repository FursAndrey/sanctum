<?php

namespace App\Http\Controllers\TimeCalculator\Admin;

use App\Actions\TimeCalculator\generateDayEventsAction;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventGenController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function generate()
    {
        $users = User::all();
        foreach ($users as $user) {
            $userId = $user->id;
            $userCalendarDays = $user?->calendar?->calendarDays;

            if (is_null($userCalendarDays)) {
                continue;
            }

            $daysWithEvents = $user?->events->map->month_day_id->toArray();
            $daysWithEvents = array_unique($daysWithEvents);

            foreach ($userCalendarDays as $day) {
                $originDay = $day->month_day_id;
                if (in_array($originDay, $daysWithEvents)) {
                    continue;
                }
                $tmpDay = $day->month_day_id;
                if ((int) $tmpDay < 10) {
                    $tmpDay = '0'.$tmpDay;
                }
                $tmpTime = date('Y-m-').$tmpDay.' '.$day->work_start;
                $graph['start'] = Carbon::parse($tmpTime);

                $tmpTime = date('Y-m-').$tmpDay.' '.$day->work_end;
                $graph['end'] = Carbon::parse($tmpTime);

                $dayEvents = (new generateDayEventsAction)($graph);

                foreach ($dayEvents as $event) {
                    $createdEvent = [
                        'user_id' => $userId,
                        'month_day_id' => $originDay,
                        'event_type' => $event['type'],
                        'event_time' => $event['time'],
                    ];
                    Event::create($createdEvent);
                }
            }
        }

        return response(status: 200, content: json_encode(['message' => 'Events created.']));
    }
}
