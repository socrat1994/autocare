<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\Auto\Fuel;
use App\Models\Auto\CarModel;
use App\Models\Auto\Version;
use App\Models\Auto\Vehicle;
use App\Models\Auto\Plate;
use App\Models\Auto\Location;
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


class VehicleController extends Controller
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
    try {
      $i = 0;
      $arr = new ToArray();
      $datas = json_decode($request->pTableData, true);
      $company = session('company');
      $models = $arr->to_array(CarModel::query()->select('id')->get(), "id");
      $fuels = $arr->to_array(Fuel::query()->select('id')->get(), "id");
      $branches = $arr->to_array(Branch::query()->select('id')->where('company_id', $company)->get(), "id");
      a:foreach(array_slice($datas, $i, count($datas)-$i) as $data){
        $validated = Validator::make($data,
        ['model_id' => ['required', 'integer', Rule::in($models)],
        'fuel_id' => ['required', 'integer', Rule::in($fuels)],
        'model_year' => ['required', 'integer','max:2100','min:1900'],
        'vin_number' => ['required', 'string'],
        'changed_at' => ['required', 'date', 'after:1900-08-11', 'before:2100-08-11'],
        'plate_number' => ['required', 'string'],
        'car_number' => ['required', 'string'],
        'moved_at' => ['required', 'date', 'after:1900-08-11', 'before:2100-08-11'],
        'branch_id' => ['required', 'integer', Rule::in($branches)],
      ]);
      if ($validated->fails()) {
        $status[$i] = $validated->errors();
        $i++;
        $error = true;
        continue;
      }
      $plates = Plate::query()->select('*')->where([
        ['plate_number', '=', $data['plate_number']],
        ['vin_number', '=', $data['vin_number']],
        ])->get();
        if(!$plates->isempty()) {
          $error = true;
          throw new \Exception("vin number and plate number are exist in database for another vehicle");
        }
        $locations = Location::query()->select('*')->where([
          ['car_number', '=', $data['car_number']],
          ['branch_id', '=', $data['branch_id']],
          ])->get();
          if(!$locations->isempty()) {
            $error = true;
            throw new \Exception("car number can not Duplicated in the same branch");
          }
      $version = Version::query()->select('*')->where([
        ['model_id', '=', $data['model_id']],
        ['fuel_id', '=', $data['fuel_id']],
        ['model_year', '=', $data['model_year']],
        ])->get();
        if($version->isempty()){
          $version = Version::create([
            'model_id' => $data['model_id'],
            'fuel_id' => $data['fuel_id'],
            'model_year' => $data['model_year'],
          ]);
        }
        $vehicle = $version[0]->vehicles()->create();
        $plate = $vehicle->plates()->create([
          'vin_number' => $data['vin_number'],
          'plate_number' => $data['plate_number'],
          'changed_at' => $data['changed_at'],
        ]);
        $location = $plate->locations()->create([
          'car_number' => $data['car_number'],
          'moved_at' => $data['moved_at'],
          'branch_id' => $data['branch_id'],
        ]);
        $status[$i] = [$location, $plate, $version];
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
    foreach($data as $data){

      if(count($data) > 1)
      {
        isset($data['role'])?$data['role'] = explode(",", $data['role']):[null];
        isset($data['permission'])?$data['permission'] = explode(",", $data['permission']):[null];
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
              if(isset($data['role']))
              {
                $user->syncRoles($data['role']);
              }
              if(isset($data['permission']))
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
        return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
      }
    }
    return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'هذه حالة كل عملية إدخال'));
  }
}
