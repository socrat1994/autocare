<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LowerRole implements Rule
{
    Private $userrole;

    public function __construct()
    {
      $this->userrole = session('role');
    }

    public function passes($attribute, $value)
    {
      $role = Role::findByName($value);
      foreach($this->userrole as $userrole)
      {
        if($userrole->id <= $role->id)
        {
          return true;
        }
      }
        return false;
    }

    public function message()
    {
        return 'you can not assign higher role than yours.';
    }
}
