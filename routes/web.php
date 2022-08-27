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
use App\Http\Controllers\MyFunction;
use App\Models\Branch;

Route::get('/',function(Request $request){
  $roles_arr = Role::query()->select('name')->get();
  $roles_arr = to_array($roles_arr, 'name');
  return array_search('dmin', $roles_arr, true) !== false?'yes':'no';// view('home');
});


Route::get('setlang/{locale}', [IntiController::Class, 'setlang'])->name('setlang');
Auth::routes();

Route::group(['middleware' => ['auth']], function() {
  Route::resource('roles' , RoleController::class);
  Route::resource('users','UserController');
  Route::resource('products','ProductController');});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/branchdeledi', [BranchController::class, 'del_edi'])->name('branchdeledi');
Route::get('/branchshow', [BranchController::class, 'show'])->name('branchshow');
Route::resources(['employee' => EmployeeController::class,
'branch' => BranchController::class,]);
