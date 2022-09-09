<?php

use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\IntiController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\HelperClasses\ToArray;
use App\Http\Controllers\Auth\AdminLogController;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use App\Models\Company;

Route::get('/',function(){

return  Branch::factory()->count(1)->create();//view('home');
});

Auth::routes();
Route::group(['middleware' => ['isactive' , 'auth']], function() {
Route::get('setlang/{locale}', [IntiController::Class, 'setlang'])->name('setlang');


Route::group(['middleware' => ['auth']], function() {
  Route::resource('roles' , RoleController::class);
  Route::resource('users','UserController');
  Route::resource('products','ProductController');});

Route::get('/admin/panel', [AdminLogController::class, 'showadmin'])->name('adminpanel');
Route::get('/admin/login', [AdminLogController::class, 'showAdminLoginForm']);
Route::post('/admin/login', [AdminLogController::class, 'login'])->name('adminlogin');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/branchdeledi', [BranchController::class, 'del_edi'])->name('branchdeledi');
Route::get('/branchshow', [BranchController::class, 'show'])->name('branchshow');
Route::resources(['employee' => EmployeeController::class,
'branch' => BranchController::class,]);
});
