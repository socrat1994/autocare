<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
      $s =microtime(true);
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
            }else{$version = $version[0];}
            $vehicle = $version->vehicles()->create();
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
        $e = microtime(true)-$s;
        return response()->json(new Message($status, '200', isset($error)?false:true, $e, "here status of every insertion", 'Arabictext'));
  }

  public function move(Request $request)
  {
        try {
          $i = 0;
          $arr = new ToArray();
          $datas = json_decode($request->pTableData, true);
          $in_data = $arr->to_array(json_decode($request->pTableData), 'plate_id');
          $company = session('company');
          $locations = DB::raw('(select * from locations where id in (select max(id) from locations group by plate_id)) as locations');
          $plates = DB::raw('(select * from plates where id in (select max(id) from plates group by vehicle_id)) as plates');
          $ids = DB::table($plates)
          ->join($locations, 'plates.id', '=', 'locations.plate_id')
          ->join('branches', 'branches.id', '=', 'locations.branch_id')
          ->select(['plates.id as plates',
          'branches.id as branches',
          'branches.company_id',
          'locations.car_number',
          ])->where('company_id',$company)->whereIn('plates.id', $in_data)->get();
          $plates = $arr->to_array($ids, 'plates');
          $branches = $arr->to_array($ids, 'branches');
          a:foreach(array_slice($datas, $i, count($datas)-$i) as $data){
            $used_number = $ids->where('branches', $data['branch_id'])
            ->where('car_number', $data['car_number'])->last()->car_number;
            $not_in_branch = $ids->where('plates', $data['plate_id'])->last();
            return $not_in_branch;
            $validated = Validator::make($data,
            ['plate_id' => ['integer', Rule::in($plates),],
            'car_number' => ['required', 'string', Rule::notIn($used_number)],
            'moved_at' => ['required', 'date', 'after:1900-08-11', 'before:2100-08-11'],
            'branch_id' => ['required', 'integer', Rule::in($branches), Rule::notIn($not_in_branch)],
          ]);
          if ($validated->fails()) {
            $status[$i] = $validated->errors();
            $i++;
            $error = true;
            continue;
          }
            $location = Location::create([
              'plate_id' => $data['plate_id'],
              'car_number' => $data['car_number'],
              'moved_at' => $data['moved_at'],
              'branch_id' => $data['branch_id'],
            ]);
            $status[$i] = $location;
            $i++;
          }
        }catch (\Exception $e) {
          $status[$i] = $e->getMessage();
          $i++;
          goto a;
        }
        return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
      }

  public function change_num(Request $request)
  {
        try {
          $i = 0;
          $arr = new ToArray();
          $datas = json_decode($request->pTableData, true);
          $in_data = $arr->to_array(json_decode($request->pTableData), 'vehicle_id');
          $company = session('company');
          $locations = DB::raw('(select * from locations where id in (select max(id) from locations group by plate_id)) as locations');
          $plates = DB::raw('(select * from plates where id in (select max(id) from plates group by vehicle_id)) as plates');
          $ids = DB::table('vehicles')
          ->join($plates, 'vehicles.id', '=', 'plates.vehicle_id')
          ->join($locations, 'plates.id', '=', 'locations.plate_id')
          ->join('branches', 'branches.id', '=', 'locations.branch_id')
          ->select(['vehicles.id as vehicles',
          'plates.id as plates',
          'locations.car_number',
          'locations.moved_at',
          'branches.id as branches',
          'branches.company_id',
          ])->where('company_id',$company)->wherein('vehicles.id', $in_data)->get();
          $vehicles = $arr->to_array($ids, 'vehicles');
          $branches = $arr->to_array($ids, 'branches');
          a:foreach(array_slice($datas, $i, count($datas)-$i) as $data){
            $used_number = $ids->where('branches', $data['branch_id'])
            ->where('car_number', $data['car_number'])->last()->car_number;
            $not_in_branch = $ids->where('plates', $data['plate_id'])->last();
            return $not_in_branch;
            $validated = Validator::make($data,
            ['vehicle_id' => ['required', 'integer', Rule::in($plates),],
            'plate_number' => ['required', 'string', Rule::notIn($used_number)],
            'vin_number' => ['required', 'string', Rule::notIn($used_number)],
            'changed_at' => ['required', 'date', 'after:1900-08-11', 'before:2100-08-11'],
            'branch_id' => ['required', 'integer', Rule::in($branches), Rule::notIn($not_in_branch)],
          ]);
          if ($validated->fails()) {
            $status[$i] = $validated->errors();
            $i++;
            $error = true;
            continue;
          }
            $plate = Plate::create([
              'plate_id' => $data['plate_id'],
              'car_number' => $data['car_number'],
              'moved_at' => $data['moved_at'],
              'branch_id' => $data['branch_id'],
            ]);
            $current_location = $ids->where('vehicles', $data['vehicle_id'])->last();
            $location = $plate->create([
              'car_number' => $ids->car_number,
              'moved_at' => $ids->moved_at,
              'branch_id' => $ids->branches,
            ]);
            $status[$i] = $location;
            $i++;
          }
        }catch (\Exception $e) {
          $status[$i] = $e->getMessage();
          $i++;
          goto a;
        }
        return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion", 'Arabictext'));
      }

  public function show($options)
  {
        $options = explode(',', $options);
        $Allplates = $options[0];
        $Alllocations = $options[1];
        $branch = $options[2];
        if(!is_numeric($branch))
        {
          $status = "branch id must be numeric value";
        }
        else
        {
          if($Allplates)
          {
            $plates = "plates";
          }
          else
          {
            $Allplates == 2?$m = 'min(id)':$m = 'max(id)';
            $plates = DB::raw('(select * from plates where id in (select '. $m .' from plates group by vehicle_id)) as plates');
          }
          if($Alllocations)
          {
            $locations = "locations";
          }
          else
          {
            $locations = DB::raw('(select * from locations where id in (select max(id) from locations group by plate_id)) as locations');
          }
          $plates = DB::raw('(select * from plates where id in (select max(id) from plates group by vehicle_id)) as plates');
          $plates = "plates";
          if($branch == 0)
          {
            $branches = DB::raw('(select * from branches where company_id ='.session("company").') as branches');
          }
          else
          {
            $branches = DB::raw('(select * from branches where id ='.$branch.' and company_id='.session("company").') as branches');
          }
          $status = DB::table('manufactuerers')
          ->join('models', 'manufactuerers.id', '=', 'models.manufactuerer_id')
          ->join('versions', 'models.id', '=', 'versions.model_id')
          ->join('fuels', 'fuels.id', '=', 'versions.fuel_id')
          ->join('vehicles', 'versions.id', '=', 'vehicles.version_id')
          ->join($plates,'vehicles.id', '=', 'plates.vehicle_id')
          ->join($locations, 'plates.id', '=', 'locations.plate_id')
          ->join($branches, 'branches.id', '=', 'locations.branch_id')
          ->select(['manufactuerers.name as manufactuerer',
          'models.name as model',
          'versions.model_year as year',
          'fuels.type as fuel',
          'plates.plate_number',
          'plates.vin_number',
          'plates.changed_at',
          'locations.car_number',
          'branches.name as branch',
          'locations.moved_at',
          'vehicles.id as vehicle_id',
          'vehicles.version_id',
          'locations.plate_id',
          'locations.id as location_id'
          ])->get();}
          return response()->json(new Message($status, '200', true, 'info', 'all vehicles of company', 'كل سيارات الفرع'));
      }

  public function del_edi(Request $request)
  {
          try {
            $i = 0;
            $arr = new ToArray();
            $datas = json_decode($request->pTableData, true);
            $company = session('company');
            $models = $arr->to_array(CarModel::query()->select('id')->get(), "id");
            $fuels = $arr->to_array(Fuel::query()->select('id')->get(), "id");
            $ids = DB::table('vehicles')
            ->join('plates','vehicles.id', '=', 'plates.vehicle_id')
            ->join('locations', 'plates.id', '=', 'locations.plate_id')
            ->join('branches', 'branches.id', '=', 'locations.branch_id')
            ->select(['plates.id as plates',
            'vehicles.id as vehicles',
            'locations.id as locations',
            'branches.id as branches',
            'branches.company_id'
            ])->where('company_id',$company)->get();
            $vehicles = $arr->to_array($ids, 'vehicles');
            $plates = $arr->to_array($ids, 'plates');
            $locations = $arr->to_array($ids, 'locations');
            $branches = $arr->to_array($ids, 'branches');
            $permissions = session('permission');
            a:foreach(array_slice($datas, $i, count($datas)-$i) as $data){
              $validated = Validator::make($data,
              ['model_id' => ['integer', Rule::in($models)],
              'fuel_id' => ['integer', Rule::in($fuels)],
              'model_year' => ['integer','max:2100','min:1900'],
              'vin_number' => ['string'],//should not two vehicles in company had same vin number
              'changed_at' => ['date', 'after:1900-08-11', 'before:2100-08-11'],
              'plate_number' => ['string'],//should not two vehicles in company had same plate number
              'car_number' => ['string'],// should not duplicated in one branch
              'moved_at' => ['date', 'after:1900-08-11', 'before:2100-08-11'],
              'branch_id' => ['integer', Rule::in($branches)],
              'version_id' => ['integer',],//in versions
              'vehicle_id' => ['integer', Rule::in($vehicles)],
              'plate_id' => ['integer', Rule::in($plates)],
              'location_id' => ['integer', Rule::in($locations)],
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
                if(count($data) > 1)
                {
                  $a = $data['model_id']??null;
                  $b = $data['fuel_id']??null;
                  $c = $data['model_year']??null;
                  if ((!$a and $c) or ($b and !$c) or ($a and !$b)){
                    throw new \Exception('model and fuel and year are all required.');
                  }
                  if ($a and $b and $c)
                  {
                    $version = Version::query()->select('*')->where([
                      ['model_id', '=', $data['model_id']],
                      ['fuel_id', '=', $data['fuel_id']],
                      ['model_year', '=', $data['model_year']],
                      ])->get();
                      if($version->isempty()){
                        $version = Version::find($data['version_id']);
                        $version->update([
                          'model_id' => $data['model_id'],
                          'fuel_id' => $data['fuel_id'],
                          'model_year' => $data['model_year'],
                        ]);
                      }
                      $vehicle =  Vehicle::find($data['vehicle_id']);
                      $vehicle->update(['version_id' => $version[0]->id]);
                      $plate =  Plate::find($data['plate_id']);
                      $plate->update($data);
                      $location =  Location::find($data['location_id']);
                      $location->update($data);
                    }
                    $status[$i] = [$location, $plate, $version[0]];
                    $i++;
                  }
                  else
                  {
                    $data['vehicle_id']??null?$todelete = Vehicle::find($data['vehicle_id']):"";
                    $data['plate_id']??null?$todelete = Plate::find($data['plate_id']):"";
                    $data['location_id']??null?$todelete = Location::find($data['location_id']):"";
                    $todelete->delete();
                    $status[$i] = $todelete->id;
                    $i++;
                  }
                }
              }catch (\Exception $e) {
                $status[$i] = $e->getMessage();
                $i++;
                goto a;
              }
              return response()->json(new Message($status, '200', isset($error)?false:true, 'info', "here status of every insertion of vehicles", 'هذه حالة كل عملية إدخال'));
            }
          }
