<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name'            => ['required', 'string', 'max:255'],
            'email'                 => 'required|unique:users,email,' . $this->user->id,
            'phone_number'          => 'required|unique:users,phone_number,' . $this->user->id,
            'password'              => ['nullable', 'confirmed', Password::defaults()],
            'password_confirmation' => 'sometimes|required_with:password|same:password',
        ];
    }
}
