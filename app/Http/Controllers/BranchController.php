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
use App\HelperClasses\ToArray;
use app\Policies\AnyChangingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;


class BranchController extends Controller
{
  public function __construct()
  {
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|DataEntry|add-branch'])->only(['store', 'index']);
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|DataEntry|edit-branch'])->only(['show', 'del_edi']);
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
    $datas = json_decode($request->pTableData, true);
    $company = $request->session()->pull('company');
    $branches = Branch::query()->select('name')->where('company_id', $company)->get();
    foreach($datas as $data){
      $data['geolocation'] = explode(",", $data['geolocation']);
      $data['latitude'] = $data['geolocation'][0];
      $data['longitude'] = $data['geolocation'][1];
      $validated = Validator::make($data,
      ['name' => ['required', 'string', 'max:50', Rule::notIn(to_array($branches, "name")),],
      'location' => ['required', 'string', 'max:50'],
      'latitude' => [ 'numeric', 'between:-90,90'],
      'longitude' => [ 'numeric', 'between:-180,180'],
    ]);
      if ($validated->fails()) {
        $status[$i] = $validated->errors();
        $i++;
        $error = true;
        continue;
      }
      try {
        $data_arr =array_merge($data,['company_id' => $company]);
        $branch = Branch::create($data_arr);
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
      $data['geolocation'] = explode(",", $data['geolocation']);
      $data['latitude'] = $data['geolocation'][0];
      $data['longitude'] = $data['geolocation'][1];
      if(count($data) > 1)
      {
        $validated = Validator::make($data,
        ['name' => ['string', 'max:50',Rule::notIn(to_array($branches, "name")) ],
        'location' => ['string', 'max:50'],
        'latitude' => [ 'numeric', 'between:-90,90'],
        'longitude' => [ 'numeric', 'between:-180,180']]);
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
