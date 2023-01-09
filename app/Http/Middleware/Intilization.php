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
use App\HelperClasses\Iteration;

class Intilization
{

  public function handle(Request $request, Closure $next)
  {
    if(!Session::has('company'))
    {
      $arr = new Iteration();
      $user = Auth::user();
      if ($user) {
        $user = User::find($user->id);
        $employee = $user->transfers->last();
        if($employee)
        {
          $branch = $employee->branch_id;
          $request->session()->put('branch', $branch);
          $company = Branch::query()->select('company_id')->where('id', $branch)->get();
          $active = Company::query()->select('active')->where('id', $company[0]->company_id)->get();
          $request->session()->put('company', $company[0]->company_id);
          $request->session()->put('active', $active[0]->active);
          $request->session()->put('role', $user->roles);
          $request->session()->put('permission', $arr->to_array($user->getAllPermissions(), 'name'));
          $request->session()->save();
        }
        else
        {
          $user = User::find($user->id);
          $company = $user->company()->first();
          $request->session()->put('active', $company->active);
          $request->session()->put('company', $company->id);
          $request->session()->put('role', $user->roles);
          $request->session()->put('permission', $arr->to_array($user->getAllPermissions(), 'name'));
          $request->session()->save();
        }
      }
    }
    return $next($request);
  }
}
