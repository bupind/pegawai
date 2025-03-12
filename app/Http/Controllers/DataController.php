<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\RegistrationCertificate;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function employees(Request $request)
    {
        $query = Employee::active()->select('id', 'code', 'name');
        if($request->has('gender')) {
            $query->where('gender', $request->gender);
        }
        $data = $query->get()->map(function($val) {
            return [
                'name' => $val->code . ' ' . $val->name,
                'id'   => $val->id,
            ];
        });

        return response()->json($data);
    }

    public function certificates(Request $request)
    {
        $query = RegistrationCertificate::active()->select('id', 'type', 'competence');
        if($request->has('type')) {
            $query->where('type', $request->type);
        }
        $data = $query->get()->map(function($val) {
            return [
                'name' => $val->type . ' ' . $val->competence,
                'id'   => $val->id,
            ];
        });

        return response()->json($data);
    }
}
