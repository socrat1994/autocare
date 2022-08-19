<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Validation\Rule;
use App\HelperClasses\Message;
use App\Http\Controllers\MyFunction;
use Illuminate\Support\Facades\Cookie;

class EmployeeController extends Controller
{
  private $rules = array(
    'name' => ['required', 'string', 'max:255'],
    'phone' => ['required', 'string', 'max:255', 'unique:users'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
  );

  private  $jsona = '[
   {
      "name":"syria",
      "phone":"1235",
      "password":"12345678",
      "branch_id":"53",
      "moved_at":"2022-08-11",
   },
   {
      "name":"syria1",
      "phone":"125",
      "password":"12345678",
      "branch_id":"53",
      "moved_at":"2022-08-11",
   }
]';

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
      $data = json_decode($this->jsona, true);
      $company = Cookie::get('company');
      $branches = Branch::query()->select('id')->where('company_id', $company)->get();
      /*foreach($data as $data){
        $validated = Validator::make($data,
        ['name' => ['required', 'string', 'max:255'],
        'phone' => ['required', 'string', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'branch_id' => ['required', 'integer', Rule::in(to_array($branches, "id"))],
        'moved_at' => ['date'],]);
         if ($validated->fails()) {
           $status[$i] = $validated->errors();
           $i++;
           $error = true;
           continue;
        }
        try {
        $user = User::create([
        'name' => $data['Name'],
        'phone' => $data['phone'],
        'password' => Hash::make($data['password']),
        ]);
        $employee = Employee::create([
        'user_id' => $user->id,
        'branch_id' => $data['branch_id'],
        'moved_at' => $data['moved_at']
        ]);
        $status[$i] = 'done';
        $i++;
        }catch (\Exception $e) {
          return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
      }
    }*/
    return response()->json(new Message($data, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
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
