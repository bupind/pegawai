<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jabatan\JabatanIndexRequest;
use App\Http\Requests\Jabatan\JabatanStoreRequest;
use App\Http\Requests\Jabatan\JabatanUpdateRequest;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JabatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:jabatan create', ['only' => ['create', 'store']]);
        $this->middleware('permission:jabatan read', ['only' => ['index', 'show']]);
        $this->middleware('permission:jabatan update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:jabatan delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(JabatanIndexRequest $request)
    {
        $jabatans = Jabatan::query();
        if($request->has('search')) {
            $jabatans->where('name', 'LIKE', "%" . $request->search . "%");
        }
        if($request->has(['field', 'order'])) {
            $jabatans->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render('Jabatan/Index', [
            'title'       => __('jabatan'),
            'filters'     => $request->all(['search', 'field', 'order']),
            'perPage'     => (int)$perPage,
            'statuses'    => Jabatan::statuses(),
            'jabatans'    => $jabatans->with('user')->paginate($perPage)->onEachSide(0),
            'breadcrumbs' => [['label' => __('jabatan'), 'href' => route('jabatan.index')]],
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
    public function store(JabatanStoreRequest $request)
    {
        try {
            Jabatan::create([
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
    public function show(Jabatan $jabatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jabatan $jabatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JabatanUpdateRequest $request, Jabatan $jabatan)
    {
        try {
            $jabatan->update([
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
    public function destroy(Jabatan $jabatan)
    {
        try {
            $jabatan->delete();
            return back()->with('success', __('app.label.deleted_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error') . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $jabatans = Jabatan::whereIn('id', $request->id);
            $jabatans->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . "Jabatan"]));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . "Jabatan"]) . $th->getMessage());
        }
    }
}
