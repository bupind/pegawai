<?php

namespace App\Http\Controllers;

use App\Http\Requests\License\LicenseIndexRequest;
use App\Http\Requests\License\LicenseStoreRequest;
use App\Http\Requests\License\LicenseUpdateRequest;
use App\Models\License;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function index(LicenseIndexRequest $request)
    {
        $licenses = License::query()->with(['employee']);
        if($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $licenses->where(function($query) use ($search) {
                $query->where('type', 'LIKE', $search)->orWhere('registrationNumber', 'LIKE', $search)
                    ->orWhere('validFrom', 'LIKE', $search)->orWhere('validUntil', 'LIKE', $search)
                    ->orWhereHas('employee', function($q) use ($search) {
                        $q->where('name', 'LIKE', $search);
                    });
            });
        }
        if($request->filled('status')) {
            $licenses->where('status', $request->status);
        }

        if($request->has(['field', 'order'])) {
            $licenses->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render('License/Index', [
            'title'       => __('app.label.license'),
            'filters'     => $request->all(['search', 'field', 'order']),
            'perPage'     => (int)$perPage,
            'types'       => License::types(),
            'statuses'    => License::statuses(),
            'datas'       => $licenses->with(['employee'])->paginate($perPage)->onEachSide(0),
            'breadcrumbs' => [['label' => __('app.label.license'), 'href' => route('license.index')]],
        ]);
    }

    public function create()
    {
    }

    public function store(LicenseStoreRequest $request)
    {
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
                $status = License::STATUS_ACTIVE;
            }
            License::create([
                'employeeId'         => $request->employeeId,
                'type'               => $request->type,
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

    public function update(LicenseUpdateRequest $request, License $license)
    {
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
                $status = License::STATUS_ACTIVE;
            }

            $license->update([
                'employeeId'         => $request->employeeId,
                'type'               => $request->type,
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
