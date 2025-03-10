<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Rules\PhoneNumberRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', new PhoneNumberRule, 'unique:users'],
            'company_id'   => 'required',
            'password'     => $this->passwordRules(),
            'terms'        => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'first_name'   => $input['first_name'],
            'last_name'    => $input['last_name'],
            'email'        => $input['email'],
            'phone_number' => $input['phone_number'],
            'company_id'   => $input['bumn_id'],
            'password'     => Hash::make($input['password']),
        ]);
        $user->assignRole(User::ROLE_PEGAWAI);
        return $user;
    }
}
