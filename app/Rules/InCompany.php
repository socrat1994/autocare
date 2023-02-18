<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InCompany implements Rule
{
  public function passes($attribute, $value)
  {
    $ids=DB::table(DB::raw('(select
    `users`.`id` as `id`
    from
    `users`
    inner join `employees` on `users`.`id` = `employees`.`user_id`
    inner join `branches` on `branches`.`id` = `employees`.`branch_id`
    where `branches`.`company_id` ='.session('company').' and
    `employees`.`id` in (select max(`id`) from `employees` group by `user_id`)
    ) as employees'))
    ->where('employees.id', $value)->get();
    if(sizeof($ids))
    {
      return true;
    }
    return false;
  }

  public function message()
  {
    return  __('the user has been deleted by somebody else or it is not found');
  }
}
