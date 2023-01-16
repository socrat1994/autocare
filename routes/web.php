<?php

use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\IntiController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\HelperClasses\Iteration;
use App\Http\Controllers\Auth\AdminLogController;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
//hide moving and show
if(env('APP_ENV') === 'production')
{
  URL::forceScheme('https');
}

Route::get('/',function(){
  return view('home');
});

Auth::routes();
Route::get('setlang/{locale}', [IntiController::Class, 'setlang'])->name('setlang');
Route::group(['middleware' => ['isactive' , 'auth']], function() {

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
    Route::controller(EmployeeController::class)->prefix('employee')->name('employee.')->group(function(){
      Route::post('/add', 'store')->name('add');
      Route::post('/update', 'del_edi')->name('update');
      Route::get('/', 'index')->name('index');
      Route::get('/show', 'show')->name('show');
    });
    Route::controller(VehicleController::class)->prefix('vehicle')->name('vehicle.')->group(function(){
      Route::post('/add', 'store')->name('add');
      Route::post('/update', 'del_edi')->name('update');
      Route::post('/move', 'move')->name('move');
      Route::post('/changenum', 'change_num')->name('changenum');
      Route::get('/show/{options}', 'show')->name('show');
    });
    Route::controller(MovementController::class)->prefix('movement')->name('movement.')->group(function(){
      Route::post('/add', 'store')->name('add');
      Route::post('/update', 'del_edi')->name('update');
      Route::post('/move', 'move')->name('move');
      Route::post('/changenum', 'change_num')->name('changenum');
      Route::get('/show/{options}', 'show')->name('show');
    });

    Route::resources([
    'branch' => BranchController::class,]);
  });
