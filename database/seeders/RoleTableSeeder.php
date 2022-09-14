<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\HelperClasses\ToArray;
class RoleTableSeeder extends Seeder{
  /*** Run the database seeds.** @return void*/

  public function run(){
    $i=0;
    $arr = new ToArray();
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
        'add-branch',//0
        'edit-branch',//1
        'add-employee',//2
        'edit-employee',//3
        ];

      $roles_perms = [
        [0,1,2,3],//superadmin
        [1,1],//owner
        [1,1],//admin
        [1,1],//dataentry
        [1,1],//inspector
        [1,1],//acountent
        [1,1]//driver
      ];
      $roles_arr = Role::query()->select('name')->get();
      $roles_arr = $arr->to_array($roles_arr, 'name');
      $permissions_arr = Permission::query()->select('name')->get();
      $permissions_arr = $arr->to_array($permissions_arr, 'name');
      foreach ($permissions as $permission) {
        if(array_search($permission, $permissions_arr?$permissions_arr:['<>'], true) !== false)
        {
          continue;
        }
        $permission = Permission::create(['name' => $permission]);
      }

      foreach ($roles as $role) {
        if(array_search($role, $roles_arr?$roles_arr:['<>'], true) !== false)
        {
          $role = Role::findByName($role);
          foreach($roles_perms[$i] as $roles_perm)
          {
            $role->givePermissionTo($permissions[$roles_perm]);
          }
          $i++;
          continue;
        }
        $role = Role::create(['name' => $role]);
        foreach($roles_perms[$i] as $roles_perm)
        {
          $role->givePermissionTo($permissions[$roles_perm]);
        }
        $i++;
      }
    }
  }
