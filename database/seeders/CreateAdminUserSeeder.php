<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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
$user = User::create([
'name' => 'super admin',
'phone' => '09871058',
'password' => bcrypt('12345678')
]);
$user->company()->create(['name' => 'Autocare',]);
//$role = Role::create(['name' => 'SuperAdmin']);
//$permissions = Permission::pluck('id','id')->all();
//$role->syncPermissions($permissions);
//$user->assignRole([$role->id]);
$user->assignRole('SuperAdmin');
}
}
