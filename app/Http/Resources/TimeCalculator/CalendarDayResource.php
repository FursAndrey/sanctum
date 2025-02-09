<?php

namespace App\Http\Resources\TimeCalculator;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarDayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'calendar_id' => $this->calendar_id,
            'month_day_id' => $this->month_day_id,
            'work_start' => $this->work_start,
            'work_end' => $this->work_end,
            'lunch_start' => $this->lunch_start,
            'lunch_end' => $this->lunch_end,
            'control_start' => $this->control_start,
            'control_end' => $this->control_end,
        ];
    }
}
