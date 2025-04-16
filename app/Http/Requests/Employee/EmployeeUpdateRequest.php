<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:255'],
            'gender' => 'required',
            'type'   => 'required',
            'status' => 'required',
        ];
    }
}
