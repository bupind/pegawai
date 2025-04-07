<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\EmployeeIndexRequest;
use App\Http\Requests\Employee\EmployeeStoreRequest;
use App\Http\Requests\Employee\EmployeeUpdateRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
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

    public function index(EmployeeIndexRequest $request)
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
        if($request->has(['field', 'order'])) {
            $employees->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render('Employee/Index', [
            'title'       => __('app.label.employee'),
            'filters'     => $request->all(['search', 'field', 'order']),
            'perPage'     => (int)$perPage,
            'statuses'    => Employee::statuses(),
            'genders'     => Employee::genders(),
            'datas'       => $employees->with('user')->paginate($perPage)->onEachSide(0),
            'breadcrumbs' => [['label' => __('app.label.employee'), 'href' => route('employee.index')]],
        ]);
    }

    public function create()
    {
    }

    public function store(EmployeeStoreRequest $request)
    {
        try {
            Employee::create([
                'code'   => $request->code,
                'name'   => $request->name,
                'gender' => $request->gender,
                'status' => $request->status,
            ]);
            return back()->with('success', __('app.label.created_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.created_error') . $th->getMessage());
        }
    }

    public function show(Employee $employee)
    {
    }

    public function edit(Employee $employee)
    {
    }

    public function update(EmployeeUpdateRequest $request, Employee $employee)
    {
        try {
            $employee->update([
                'code'   => $request->code,
                'name'   => $request->name,
                'gender' => $request->gender,
                'status' => $request->status,
            ]);
            return back()->with('success', __('app.label.updated_successfully'));
        } catch(\Throwable $th) {
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
