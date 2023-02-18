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
use App\Rules\InCompany;
use App\Rules\DenyChanging;
use app\Policies\YourEmployeesPolicy;
use Illuminate\Support\Facades\Gate;


class EmployeeController extends Controller
{

  public function __construct()
  {
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|DataEntry|add-employee'])->only(['store', 'index']);
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|edit-employee'])->only(['show', 'del_edi']);
  }

  public function index()
  {
    return view('employee');
  }

  public function common(Request $request)
  {
    $arr = new iteration;
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
        'role.*' => ['bail','required', 'string', Rule::in($common['roles']), new LowerRole()],
        'permission.*' => ['required', 'string', Rule::in(session('permission'))],
        'branch_id' => ['required', 'integer', Rule::in($common['branches'])],
        'moved_at' => ['required', 'date', 'after:1900-08-11', 'before:2100-08-11'],];
        return $common;
      }

      public function in_compny()
      {
        $arr = new iteration();
        $ids = $arr->to_array(User::query()->select('id')->where('company_id', session('company'))->get(), "id");
        $subrules = ['id' => ['required', 'integer', Rule::in($ids)],];
        return $subrules;
      }

      static  function for_store($data)
      {
        $arr = new iteration;
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
      $arr = new iteration;
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

    static function formove($data)
    {
      $user = User::find($data['id']);
      $employee = $user->transfers->last();
      $last_branch = [$employee->branch_id];
      $last_moving =  $employee->moved_at;
      $validated = Validator::make($data, ['branch_id' => [Rule::notIn($last_branch)],
      'moved_at' => ['after:'.$last_moving]]);
      if ($validated->fails()) {
        $status = $validated->errors();
        $error = true;
      }
      else{
        $employee = Employee::create([
          'user_id' => $data['id'],
          'branch_id' => $data['branch_id'],
          'moved_at' => $data['moved_at']
        ]);

        $status = [
          "id"=>$employee->user_id,
          "branch_id"=>$employee->branch_id,
          "moved_at"=>$employee->moved_at,
        ];
      }
      return $status;
    }

    public function move(Request $request)
    {
      //add
      $common = $this->common($request);
      $arr = new iteration;
      $rules = array_merge(array_slice($this->rules, 5, 2), ['id' => ['required', 'integer', new InCompany()],]);
      return $arr->Iteration($common, $rules , EmployeeController::class, 'formove');
    }

    public function for_del_edi(Request $request)
    {
      $user = User::find($data['id']);
      if(is_null($user)) {
        $status[$i] = __('the user have been deleted before');
        continue;}
        $employee = $user->transfers->last();
        if($employee)
        {
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
            }
            else
            {
              $user->delete();
              $status[$i] = $user->id;
            }
          }
          else
          {
            $status[$i] = $response->message();
          }
        }
        else {
          $status[$i] = 'wrong id of employee';
        }
      }

      public function del_edi(Request $request)
      {
        $common = $this->common($request);
        $arr = new iteration;
        $rules = array_merge($arr->delete($this->rules, ['required']), ['id' => ['required', 'integer', new InCompany()],]);
        return $arr->Iteration($common, $rules , EmployeeController::class, 'for_del_edi');
      }
