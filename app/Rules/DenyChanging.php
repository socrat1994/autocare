<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class DenyChanging implements Rule
{

    private $changed;
    public function __construct($changed_value)
    {
      $this->changed = $changed_value;
    }

    public function passes($attribute, $value)
    {
      if(!$value)
        {return true;}
        return false;
    }

    public function message()
    {
        return 'you can not change '. $this->changed .' here';
    }
}
