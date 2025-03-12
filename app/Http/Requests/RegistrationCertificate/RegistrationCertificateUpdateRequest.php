<?php

namespace App\Http\Requests\RegistrationCertificate;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationCertificateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'               => 'required',
            'registrationNumber' => 'required',
        ];
    }
}
