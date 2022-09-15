<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Branch;
use App\Models\User;
use App\Models\Employee;
use Spatie\Permission\Models\Role;


use Faker\Generator as Faker;

class EmployeeFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'moved_at' => date($format = 'Y-m-d', $max =123*365*24*60*60),
    ];}
    public function configure()
    {
      return $this->afterCreating(function (Employee $employee) {
        $user = $employee->user;
        return $user->removeRole('Owner');;
      });
    }
}
