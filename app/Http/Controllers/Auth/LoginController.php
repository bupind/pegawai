<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login', []);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
        $user        = User::with('roles')->where('email', $credentials['email'])->first();
        if($user && Hash::check($credentials['password'], $user->password)) {
            if($user->roles->isNotEmpty()) {
                $roleName = $user->roles->first()->name;
            }
            else {
                throw ValidationException::withMessages([
                    'email' => ['User tidak memiliki role yang valid.'],
                ]);
            }
            $canLogin = Setting::first();
            if($roleName === User::ROLE_PEGAWAI && $canLogin && $canLogin->employeecanlogin != Setting::LOGIN_TRUE) {
                throw ValidationException::withMessages([
                    'email' => ['Akun pegawai tidak diizinkan login saat ini.'],
                ]);
            }

            Auth::login($user);
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => ['Email atau password tidak valid.'],
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
