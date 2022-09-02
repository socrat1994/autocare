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
use App\Models\Branch;
use App\HelperClasses\ToArray;

class Intilization
{

  public function handle(Request $request, Closure $next)
  {
    if(!Session::has('company'))
    {
      $user = Auth::user();
      if ($user) {
        $user=User::find($user->id);
        $employee = $user->transfers->last();
        if($employee)
        {
          $branch = $employee->branch_id;
          $request->session()->put('branch', $branch);
          $company = Branch::query()->select('company_id')->where('id', $branch)->get();
          $request->session()->put('company', $company);
          $request->session()->put('role', to_array($user->roles, 'name'));
          $request->session()->put('permission', to_array($user->permissions, 'name'));
          $request->session()->save();
        }
        else
        {
          $company = $user->company()->get('id');
          $request->session()->put('company', $company[0]->id);
          $request->session()->put('role', to_array($user->roles, 'name'));
          $request->session()->put('permission', to_array($user->permissions, 'name'));
          $request->session()->save();
        }
      }
    }
    return $next($request);
  }
}
