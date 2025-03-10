<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pegawai\PegawaiIndexRequest;
use App\Http\Requests\Pegawai\PegawaiStoreRequest;
use App\Http\Requests\Pegawai\PegawaiUpdateRequest;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PegawaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pegawai create', ['only' => ['create', 'store']]);
        $this->middleware('permission:pegawai read', ['only' => ['index', 'show']]);
        $this->middleware('permission:pegawai update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:pegawai delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(PegawaiIndexRequest $request)
    {
        $pegawais = Pegawai::query();
        if($request->has('search')) {
            $pegawais->where('name', 'LIKE', "%" . $request->search . "%");
        }
        if($request->has(['field', 'order'])) {
            $pegawais->orderBy($request->field, $request->order);
        }
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render('Pegawai/Index', [
            'title'          => __('pegawai'),
            'filters'        => $request->all(['search', 'field', 'order']),
            'perPage'        => (int)$perPage,
            'pegawaipegawai' => Pegawai::statusPegawai(),
            'statuses'       => Pegawai::statuses(),
            'pegawais'       => $pegawais->with('user')->paginate($perPage)->onEachSide(0),
            'breadcrumbs'    => [['label' => __('pegawai'), 'href' => route('pegawai.index')]],
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
    public function store(PegawaiStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name'   => $request->name,
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'password'     => Hash::make("password"),
            ]);
            $user->assignRole(User::ROLE_PEGAWAI);
            Pegawai::create([
                'user_id'            => $user->id,
                'jabatan_id'         => $request->jabatan_id,
                'departemen_id'      => $request->departemen_id,
                'status_kepegawaian' => $request->status_kepegawaian,
                'status'             => $request->status,
            ]);

            DB::commit();
            return back()->with('success', __('app.label.created_successfully'));
        } catch(\Throwable $th) {
            DB::rollBack();
            return back()->with('error', __('app.label.created_error') . ' ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pegawai $pegawai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pegawai $pegawai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PegawaiUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $pegawai = Pegawai::findOrFail($id);
            $user    = $pegawai->user;

            $user->update([
                'first_name'   => $request->name,
                'phone_number' => $request->phone_number,
                'email'        => $request->email,
            ]);
            $pegawai->update([
                'jabatan_id'         => $request->jabatan_id,
                'departemen_id'      => $request->departemen_id,
                'status_kepegawaian' => $request->status_kepegawaian,
                'status'             => $request->status,
            ]);

            DB::commit();
            return back()->with('success', __('app.label.updated_successfully'));
        } catch(\Throwable $th) {
            DB::rollBack();
            return back()->with('error', __('app.label.updated_error') . ' ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pegawai $pegawai)
    {
        try {
            $pegawai->delete();
            return back()->with('success', __('app.label.deleted_successfully'));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error') . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $pegawais = Pegawai::whereIn('id', $request->id);
            $pegawais->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . "Pegawai"]));
        } catch(\Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . "Pegawai"]) . $th->getMessage());
        }
    }
}
