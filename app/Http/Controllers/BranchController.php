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

class BranchController extends Controller
{
  private  $jsona = '[
   {
      "name":"syria",
      "location":"syria",
      "geolocation":"syria"
   },
   {
      "name":"syria1",
      "location":"syria1",
      "geolocation":"syria1"
   }
]';
  private $rules = array(
      'name' => ['required', 'string', 'max:50',],
      'location' => ['required', 'string', 'max:50'],
      'geolocation' => ['required', 'string', 'max:50'],
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
    $a = json_decode($request->string, true);
    $user = Auth::user();//we should get the owner of the company
    $company = $user->company()->get('id');
    $branches = Branch::query()->select('name')->where('company_id', $company[0]->id)->get();
    foreach($a as $a){
      $validated = Validator::make($a,
      ['name' => ['required', 'string', 'max:50', Rule::notIn(to_array($branches, "name")),],
      'location' => ['required', 'string', 'max:50'],
      'geolocation' => ['required', 'string', 'max:50'],]);
       if ($validated->fails()) {
         $status[$i] = $validated->errors();
         $i++;
         $error = true;
         continue;
      }
      try {
      $branch = Branch::create([
      'name' => $a['name'],
      'location' => $a['location'],
      'geolocation' => $a['geolocation'],
      'company_id' => $company[0]->id,
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
