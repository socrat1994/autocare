<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


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

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
              'phone_verified_at' => date($format = 'Y-m-d', $max = 'now'),
            ];
        });
    }
}
