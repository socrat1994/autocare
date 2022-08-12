<?php

use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MoreInfoController;

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

Route::get('/',function(){
    return view('welcome');
});

//changing socrat repo 
Route::get('setlang/{locale}',function($locale){
  app()->setLocale($locale);
  Cookie::queue('locale', $locale, 1000000);
  return redirect()->back();
})->name('setlang');

Route::get('/',function(){
    return view('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/addbranches', [App\Http\Controllers\MoreInfoController::class, 'index'])->name('addbranches');
Route::post('/addbranches', [App\Http\Controllers\MoreInfoController::class, 'store'])->name('addbranches');
Route::Apiresources([
  'employee' => EmployeeController::class,
]);
