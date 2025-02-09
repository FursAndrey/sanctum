<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarDayFirstSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $calendarId = DB::table('calendars')->select('id')->orderByDesc('id')->limit(1)->first()->id;

        DB::table('calendar_days')->insert(
            [
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 1,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 2,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 3,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 4,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 5,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 6,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 7,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 8,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 9,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 10,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 11,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 12,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 12,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 13,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 14,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 15,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 16,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 17,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 18,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 19,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 20,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 21,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 22,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 23,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 24,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 25,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 26,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 27,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 28,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 29,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 30,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
                [
                    'calendar_id' => $calendarId, 'month_day_id' => 31,
                    'work_start' => '07:20', 'work_end' => '18:00', 'lunch_start' => '12:00', 'lunch_end' => '12:40', 'control_start' => '09:00', 'control_end' => '16:00',
                ],
            ]
        );
    }
}
