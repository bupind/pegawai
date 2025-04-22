<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
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
use App\Http\Controllers\UserController;
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

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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

    Route::resource('user', UserController::class);
    Route::post('user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');

    Route::resource('permission', PermissionController::class);
    Route::post('permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])
        ->name('permission.destroy-bulk');

    Route::resource('setting', SettingController::class)->except('create', 'store', 'show', 'edit', 'destory');

    Route::resource('employee', EmployeeController::class);
    Route::post('employee/destroy-bulk', [EmployeeController::class, 'destroyBulk'])->name('employee.destroy-bulk');

    Route::resource('registrationcertificate', RegistrationCertificateController::class);
    Route::post('registrationcertificate/destroy-bulk', [RegistrationCertificateController::class, 'destroyBulk'])
        ->name('registrationcertificate.destroy-bulk');

    Route::resource('license', LicenseController::class);
    Route::post('license/destroy-bulk', [LicenseController::class, 'destroyBulk'])->name('license.destroy-bulk');

});
