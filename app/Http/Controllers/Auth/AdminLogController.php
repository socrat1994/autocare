<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminLogController extends Controller
{

    public function __construct()
    {
      //  $this->middleware('guest')->except('logout');
       $this->middleware('auth:admin')->only('showadmin');
       $this->middleware('guest')->except('showadmin');

    }

    public function showAdminLoginForm()
    {
        return view('auth.adminlogin');
    }

    public function showadmin()
    {
        return view('adminpanel');
    }

    public function login(Request $request)
    {
      $validator =  Validator::make($request->all(), [
            'phone'   => 'required|exists:admins,phone',
            'password' => 'required|min:6'
        ]);
        if($validator->fails()){
        return back()->withErrors($validator->errors());}//->withInput($request->only('phone', 'remember'));}
        if (Auth::guard('admin')->attempt(['phone' => $request->phone, 'password' => $request->password], $request->get('remember'))) {
            return redirect()->intended('/admin/panel');
        }

    //return redirect()->back()->withErrors($validator);
//withInput($request->only('phone', 'remember'));
}
}
