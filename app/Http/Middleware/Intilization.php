<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\User;

class Intilization
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle(Request $request, Closure $next)
    {
      if(!Session::has('company'))
      {
        $user = Auth::user();
        if ($user) {
          $company = $user->company()->get('id');
          $request->session()->put('company', $company[0]->id);
        }
      }
        return $next($request);
    }
}
