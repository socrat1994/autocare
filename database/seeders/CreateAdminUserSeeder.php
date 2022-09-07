<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class CreateAdminUserSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {
    $phone = '0';
    $user = User::query()->select('phone')->where('phone', $phone)->first();
    $admin = Admin::query()->select('phone')->where('phone', $phone)->first();
    if(!$user)
    {
      $user = User::create([
        'name' => 'super admin',
        'phone' => $phone,
        'password' => bcrypt('0'),
      ]);
      $user->company()->create(['name' => 'Autocare', 'active' => '1']);
      //$role = Role::create(['name' => 'SuperAdmin']);
      //$permissions = Permission::pluck('id','id')->all();
      //$role->syncPermissions($permissions);
      //$user->assignRole([$role->id]);
      $user->assignRole('SuperAdmin');
    }
    if(!$admin)
    {
      $admin = Admin::create([
        'name' => 'the admin',
        'phone' => $phone,
        'password' => bcrypt('0')
      ]);
    }
  }
}
