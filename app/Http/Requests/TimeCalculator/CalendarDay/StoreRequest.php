<?php

namespace App\Http\Requests\TimeCalculator\CalendarDay;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'calendar_id' => 'required|integer|exists:calendars,id',
            'month_day_id' => 'required|integer',
            'work_start' => 'required|date_format:H:i:s',
            'work_end' => 'required|date_format:H:i:s',
            'lunch_start' => 'required|date_format:H:i:s',
            'lunch_end' => 'required|date_format:H:i:s',
            'control_start' => 'nullable|date_format:H:i:s',
            'control_end' => 'nullable|date_format:H:i:s',
        ];
    }
}
