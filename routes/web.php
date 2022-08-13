<?php

use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

Route::get('/',function(){
    return view('welcome');
});


Route::get('setlang/{locale}',function($locale){
  app()->setLocale($locale);
  Cookie::queue('locale', $locale, 1000000);
  return redirect()->back();
})->name('setlang');

Route::get('/',function(){
//$user = Auth::user();
//$permission = Permission::create(['name' => 'edit articles']);
//$user->givePermissionTo($permission);
//  $user->assignRole('admin');
 return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/addbranches', [App\Http\Controllers\MoreInfoController::class, 'index'])->name('addbranches');
Route::post('/addbranches', [App\Http\Controllers\MoreInfoController::class, 'store'])->name('addbranches');
Route::resources(['employee' => EmployeeController::class,
]);
