<?php

use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IntiController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

Route::get('/',function(){
    return view('welcome');
});


Route::get('setlang/{locale}', [IntiController::Class, 'setlang'])->name('setlang');
Route::post('deletecookie', [IntiController::Class, 'del_cookie'])->name('deletecookie');
Route::get('/', [IntiController::Class, 'inti']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/addbranches', [App\Http\Controllers\BranchController::class, 'index'])->name('addbranches');
Route::post('/addbranches', [App\Http\Controllers\BranchController::class, 'store'])->name('addbranches');
Route::resources(['employee' => EmployeeController::class,
]);
