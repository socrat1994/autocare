<?php

use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;

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

Route::get('/mul',function(){
    return view('auth.login');
});

//mulahm updates in mulham repo.
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
Route::get('/moreinfo', [App\Http\Controllers\MoreInfoController::class, 'index'])->name('moreinfo');
