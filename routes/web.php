<?php

use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\IntiController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

Route::get('/',function(){
    return view('welcome');
});


Route::get('setlang/{locale}', [IntiController::Class, 'setlang'])->name('setlang');
Route::get('deletecookie', [IntiController::Class, 'del_cookie'])->name('deletecookie');
Route::get('/', [IntiController::Class, 'inti']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resources(['employee' => EmployeeController::class,
'branch' => BranchController::class,]);
