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
    $this->middleware(['role_or_permission:SuperAdmin|Owner|Admin|edit-branch'])->only(['show', 'del_edi']);
  }

  public function index()
  {
    return view('addbranches');
  }

  public function show(){
    $company = session('company');
    $branches = Branch::query()->select('*')->where('company_id', $company)->get();
    return response()->json(new Message($branches, '200', isset($error)?false:true, 'info', 'all branches of company', 'كل فروع الشركة'));

  }

  public function store(Request $request)
  {
    try {
      $s=memory_get_usage();
      $i = 0;
      $arr = new ToArray();
      $new_branches =[];
      $datas = json_decode($request->pTableData, true);
      $company = session('company');
      $branches = $arr->to_array(Branch::query()->select('name')->where('company_id', $company)->get(), "name");
      a:foreach(array_slice($datas, $i, count($datas)-$i) as $data){
        if($data['geolocation']??null)
        {
          $data['geolocation'] = explode(",", $data['geolocation']);
          if(!($data['geolocation'][0]??null) or !($data['geolocation'][1]??null)) {
            $error = true;
            throw new \Exception("the geolocation must be in this format 0.00,0.00");
          }
          $data['latitude'] = $data['geolocation'][0];
          $data['longitude'] = $data['geolocation'][1];
        }
        $validated = Validator::make($data,
        [
          'name' => ['required', 'string', 'max:50', Rule::notIn($branches),],
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
        if(array_search($data['name'], $new_branches)!== false) {
          $error = true;
          throw new \Exception("the branchs name is duplicated");
        }
        array_push($new_branches, $data['name']);
        $data_arr =array_merge($data,['company_id' => $company]);
        $branch = Branch::create($data_arr);
        $status[$i] = $branch;
        $i++;
      }  }catch (\Exception $e) {
        $status[$i] = $e->getMessage();
        $i++;
        goto a;
      }
      return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'هذه حالة كل عملية إدخال'));
    }

    public function del_edi(Request $request)
    {
      try {
        $i = 0;
        $arr = new ToArray();
        $datas = json_decode($request->pTableData, true);
        $company = session('company');
        $branches = $arr->to_array(Branch::query()->select('name')->where('company_id', $company)->get(), "name");
        a:  foreach(array_slice($datas, $i, count($datas)-$i) as $data){
          if(count($data) > 1)
          {
            if($data['geolocation']??null)
            {
              $data['geolocation'] = explode(",", $data['geolocation']);
              if(!($data['geolocation'][0]??null) or !($data['geolocation'][1]??null)) {
                $error = true;
                throw new \Exception("the geolocation must be in this format 0.00,0.00");
              }
              $data['latitude'] = $data['geolocation'][0];
              $data['longitude'] = $data['geolocation'][1];
            }
            $validated = Validator::make($data,
            ['id' => ['required','integer'],
            'name' => ['string', 'max:50',Rule::notIn($branches) ],
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
          $branch = Branch::find($data['id']);
          if($branch){
            $response = Gate::inspect('branches', $branch);
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

        }}catch (\Exception $e) {
          $status[$i] = $e->getMessage();
          $i++;
          goto a;
        }
        return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'هذه حالة كل عملية إدخال'));
      }
    }
