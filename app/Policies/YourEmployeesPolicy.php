<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Cookie;

class YourEmployeesPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function your_employees(User $user, Employee $employee)
    {
       $company = session('company');
        return $employee->branch->company_id == $company?
         Response::allow()
        : Response::deny('these data do not belonge to you.');
    }
}
