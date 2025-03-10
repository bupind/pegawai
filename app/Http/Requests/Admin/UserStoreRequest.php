<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
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
            'email'        => 'required|string|email|max:255|unique:' . User::class,
            'phone_number' => 'required|string|string|max:15|unique:' . User::class,
            'password'     => ['required', 'confirmed', Password::defaults()],
            'role'         => ['required'],
        ];
    }
}
