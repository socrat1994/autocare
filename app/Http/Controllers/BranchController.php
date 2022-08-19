<?php

Namespace App\Http\Controllers;

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

class BranchController extends Controller
{
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
  private $rules = array(
      'Name' => ['required', 'string', 'max:50',],
      'Location' => ['required', 'string', 'max:50'],
      'GeoLocation' => ['required', 'string', 'max:50'],
  );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('addbranches');
    }

    public function store(Request $request)
  {
    $i = 0;
    $data = json_decode($request->pTableData, true);
    $company = Cookie::get('company');
    $branches = Branch::query()->select('name')->where('company_id', $company)->get();
    foreach($data as $data){
      $validated = Validator::make($data,
      ['Name' => ['required', 'string', 'max:50', Rule::notIn(to_array($branches, "name")),],
      'Location' => ['required', 'string', 'max:50'],
      'GeoLocation' => ['required', 'string', 'max:50'],]);
       if ($validated->fails()) {
         $status[$i] = $validated->errors();
         $i++;
         $error = true;
         continue;
      }
      try {
      $branch = Branch::create([
      'name' => $data['Name'],
      'location' => $data['Location'],
      'geolocation' => $data['GeoLocation'],
      'company_id' => $company,
      ]);
      $status[$i] = 'done';
      $i++;
      }catch (\Exception $e) {
        return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
    }
  }
  return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
}
}
