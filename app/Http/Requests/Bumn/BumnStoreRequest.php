<?php

namespace App\Http\Requests\Bumn;

use Illuminate\Foundation\Http\FormRequest;

class BumnStoreRequest extends FormRequest
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
            'cluster' => 'nullable|string|max:255',
            'name'    => 'required|string|max:255',
            'domain'  => 'nullable|url',
            'status'  => 'required',
            'logo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
