<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:employee create', ['only' => ['create', 'store']]);
        $this->middleware('permission:employee read', ['only' => ['index', 'show']]);
        $this->middleware('permission:employee update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:employee delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    public function index(Request $request)
    {
        $employees = Employee::query();
        if($request->filled('search')) {
            $searchable = ['code', 'name', 'gender', 'status'];
            $employees->where(function($query) use ($request, $searchable) {
                foreach($searchable as $field) {
                    $query->orWhere($field, 'LIKE', '%' . $request->search . '%');
                }
            });
        }

        if($request->filled('field') && $request->filled('order')) {
            $allowedFields = ['code', 'name', 'gender', 'status', 'type', 'created_at'];
            $allowedOrder  = ['asc', 'desc'];

            $field = in_array($request->field, $allowedFields) ? $request->field : 'created_at';
            $order = in_array(strtolower($request->order), $allowedOrder) ? strtolower($request->order) : 'desc';

            $employees->orderBy($field, $order);
        }
        $perPage  = $request->integer('perPage') ?? 10;
        $settings = Setting::first();
        $canLogin = $settings->employeecanlogin;
        return Inertia::render('Employee/Index', [
            'title'       => __('app.label.employee'),
            'filters'     => $request->only(['search', 'field', 'order']),
            'perPage'     => $perPage,
            'statuses'    => Employee::statuses(),
            'genders'     => Employee::genders(),
            'types'       => Employee::types(),
            'canLogin'    => $canLogin,
            'datas'       => ($canLogin === Setting::LOGIN_TRUE ? $employees->with('user') : $employees)->paginate($perPage)
                ->onEachSide(0),
            'breadcrumbs' => [
                ['label' => __('app.label.employee'), 'href' => route('employee.index')],
            ],
        ]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code'   => 'required|unique:employee,code',
            'name'   => 'required|string|max:200',
            'gender' => 'required',
            'type'   => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = Setting::first();
        $canLogin = $settings->employeecanlogin;

        DB::beginTransaction();

        try {
            $user = null;
            if($canLogin === Setting::LOGIN_TRUE) {
                $userValidator = Validator::make($request->all(), [
                    'email'        => 'required|email|unique:users,email',
                    'phone_number' => 'required|string|max:20',
                ]);
                if($userValidator->fails()) {
                    return back()->withErrors($userValidator)->withInput();
                }

                $user = User::create([
                    'first_name'        => $request->name,
                    'email'             => $request->email,
                    'phone_number'      => $request->phone_number,
                    'email_verified_at' => now(),
                    'password'          => Hash::make('password'),
                ]);

                $user->assignRole(User::ROLE_PEGAWAI);
            }

            Employee::create([
                'code'    => $request->code,
                'name'    => $request->name,
                'gender'  => $request->gender,
                'type'    => $request->type,
                'status'  => $request->status,
                'user_id' => $user?->id,
            ]);

            DB::commit();

            return back()->with('success', __('app.label.created_successfully'));

        } catch(\Throwable $th) {
            DB::rollBack();
            return back()->with('error', __('app.label.created_error'));
        }
    }

    public function show(Employee $employee)
    {
    }

    public function edit(Employee $employee)
    {
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $canLogin = Setting::first()->employeecanlogin;

        $validator = Validator::make($request->all(), [
            'code'   => 'required|unique:employee,code,' . $employee->id,
            'name'   => 'required|string|max:200',
            'gender' => 'required|in:' . implode(',', array_keys(Employee::genders())),
            'type'   => 'required|in:' . implode(',', array_keys(Employee::types())),
            'status' => 'required|in:' . implode(',', array_keys(Employee::statuses())),
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            if($canLogin === Setting::LOGIN_TRUE) {
                $userValidator = Validator::make($request->all(), [
                    'email'        => 'required|email|unique:users,email,' . optional($employee->user)->id,
                    'phone_number' => 'required|string|max:20',
                ]);

                if($userValidator->fails()) {
                    return back()->withErrors($userValidator)->withInput();
                }

                if($employee->user) {
                    $employee->user->update([
                        'first_name'   => $request->name,
                        'email'        => $request->email,
                        'phone_number' => $request->phone_number,
                    ]);
                }
                else {
                    $user = User::create([
                        'first_name'        => $request->name,
                        'email'             => $request->email,
                        'phone_number'      => $request->phone_number,
                        'email_verified_at' => now(),
                        'password'          => Hash::make('password'),
                    ]);
                    $user->assignRole(User::ROLE_PEGAWAI);
                    $employee->user_id = $user->id;
                }
            }

            $employee->update([
                'code'   => $request->code,
                'name'   => $request->name,
                'gender' => $request->gender,
                'type'   => $request->type,
                'status' => $request->status,
            ]);

            $employee->save();

            DB::commit();
            return back()->with('success', __('app.label.updated_successfully'));

        } catch(\Throwable $th) {
            DB::rollBack();
            return back()->with('error', __('app.label.updated_error') . $th->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return back()->with('success', __('app.label.deleted_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error') . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $employees = Employee::whereIn('id', $request->id);
            $employees->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . "Employee"]));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . "Employee"]) . $th->getMessage());
        }
    }
}
