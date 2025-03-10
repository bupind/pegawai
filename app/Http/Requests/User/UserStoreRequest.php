<?php

namespace App\Http\Requests\User;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
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
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', new PhoneNumberRule, 'unique:users'],
            'password'     => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
