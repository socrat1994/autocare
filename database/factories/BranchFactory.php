<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Company;
use Faker\Generator as Faker;

class BranchFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'location' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'company_id' => Company::factory(),
    ];}

}
