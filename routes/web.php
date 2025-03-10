<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [GuestController::class, 'index'])->name('index');

Route::get('/set-locale/{locale}', function($locale) {
    Session::put('locale', $locale);
    return back();
})->name('set-locale');

Route::get('dropdown/bumn', [DataController::class, 'bumns'])->name('dropdown.bumn');

Route::prefix('system')->middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function() {
    Route::prefix('dropdown')->group(function() {
        Route::get('users', [DataController::class, 'users'])->name('dropdown.users');
        Route::get('jabatan', [DataController::class, 'jabatan'])->name('dropdown.jabatan');
        Route::get('department', [DataController::class, 'department'])->name('dropdown.department');

    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('admin', AdminController::class);
    Route::post('admin/destroy-bulk', [AdminController::class, 'destroyBulk'])->name('admin.destroy-bulk');

    Route::resource('role', RoleController::class);
    Route::post('role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('permission', PermissionController::class);
    Route::post('permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])
        ->name('permission.destroy-bulk');

    Route::resource('setting', SettingController::class)->except('create', 'store', 'show', 'edit', 'destory');

    Route::resource('pegawai', PegawaiController::class);
    Route::post('pegawai/destroy-bulk', [PegawaiController::class, 'destroyBulk'])->name('pegawai.destroy-bulk');

    Route::resource('jabatan', JabatanController::class);
    Route::post('jabatan/destroy-bulk', [JabatanController::class, 'destroyBulk'])->name('jabatan.destroy-bulk');

    Route::resource('department', DepartementController::class);
    Route::post('department/destroy-bulk', [DepartementController::class, 'destroyBulk'])->name('department.destroy-bulk');

});
