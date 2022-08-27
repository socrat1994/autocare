<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\MyFunction;
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

      $permissions = [
        'add-branch',
        'edit-branch',
        ];
      $roles_arr = Role::query()->select('name')->get();
      $roles_arr = to_array($roles_arr, 'name');
      $permissions_arr = Permission::query()->select('name')->get();
      $permissions_arr = to_array($permissions_arr, 'name');
      foreach ($roles as $role) {
        if(array_search($role, $roles_arr, true) !== false)
        {
          continue;
        }
        Role::create(['name' => $role]);
      }

      foreach ($permissions as $permission) {
        if(array_search($permission, $permissions_arr, true) !== false)
        {
          continue;
        }
        Permission::create(['name' => $permission]);
      }
    }
  }
