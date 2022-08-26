<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class RoleTableSeeder extends Seeder{
  /*** Run the database seeds.** @return void*/

  public function run(){
    $roles = [
      'SuperAdmin',
      'Owner',
      'Admin',
      'DataEntry',
      'Inspector',
      'Acountent',
      'Driver',
      ];
      foreach ($roles as $role) {
        Role::create(['name' => $role]);
      }
    }
  }
