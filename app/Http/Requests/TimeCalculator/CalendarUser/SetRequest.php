<?php

namespace App\Http\Requests\TimeCalculator\CalendarUser;

use Illuminate\Foundation\Http\FormRequest;

class SetRequest extends FormRequest
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
            'calendar_id' => 'nullable|integer|exists:calendars,id',
            'user_id' => 'required|integer|exists:users,id',
        ];
    }
}
