<?php

namespace App\Http\Requests\License;

use Illuminate\Foundation\Http\FormRequest;

class LicenseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                      => 'required',
            'employeeId'                => 'required',
            'registrationCertificateId' => 'required',
            'validFrom'                 => 'required',
            'validUntil'                => 'required',
            'status'                    => 'required',
        ];
    }
}
