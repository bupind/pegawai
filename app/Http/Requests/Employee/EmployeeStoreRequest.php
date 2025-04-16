<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'   => ['required', 'string', 'max:255'],
            'name'   => ['required', 'string', 'max:255'],
            'gender' => 'required',
            'type'   => 'required',
            'status' => 'required',
        ];
    }
}
