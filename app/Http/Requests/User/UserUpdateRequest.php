<?php

namespace App\Http\Requests\User;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
        $userId = $this->route('user');
        return [
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'nullable|string|max:255',
            'email'                 => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone_number'          => [
                'required',
                'string',
                new PhoneNumberRule,
                Rule::unique('users', 'phone_number')->ignore($userId),
            ],
            'password'              => ['nullable', 'confirmed', Password::defaults()],
            'password_confirmation' => 'sometimes|required_with:password|same:password',
        ];
    }
}
