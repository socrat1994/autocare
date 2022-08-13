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

class MoreInfoController extends Controller
{
  private $arr = ['e'];//DB::table('branches')->get()->name;
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
      $user = Auth::user();
      $company = $user->company()->get('id');
      $branches = Branch::query()->select('name')->where('company_id', $company[0]->id)->get();
      $validated = Validator::make($request->all(),
      ['name' => ['required', 'string', 'max:50', Rule::notIn(to_array($branches, "name")),],
      'location' => ['required', 'string', 'max:50'],
      'geolocation' => ['required', 'string', 'max:50'],]);
      if ($validated->fails()) {
        return back()->withErrors($validated->errors());
      }
      try {

      $branch = Branch::create([
      'name' => $request->name,
      'location' => $request->location,
      'geolocation' => $request->geolocation,
      'company_id' => $company[0]->id,
    ]);
      return to_array($branches, "name");//back()->with('message', 'success');
    }catch (\Exception $e) {
        return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
    }
  }
}
