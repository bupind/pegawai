<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationCertificate\RegistrationCertificateIndexRequest;
use App\Http\Requests\RegistrationCertificate\RegistrationCertificateStoreRequest;
use App\Http\Requests\RegistrationCertificate\RegistrationCertificateUpdateRequest;
use App\Models\RegistrationCertificate;
use Illuminate\Http\Request;
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

    public function index(RegistrationCertificateIndexRequest $request)
    {
        $registrationcertificates = RegistrationCertificate::query();
        if($request->has('search')) {
            $registrationcertificates->where('name', 'LIKE', "%" . $request->search . "%");
        }
        if($request->has(['field', 'order'])) {
            $registrationcertificates->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render('RegistrationCertificate/Index', [
            'title'       => __('app.label.registrationcertificate'),
            'filters'     => $request->all(['search', 'field', 'order']),
            'perPage'     => (int)$perPage,
            'statuses'    => RegistrationCertificate::statuses(),
            'types'       => RegistrationCertificate::types(),
            'datas'       => $registrationcertificates->with('user')->paginate($perPage)->onEachSide(0),
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

    public function store(RegistrationCertificateStoreRequest $request)
    {
        try {
            RegistrationCertificate::create([
                'registered_by'                 => auth()->user()->id,
                'employeeId'                    => $request->employeeId,
                'type'                          => $request->type,
                'registrationNumber'            => $request->registrationNumber,
                'competence'                    => $request->competence,
                'certificateOfCompetenceNumber' => $request->certificateOfCompetenceNumber,
                'validFrom'                     => $request->validFrom,
                'validUntil'                    => $request->validUntil,
                'status'                        => $request->status,

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

    public function update(RegistrationCertificateUpdateRequest $request,
        RegistrationCertificate $registrationcertificate)
    {
        try {
            $registrationcertificate->update([
                'employeeId'                    => $request->employeeId,
                'type'                          => $request->type,
                'registrationNumber'            => $request->registrationNumber,
                'competence'                    => $request->competence,
                'certificateOfCompetenceNumber' => $request->certificateOfCompetenceNumber,
                'validFrom'                     => $request->validFrom,
                'validUntil'                    => $request->validUntil,
                'status'                        => $request->status,
            ]);
            return back()->with('success', __('app.label.updated_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.updated_error') . $th->getMessage());
        }
    }

    public function destroy(RegistrationCertificate $registrationcertificate)
    {
        try {
            $registrationcertificate->delete();
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
