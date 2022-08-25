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
use app\Policies\AnyChangingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;


class BranchController extends Controller
{
  private $rules = array(
    'Name' => ['required', 'string', 'max:50',],
    'Location' => ['required', 'string', 'max:50'],
    'GeoLocation' => ['required', 'string', 'max:50'],
  );

  public function __construct()
  {
    $this->middleware('auth');
    $this->middleware(['role:SuperAdmin|Owner|Admin|dataentry']);
  }

  public function index()
  {
    return view('addbranches');
  }

  public function show(){
    $company = $request->session()->pull('company');
    $branches = Branch::query()->select(['id', 'name', 'location', 'geolocation'])->where('company_id', $company)->get();
    return response()->json(new Message($branches, '200', isset($error)?false:true, 'info', 'all branches of company', 'كل فروع الشركة'));

  }

  public function store(Request $request)
  {
    $i = 0;
    $data = json_decode($request->pTableData, true);
    $company = $request->session()->pull('company');
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
    return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'هذه حالة كل عملية إدخال'));
  }

  public function del_edi(Request $request)
  {
    $i = 0;
    $data = json_decode($request->pTableData, true);
    $company = $request->session()->pull('company');
    $branches = Branch::query()->select('name')->where('company_id', $company)->get();
    foreach($data as $data){
      if(count($data) > 1)
      {
        $validated = Validator::make($data,
        ['name' => ['string', 'max:50',Rule::notIn(to_array($branches, "name")) ],
        'location' => ['string', 'max:50'],
        'geolocation' => ['string', 'max:50'],]);
        if ($validated->fails()) {
          $status[$i] = $validated->errors();
          $i++;
          $error = true;
          continue;
        }
      }
      try {
        $branch = Branch::find($data['id']);
        if($branch){
          $response = Gate::inspect('anychang', $branch);
          if($response->allowed())
          {
            if(count($data) > 1)
            {
              $branch->update($data);
              $status[$i] = $branch;
              $i++;
            }
            else
            {
              $branch->delete();
              $status[$i] = $branch->id;
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
          $status[$i] = 'wrong id of branch';
          $i++;
        }
      }catch (\Exception $e) {
        return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
      }
    }
    return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'هذه حالة كل عملية إدخال'));
  }
}
