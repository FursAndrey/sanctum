<?php

namespace App\Http\Controllers\TimeCalculator\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeCalculator\CalendarUser\SetRequest;
use App\Http\Resources\User\UserResource;
// use App\Models\Calendar;
use App\Models\User;

class CalendarUserController extends Controller
{
    public function set(SetRequest $request)
    {
        $data = $request->validated();
        $user = User::find($data['user_id']);
        $user->calendar_id = $data['calendar_id'];
        // $calendar = Calendar::find($data['calendar_id']);
        // $user->calendar()->associate($calendar);
        $user->save();

        return new UserResource($user);
    }
}
