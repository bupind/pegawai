<?php

namespace App\Http\Requests\RegistrationCertificate;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationCertificateIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order'   => ['in:asc,desc'],
            'perPage' => ['numeric'],
        ];
    }
}
