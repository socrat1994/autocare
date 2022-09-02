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
      $this->userrole = Auth::user()->roles->first()->id;
    }

    public function passes($attribute, $value)
    {
        $role = Role::findByName($value);
        return $this->userrole <= $role->id;
    }

    public function message()
    {
        return 'you can not assign higher role than yours.';
    }
}
