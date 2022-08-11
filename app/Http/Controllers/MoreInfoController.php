<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;

class MoreInfoController extends Controller
{
  private $rules = array(
      'name' => ['required', 'string', 'max:50'],
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
      $validated = Validator::make($request->all(), $this->rules);
      $user = Auth::user();
      $company = $user->company()->get('id');
      $branch = Branch::create([
      'name' => $request->name,
      'location' => $request->location,
      'geolocation' => $request->geolocation,
      'company_id' => $company[0]->id,
    ]);
      return response()->json($branch);
      /*if ($validated->fails()) {
          return response()->json(new Message($validated->errors(), '200', false, 'error', 'validation error', 'تحقق من المعلومات المدخلة'));
      }
      try {
          $branch = Branch::create($request->all());
          return response()->json(new Message($contact->load('region', 'country'), '200', true, 'info', "inserted successfully", 'تم ادخال البيانات بنجاح'));
      } catch (\Exception $e) {
          return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
      }*/
  }
}
