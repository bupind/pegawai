<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UserIndexRequest;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Throwable;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:user create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user read', ['only' => ['index', 'show']]);
        $this->middleware('permission:user update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user delete', ['only' => ['destroy', 'destroyBulk']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserIndexRequest $request)
    {
        $perPage = $request->input('perPage', 10);

        $users = User::role(User::ROLE_SUPERUSER)->when($request->filled('search'), function($query) use ($request) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('last_name', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('phone_number', 'LIKE', "%" . $request->search . "%")
                    ->orWhere('email', 'LIKE', "%" . $request->search . "%");
            });
        })->when($request->filled(['field', 'order']), function($query) use ($request) {
            $query->orderBy($request->field, $request->order);
        })->paginate($perPage)->onEachSide(0);

        return Inertia::render('Admin/Index', [
            'title'       => __('app.label.admin'),
            'filters'     => $request->only(['search', 'field', 'order']),
            'perPage'     => (int)$perPage,
            'users'       => $users,
            'breadcrumbs' => [['label' => __('app.label.admin'), 'href' => route('admin.index')]],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'password'     => Hash::make($request->password),
            ]);
            $user->assignRole(User::ROLE_SUPERUSER);
            DB::commit();
            return back()->with('success', __('app.label.created_successfully', ['name' => $user->name]));
        } catch(Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.created_error', ['name' => __('app.label.user')]) . $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->update([
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'password'     => $request->password ? Hash::make($request->password) : $user->password,
            ]);
            DB::commit();
            return back()->with('success', __('app.label.updated_successfully', ['name' => $user->name]));
        } catch(Throwable $th) {
            DB::rollback();
            return back()->with('error', __('app.label.updated_error', ['name' => $user->name]) . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => $user->name]));
        } catch(Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => $user->name]) . $th->getMessage());
        }
    }

    public function destroyBulk(Request $request)
    {
        try {
            $users = User::whereIn('id', $request->id);
            $users->delete();
            return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.user')]));
        } catch(Throwable $th) {
            return back()->with('error', __('app.label.deleted_error', ['name' => count($request->id) . ' ' . __('app.label.user')]) . $th->getMessage());
        }
    }
}
