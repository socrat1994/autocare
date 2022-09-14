<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\HelperClasses\ToArray;

/**
* @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
*/
class UserFactory extends Factory
{
  /**
  * Define the model's default state.
  *
  * @return array<string, mixed>
  */
  public function definition()
  {
    return [
      'name' => $this->faker->name(),
      'phone' => $this->faker->unique()->e164PhoneNumber(),
      'password' => bcrypt('5'), // password
      'remember_token' => Str::random(10),
      'phone_verified_at' => date($format = 'Y-m-d', $max =5999999991),
    ];
  }

  public function unverified()
  {
    return $this->state(function (array $attributes) {
      return [
        'phone_verified_at' => date($format = 'Y-m-d', $max = 'now'),
      ];
    });
  }

  public function configure()
  {
    $arr = new ToArray();
    return $this->afterCreating(function (User $user) {
      $user->assignRole('Owner');
      return $user->assignRole($this->faker->randomElement($arr->to_array(Role::all(), 'name')));
    });
  }
}
