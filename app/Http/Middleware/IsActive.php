<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

class IsActive
{

    public function handle(Request $request, Closure $next)
    {
        if (Session::has('active')) {
          if(!session('active'))
          {
          Auth::logout();
          return redirect('/')->withErrors(['msg' => 'your company is inactive for now']);
        }
        }
        return $next($request);
    }
}
