<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Branch;
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

    public function anychang(User $user, Branch $branch)
    {
       $company = $user->company()->get();
        return $branch->company_id == $company[0]->id?
         Response::allow()
        : Response::deny('You do not own this branch.');
    }
}
