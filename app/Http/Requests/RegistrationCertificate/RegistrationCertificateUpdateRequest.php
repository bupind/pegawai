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
            'employeeId'         => 'required',
            'type'               => 'required',
            'registrationNumber' => 'required',
            'validFrom'          => 'required|date',
            'validUntil'         => 'required|date|after_or_equal:validFrom',
        ];
    }
}
