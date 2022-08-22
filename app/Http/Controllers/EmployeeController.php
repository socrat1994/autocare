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
use App\Http\Controllers\MyFunction;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

    public function index()
    {
      return view('addemployees');
    }

    public function store(Request $request)
    {
      $i = 0;
      $w =stripcslashes($request->pTableData);
      $data = json_decode($request->pTableData, true);
      $company = Cookie::get('company');
      $branches = Branch::query()->select('id')->where('company_id', $company)->get();
      $role = Role::query()->select('name')->get();
      foreach($data as $data){
        $validated = Validator::make($data,
        ['name' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'branch_id' => ['required', 'integer', Rule::in(to_array($branches, "id"))],
        'role' => ['required', 'string', Rule::in(to_array($role, "name"))],
        'moved_at' => ['date'],]);
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
        $user->assignRole($data['role']);
        $status[$i] = 'done';
        $i++;
        }catch (\Exception $e) {
          return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
      }
    }
    return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
}


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
