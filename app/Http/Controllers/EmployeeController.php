<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Validation\Rule;
use App\HelperClasses\Message;
use App\HelperClasses\Iteration;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Rules\LowerRole;
use App\Rules\DenyChanging;
use app\Policies\YourEmployeesPolicy;
use Illuminate\Support\Facades\Gate;


class EmployeeController extends Controller
{
  private $rules = [];
  private $status = [];
  static $it = 0;

  public function __construct()
  {
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|DataEntry|add-employee'])->only(['store', 'index']);
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|edit-employee'])->only(['show', 'del_edi']);
  }

  public function index()
  {
    return view('addemployees');
  }

  public function common(Request $request)
  {
    $arr = new ToArray;
    $datas = [];
    foreach(json_decode($request->pTableData, true) as $data)
    {
      $data['role']??null?$data['role'] = explode(",", $data['role']):[null];
      $data['permission']??null?$data['permission'] = explode(",", $data['permission']):[null];
      array_push($datas, $data);
    }
    $common = [
      'datas' => $datas,
      'branches' => $arr->to_array(Branch::query()->select('id')->where('company_id', session('company'))->get(), "id"),
      'roles' => $arr->to_array(Role::query()->select('name')->get(), "name"),];
      $this->rules = [
      'name' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
      'branch_id' => ['required', 'integer', Rule::in($common['branches'])],
      'role.*' => ['bail','required', 'string', Rule::in($common['roles']), new LowerRole()],
      'permission.*' => ['required', 'string', Rule::in(session('permission'))],
      'moved_at' => ['required', 'date', 'after:1900-08-11', 'before:2100-08-11'],];
      return $common;
  }

  static  function forstore($data)
  {
    $arr = new ToArray;
    $user = User::create([
      'name' => $data['name'],
      'phone' => $data['phone'],
      'password' => Hash::make($data['password']),
    ]);
    $employee = Employee::create([
      'user_id' => $user->id,
      'branch_id' => $data['branch_id'],
      'moved_at' => $data['moved_at']
    ]);
    $uroles = $user->assignRole($data['role']);
    $uroles = $arr->to_array($uroles->roles, 'name');
    $uroles = implode(",", $uroles);
    if($data['permission']??null)
    {
      $upermissions = $user->givePermissionTo($data['permission']);
      $upermissions = $arr->to_array($upermissions->permissions, 'name');
      $upermissions = implode(",", $upermissions);
    }
    $status = ["name"=>$user->name,
    "phone"=>$user->phone,
    "branch_id"=>$employee->branch_id,
    "moved_at"=>$employee->moved_at,
    "role"=>$uroles,
    "permission"=>$upermissions,
  ];
  return $status;
  }

  public function store(Request $request)
  {
    $common = $this->common($request);
    $arr = new ToArray;
    return $arr->Iteration($common, $this->rules , EmployeeController::class, 'forstore');
  }

  public function show()
  {
    $employees = DB::table(DB::raw('(select
    `users`.`id` as `id`,
    `users`.`name` as `name`,
    `users`.`phone` as `phone`,
    `employees`.`branch_id` as `branch_id`,
    `employees`.`moved_at` as `moved_at`,
    group_concat(roles.name) as role
    from
    `users`
    inner join `employees` on `users`.`id` = `employees`.`user_id`
    inner join `branches` on `branches`.`id` = `employees`.`branch_id`
    inner join `companies` on `companies`.`id` = `branches`.`company_id`
    inner join `model_has_roles` on `users`.`id` = `model_has_roles`.`model_id`
    inner join `roles` on `roles`.`id` = `model_has_roles`.`role_id`
    where `companies`.`id` ='.session('company').' and
    `employees`.`id` in (select max(`id`) from `employees` group by `user_id`)
    group by
    `phone`,
    `name`,
    `branch_id`,
    `moved_at`,
    `id`
    ) as employees'))
    ->join('model_has_permissions', 'employees.id', '=', 'model_has_permissions.model_id')
    ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
    ->select(['employees.*',
    DB::raw('group_concat(permissions.name) as permission'),])
    ->groupBy('phone' ,'name', 'branch_id', 'moved_at', 'id', 'role')->get();
    return response()->json(new Message($employees, '200', isset($error)?false:true, 'info', 'all branches of company', 'كل فروع الشركة'));
  }

  public function del_edi(Request $request)
  {//'id' => ['required', 'integer',],
    try {
      $arr = new Iteration();
      $i = 0;
      $common = $this->common($request);
      a: foreach(array_slice($common['datas'], $i, count($common['datas'])-$i) as $data){
        if(count($data) > 1)
        {
          $validated = Validator::make($data, $arr->delete($this->rules, ['required']));
          if ($validated->fails()) {
            $status[$i] = $validated->errors();
            $i++;
            $error = true;
            continue;
          }
        }
        $user = User::find($data['id']);
        if(is_null($user)) {
          $status[$i] = __('.the user have been deleted before');
          continue;}
          $employee = $user->transfers->last();
          if($employee){
            $response = Gate::inspect('your_employees', $employee);
            if($response->allowed())
            {
              if(count($data) > 1)
              {
                $user = $user->update($data);
                $employee = $employee->update($data);
                $user = User::find($data['id']);
                if($data['role']??null)
                {
                  $user->syncRoles($data['role']);
                }
                if($data['permission']??null)
                {
                  $user->syncPermissions($data['permission']);
                }
                $employee = $user->load('transfers');
                $status[$i] = [
                  "id"=>$user->id,
                  "name"=>$user->name,
                  "phone"=>$user->phone,
                  "branch_id"=>$employee->transfers[0]->branch_id,
                  "moved_at"=>$employee->transfers[0]->moved_at,
                  "role"=>implode(",", $arr->to_array($user->roles, 'name')),
                  "permission"=>implode(",", $arr->to_array($user->getAllPermissions(), 'name')),
                ];
                $i++;
              }
              else
              {
                $user->delete();
                $status[$i] = $user->id;
                $i++;
              }
            }
            else
            {
              $status[$i] = $response->message();
              $i++;
            }
          }
          else {
            $status[$i] = 'wrong id of employee';
            $i++;
          }
        }
      }catch (\Exception $e) {
        $status[$i] = $e->getMessage();
        $i++;
        goto a;}
        return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'هذه حالة كل عملية إدخال'));
      }
    }
