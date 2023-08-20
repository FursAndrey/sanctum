<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        $rules = [
            'roles' => [
                'required',
                'array'
            ],
            'roles.*.id' => [
                'required',
                'integer',
                'exists:roles,id'
            ],
            'tg_name' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('users')->ignore($this->user->id)
            ],
        ];
        
        return $rules;
    }
}
