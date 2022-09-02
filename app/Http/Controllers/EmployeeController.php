<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Validation\Rule;
use App\HelperClasses\Message;
use App\HelperClasses\ToArray;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Rules\LowerRole;

class EmployeeController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|DataEntry|add-employee'])->only(['store', 'index']);
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|DataEntry|edit-employee'])->only(['show', 'del_edi']);
  }

  public function index()
  {
    return view('addemployees');
  }

  public function store(Request $request)
  {
    $i = 0;
    $data = json_decode($request->pTableData, true);
    $company = $request->session()->pull('company');
    $userrole = $request->session()->pull('role');
    $branches = Branch::query()->select('id')->where('company_id', $company)->get();
    $role = Role::query()->select('name')->get();
    
    foreach($data as $data){
      $data['role'] = explode(",", $data['role']);
      $validated = Validator::make($data,
      ['name' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string', 'max:255', 'unique:users'],
      'password' => ['required', 'string', 'min:8', 'confirmed'],
      'branch_id' => ['required', 'integer', Rule::in(to_array($branches, "id"))],
      'role.*' => ['bail','required', 'string', Rule::in(to_array($role, "name")), new LowerRole()],
      'permission.*' => ['required', 'string', Rule::in(to_array($permission, "name"))],
      'moved_at' => ['required', 'date'],]);
      if ($validated->fails()) {
        $status[$i] = $validated->errors();
        $i++;
        $error = true;
        continue;
      }
      try {
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
        foreach($data['role'] as $role)
        {
          $user->assignRole($role);
        }
        $status[$i] = 'done';
        $i++;
      }catch (\Exception $e) {
        return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
      }
    }
    return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
  }

  public function show($id)
  {
    $company = $request->session()->pull('company');
    $branches = Branch::query()->select(['id', 'name', 'location', 'geolocation'])->where('company_id', $company)->get();
    return response()->json(new Message($branches, '200', isset($error)?false:true, 'info', 'all branches of company', 'كل فروع الشركة'));

  }

  /**
  * Show the form for editing the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function edit($id)
  {
    //
  }

  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request, $id)
  {
    //
  }

  /**
  * Remove the specified resource from storage.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function destroy($id)
  {
    //
  }
}
