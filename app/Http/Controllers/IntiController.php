<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class IntiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('setlang');
    }

    public function setlang($locale)
    {
      app()->setLocale($locale);
      Cookie::queue('locale', $locale, 1000000);
      return redirect()->back();
    }

    public function inti()
    {
      if (!Cookie::has('company') or Cookie::get('company') === "") {
         $user = Auth::user();
         $company = $user->company()->get('id');
         Cookie::queue('company', $company[0]->id, 1000000);
        $cookie = Cookie::get('company');
      }
        return view('home');
    }

    public function del_cookie()
    {
      Cookie::queue('company', "", 1000000);
      $cookie = Cookie::get('company');
        return view('home');
    }

}
