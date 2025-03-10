<?php

namespace App\Http\Requests\Pegawai;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class PegawaiUpdateRequest extends FormRequest
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
            'name'               => 'required|string|max:255',
            'jabatan_id'         => 'required|exists:jabatan,id',
            'departemen_id'      => 'required|exists:departemen,id',
            'status_kepegawaian' => 'required|string',
            'email'              => ['required', 'string', 'email', 'max:255'],
            'phone_number'       => ['required', 'string', new PhoneNumberRule],
        ];
    }
}
