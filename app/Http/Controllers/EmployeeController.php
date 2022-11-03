<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Validation\Rule;
use App\HelperClasses\Message;
use App\HelperClasses\ToArray;
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
  public function __construct()
  {
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|DataEntry|add-employee'])->only(['store', 'index']);
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|edit-employee'])->only(['show', 'del_edi']);
  }

  public function index()
  {
    return view('addemployees');
  }

  public function store(Request $request)
  {
    try
    {
      $i = 0;
      $arr = new ToArray();
      $datas = json_decode($request->pTableData, true);
      $company = session('company');
      $userrole = session('role');
      $branches = $arr->to_array(Branch::query()->select('id')->where('company_id', $company)->get(), "id");
      $roles = $arr->to_array(Role::query()->select('name')->get(), "name");
      $permissions = session('permission');
      a:foreach(array_slice($datas, $i, count($datas)-$i) as $data)
      {
        $data['role']??null?$data['role'] = explode(",", $data['role']):[null];
        $data['permission']??null?$data['permission'] = explode(",", $data['permission']):[null];
        $validated = Validator::make($data,
        ['name' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'branch_id' => ['required', 'integer', Rule::in($branches)],
        'role.*' => ['bail','required', 'string', Rule::in($roles), new LowerRole()],
        'permission.*' => ['required', 'string', Rule::in($permissions)],
        'moved_at' => ['required', 'date'],]);
        if ($validated->fails()) {
          $status[$i] = $validated->errors();
          $i++;
          $error = true;
          continue;
        }
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
        $user->assignRole($data['role']);
        if($data['permission']??null)
        {
          $user->givePermissionTo($data['permission']);
        }
        $status[$i] = $user->load('transfers');
        $i++;
      }
    }catch (\Exception $e) {
      $status[$i] = $e->getMessage();
      $i++;
      goto a;
    }
    return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
  }
  public function show($id)
  {
    $company = session('company');
    $branches = Branch::query()->select(['id', 'name', 'location', 'geolocation'])->where('company_id', $company)->get();
    return response()->json(new Message($branches, '200', isset($error)?false:true, 'info', 'all branches of company', 'كل فروع الشركة'));

  }

  public function del_edi(Request $request)
  {
    $i = 0;
    $arr = new ToArray();
    $data = json_decode($request->pTableData, true);
    $company = session('company');
    $userrole = session('role');
    $branches = $arr->to_array(Branch::query()->select('id')->where('company_id', $company)->get(), "id");
    $roles = $arr->to_array(Role::query()->select('name')->get(), "name");
    $permissions = session('permission');
    a: foreach(array_slice($datas, $i, count($datas)-$i) as $data){

      if(count($data) > 1)
      {
        $data['role']??null?$data['role'] = explode(",", $data['role']):[null];
        $data['permission']??null?$data['permission'] = explode(",", $data['permission']):[null];
        $validated = Validator::make($data,
        ['id' => ['required','integer'],
        'name' => ['string', 'max:255'],
        'phone' => ['string', 'max:255', 'unique:users'],
        'password' => [new DenyChanging('password')],
        'branch_id' => ['integer', Rule::in($branches)],
        'role.*' => ['bail','string', Rule::in($roles), new LowerRole()],
        'permission.*' => ['string', Rule::in($permissions)],
        'moved_at' => ['date'],]);
        if ($validated->fails()) {
          $status[$i] = $validated->errors();
          $i++;
          $error = true;
          continue;
        }
      }
      try {
        $user = User::find($data['id']);
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
              $status[$i] = $user->load('transfers');
              $i++;
            }
            else
            {
              $user->delete();
              $status[$i] = $user->load('transfers');
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
      }catch (\Exception $e) {
        $status[$i] = $e->getMessage();
        $i++;
        goto a;      }
      }
      return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'هذه حالة كل عملية إدخال'));
    }
  }
