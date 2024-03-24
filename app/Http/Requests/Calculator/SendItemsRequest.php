<?php

namespace App\Http\Requests\Calculator;

use Illuminate\Foundation\Http\FormRequest;

class SendItemsRequest extends FormRequest
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
            'items' => 'required|array',
            'items.*' => 'required|array',
            '*.*.num' => 'required|integer|min:1',
            '*.*.p' => 'required|numeric|min:0.001',
        ];
    }
}
