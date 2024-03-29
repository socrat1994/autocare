<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Cookie;

class AnyChangingPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function branches(User $user, Branch $branch)
    {
       $company = session('company');
        return $branch->company_id == $company?
         Response::allow()
        : Response::deny('these data do not belonge to you.');
    }
}
