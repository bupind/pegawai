<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RegistrationCertificateController;
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

Route::prefix('system')->middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function() {

    Route::prefix('dropdown')->group(function() {
        Route::get('employees', [DataController::class, 'employees'])->name('dropdown.employees');
        Route::get('certificates', [DataController::class, 'certificates'])->name('dropdown.certificates');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/employee', [DashboardController::class, 'employee'])->name('dashboard.employee');
    Route::get('/dashboard/certificates', [DashboardController::class, 'certificates'])->name('dashboard.certificates');
    Route::get('/dashboard/licenses', [DashboardController::class, 'licenses'])->name('dashboard.licenses');

    Route::resource('admin', AdminController::class);
    Route::post('admin/destroy-bulk', [AdminController::class, 'destroyBulk'])->name('admin.destroy-bulk');

    Route::resource('role', RoleController::class);
    Route::post('role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('permission', PermissionController::class);
    Route::post('permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])
        ->name('permission.destroy-bulk');

    Route::prefix('setting')->name('setting.')->group(function () {
        Route::resource('/', SettingController::class)->except('create', 'store', 'show', 'edit', 'destroy');
        Route::get('expired-status', [SettingController::class, 'expiredStatus'])->name('expired-status');
    });

    Route::resource('employee', EmployeeController::class);
    Route::post('employee/destroy-bulk', [EmployeeController::class, 'destroyBulk'])->name('employee.destroy-bulk');

    Route::resource('registrationcertificate', RegistrationCertificateController::class);
    Route::post('registrationcertificate/destroy-bulk', [RegistrationCertificateController::class, 'destroyBulk'])
        ->name('registrationcertificate.destroy-bulk');

    Route::resource('license', LicenseController::class);
    Route::post('license/destroy-bulk', [LicenseController::class, 'destroyBulk'])->name('license.destroy-bulk');

});
