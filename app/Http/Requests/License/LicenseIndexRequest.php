<?php

namespace App\Http\Requests\License;

use Illuminate\Foundation\Http\FormRequest;

class LicenseIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order' => ['in:asc,desc'],
            'perPage' => ['numeric'],
        ];
    }
}
