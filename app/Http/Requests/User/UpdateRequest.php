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
        $roles = $this->user->roles->pluck('title')->toArray();
        if (in_array('Admin', $roles)) {
            $rules = [
                'roles' => [
                    'required',
                    'array',
                ],
                'has_ban_chat' => [
                    'required',
                    'boolean',
                ],
                'has_ban_comment' => [
                    'required',
                    'boolean',
                ],
                'roles.*.id' => [
                    'required',
                    'integer',
                    'exists:roles,id',
                ],
                'tg_name' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::unique('users')->ignore($this->user->id),
                ],
            ];
        } else {
            $rules = [
                'tg_name' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('users')->ignore($this->user->id),
                ],
            ];
        }

        return $rules;
    }
}
