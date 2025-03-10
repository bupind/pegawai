<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function users(Request $request)
    {
        $query = User::whereHas('submissions')->select('id', 'first_name', 'last_name');

        if($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        $users = $query->get()->map(function($user) {
            return [
                'name' => $user->first_name . ' ' . $user->last_name,
                'id'   => $user->id,
            ];
        });

        return response()->json($users);
    }

    public function jabatan()
    {
        return response()->json(Jabatan::active()->select('id', 'name')->get());
    }

    public function department()
    {
        return response()->json(Departemen::active()->select('id', 'name')->get());
    }
}
