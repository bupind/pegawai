<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departement\DepartementIndexRequest;
use App\Http\Requests\Departement\DepartementStoreRequest;
use App\Http\Requests\Departement\DepartementUpdateRequest;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:departement create', ['only' => ['create', 'store']]);
        $this->middleware('permission:departement read', ['only' => ['index', 'show']]);
        $this->middleware('permission:departement update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:departement delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(DepartementIndexRequest $request)
    {
        $departements = Departemen::query();
        if($request->has('search')) {
            $departements->where('name', 'LIKE', "%" . $request->search . "%");
        }
        if($request->has(['field', 'order'])) {
            $departements->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render('Departement/Index', [
            'title'        => __('departement'),
            'filters'      => $request->all(['search', 'field', 'order']),
            'perPage'      => (int)$perPage,
            'statuses'     => Departemen::statuses(),
            'departements' => $departements->with('user')->paginate($perPage)->onEachSide(0),
            'breadcrumbs'  => [['label' => __('departement'), 'href' => route('department.index')]],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartementStoreRequest $request)
    {
        try {
            Departemen::create([
                'name'   => $request->name,
                'status' => $request->status,
            ]);
            return back()->with('success', __('app.label.created_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.created_error') . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Departemen $departement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departemen $departement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartementUpdateRequest $request, Departemen $departement)
    {
        try {
            $departement->update([
                'name'   => $request->name,
                'status' => $request->status,
            ]);
            return back()->with('success', __('app.label.updated_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.updated_error') . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departemen $departement)
    {
        try {
            $departement->delete();
            return back()->with('success', __('app.label.deleted_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error') . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $departements = Departemen::whereIn('id', $request->id);
            $departements->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . "Departement"]));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . "Departement"]) . $th->getMessage());
        }
    }
}
