<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\License;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class LicenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:license create', ['only' => ['create', 'store']]);
        $this->middleware('permission:license read', ['only' => ['index', 'show']]);
        $this->middleware('permission:license update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:license delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    public function index(Request $request)
    {
        $licenses = License::query()->with(['employee']);

        if($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $licenses->where(function($query) use ($search) {
                $query->where('registrationNumber', 'LIKE', $search)->orWhere('validFrom', 'LIKE', $search)
                    ->orWhere('validUntil', 'LIKE', $search)->orWhereHas('employee', function($q) use ($search) {
                        $q->where('name', 'LIKE', $search)->orWhere('type', 'LIKE', $search);
                    });
            });
        }

        if($request->has(['field', 'order'])) {
            $licenses->orderBy($request->field, $request->order);
        }

        $perPage   = $request->has('perPage') ? $request->perPage : 10;
        $paginated = $licenses->paginate($perPage)->onEachSide(0);
        $today     = Carbon::today();
        $datas = $paginated->getCollection()->map(function($license) use ($today) {
            $validUntil = Carbon::parse($license->validUntil);

            if($validUntil->lessThanOrEqualTo($today)) {
                $license->calculatedStatus = License::STATUS_EXPIRED;
            }
            elseif($validUntil->lessThanOrEqualTo($today->copy()->addDays(180))) {
                $license->calculatedStatus = License::STATUS_INACTIVE;
            }
            else {
                $license->calculatedStatus = License::STATUS_VALID;
            }

            return $license;
        });

        if($request->filled('calculatedStatus')) {
            $datas = $datas->filter(function($license) use ($request) {
                return $license->calculatedStatus === $request->calculatedStatus;
            })->values();
        }

        $paginated->setCollection($datas);

        return Inertia::render('License/Index', [
            'title'       => __('app.label.license'),
            'filters'     => $request->all(['search', 'field', 'order', 'calculatedStatus']),
            'perPage'     => (int)$perPage,
            'statuses'    => License::statuses(),
            'types'       => Employee::types(),
            'datas'       => $paginated,
            'breadcrumbs' => [
                ['label' => __('app.label.license'), 'href' => route('license.index')],
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
            'registrationNumber' => 'required|string|max:100|unique:license,registrationNumber',
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
                $status = License::STATUS_EXPIRED;
            }
            elseif($validUntil->lessThanOrEqualTo($today->copy()->addDays(180))) {
                $status = License::STATUS_INACTIVE;
            }
            else {
                $status = License::STATUS_VALID;
            }

            License::create([
                'employeeId'         => $request->employeeId,
                'registrationNumber' => $request->registrationNumber,
                'validFrom'          => $request->validFrom,
                'validUntil'         => $request->validUntil,
                'status'             => $status,
            ]);

            return back()->with('success', __('app.label.created_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.created_error') . $th->getMessage());
        }
    }

    public function show(License $license)
    {
    }

    public function edit(License $license)
    {
    }

    public function update(Request $request, $id)
    {
        $license   = License::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'employeeId'         => 'required|exists:employee,id',
            'registrationNumber' => 'required|string|max:100|unique:license,registrationNumber,' . $id,
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
                $status = License::STATUS_EXPIRED;
            }
            elseif($validUntil->lessThanOrEqualTo($today->copy()->addDays(180))) {
                $status = License::STATUS_INACTIVE;
            }
            else {
                $status = License::STATUS_VALID;
            }

            $license->update([
                'employeeId'         => $request->employeeId,
                'registrationNumber' => $request->registrationNumber,
                'validFrom'          => $request->validFrom,
                'validUntil'         => $request->validUntil,
                'status'             => $status,
            ]);

            return back()->with('success', __('app.label.updated_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.updated_error') . $th->getMessage());
        }
    }

    public function destroy(License $license)
    {
        try {
            $license->delete();
            return back()->with('success', __('app.label.deleted_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error') . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $licenses = License::whereIn('id', $request->id);
            $licenses->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . "License"]));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . "License"]) . $th->getMessage());
        }
    }
}
