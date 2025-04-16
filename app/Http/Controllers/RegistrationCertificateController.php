<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\RegistrationCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class RegistrationCertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:registrationcertificate create', ['only' => ['create', 'store']]);
        $this->middleware('permission:registrationcertificate read', ['only' => ['index', 'show']]);
        $this->middleware('permission:registrationcertificate update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:registrationcertificate delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    public function index(Request $request)
    {
        $registrationcertificates = RegistrationCertificate::query()->with(['employee', 'user']);

        $registrationcertificates->where(function($query) use ($request) {
            $search = '%' . $request->search . '%';
            $fields = ['registrationNumber', 'competence', 'validFrom', 'validUntil'];

            $query->whereHas('employee', fn($q) => $q->where('name', 'LIKE', $search))
                ->orWhereHas('user', fn($q) => $q->where('first_name', 'LIKE', $search))->orWhere(function($q) use (
                    $fields,
                    $search
                ) {
                    foreach($fields as $field) {
                        $q->orWhere($field, 'LIKE', $search);
                    }
                });
        });
        if($request->filled('status')) {
            $registrationcertificates->where('status', $request->status);
        }

        if($request->has(['field', 'order'])) {
            $registrationcertificates->orderBy($request->field, $request->order);
        }
        $perPage   = $request->has('perPage') ? $request->perPage : 10;
        $paginated = $registrationcertificates->paginate($perPage)->onEachSide(0);
        $today     = Carbon::today();
        $datas     = $paginated->getCollection()->map(function($rc) use ($today) {
            $validUntil = Carbon::parse($rc->validUntil);

            if($validUntil->lessThanOrEqualTo($today)) {
                $rc->calculatedStatus = RegistrationCertificate::STATUS_EXPIRED;
            }
            elseif($validUntil->lessThanOrEqualTo($today->copy()->addDays(180))) {
                $rc->calculatedStatus = RegistrationCertificate::STATUS_INACTIVE;
            }
            else {
                $rc->calculatedStatus = RegistrationCertificate::STATUS_VALID;
            }

            return $rc;
        });
        if($request->filled('calculatedStatus')) {
            $datas = $datas->filter(function($rc) use ($request) {
                return $rc->calculatedStatus === $request->calculatedStatus;
            })->values();
        }

        $paginated->setCollection($datas);
        return Inertia::render('RegistrationCertificate/Index', [
            'title'       => __('app.label.registrationcertificate'),
            'filters'     => $request->all(['search', 'field', 'order', 'calculatedStatus']),
            'perPage'     => (int)$perPage,
            'statuses'    => RegistrationCertificate::statuses(),
            'types'       => Employee::types(),
            'datas'       => $paginated,
            'breadcrumbs' => [
                [
                    'label' => __('app.label.registrationcertificate'),
                    'href'  => route('registrationcertificate.index')
                ]
            ],
        ]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employeeId'         => 'required|exists:employee,id',
            'registrationNumber' => 'required|string|max:100|unique:registration_certificate,registrationNumber',
            'competence'         => 'required|string|max:200',
            'validFrom'          => 'required|date',
            'validUntil'         => 'required|date|after_or_equal:validFrom',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $validUntil = Carbon::parse($request->validUntil);
            $today      = Carbon::today();

            if($validUntil->lessThanOrEqualTo($today)) {
                $status = RegistrationCertificate::STATUS_EXPIRED;
            }
            elseif($validUntil->lessThanOrEqualTo($today->copy()->addDays(180))) {
                $status = RegistrationCertificate::STATUS_INACTIVE;
            }
            else {
                $status = RegistrationCertificate::STATUS_VALID;
            }

            RegistrationCertificate::create([
                'registered_by'      => auth()->user()->id,
                'employeeId'         => $request->employeeId,
                'registrationNumber' => $request->registrationNumber,
                'competence'         => $request->competence,
                'validFrom'          => $request->validFrom,
                'validUntil'         => $request->validUntil,
                'status'             => $status,
            ]);

            return back()->with('success', __('app.label.created_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.created_error') . $th->getMessage());
        }
    }

    public function show(RegistrationCertificate $registrationcertificate)
    {
    }

    public function edit(RegistrationCertificate $registrationcertificate)
    {
    }

    public function update(Request $request, $id)
    {
        $registrationCertificate = RegistrationCertificate::findOrFail($id);
        $validator               = Validator::make($request->all(), [
            'employeeId'         => 'required|exists:employee,id',
            'registrationNumber' => 'required|string|max:100|unique:registration_certificate,registrationNumber,' . $id,
            'competence'         => 'required|string|max:200',
            'validFrom'          => 'required|date',
            'validUntil'         => 'required|date|after_or_equal:validFrom',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $validUntil = Carbon::parse($request->validUntil);
            $today      = Carbon::today();

            if($validUntil->lessThanOrEqualTo($today)) {
                $status = RegistrationCertificate::STATUS_EXPIRED;
            }
            elseif($validUntil->lessThanOrEqualTo($today->copy()->addDays(180))) {
                $status = RegistrationCertificate::STATUS_INACTIVE;
            }
            else {
                $status = RegistrationCertificate::STATUS_VALID;
            }

            $registrationCertificate->update([
                'employeeId'         => $request->employeeId,
                'registrationNumber' => $request->registrationNumber,
                'competence'         => $request->competence,
                'validFrom'          => $request->validFrom,
                'validUntil'         => $request->validUntil,
                'status'             => $status,
            ]);

            return back()->with('success', __('app.label.updated_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.updated_error') . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $registrationCertificate = RegistrationCertificate::findOrFail($id);
            $registrationCertificate->delete();
            return back()->with('success', __('app.label.deleted_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error') . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $registrationcertificates = RegistrationCertificate::whereIn('id', $request->id);
            $registrationcertificates->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . "RegistrationCertificate"]));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . "RegistrationCertificate"]) . $th->getMessage());
        }
    }
}
