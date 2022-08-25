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
}
