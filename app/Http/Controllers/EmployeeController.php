<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
  private $rules = array(
    'name' => ['required', 'string', 'max:255'],
    'phone' => ['required', 'string', 'max:255', 'unique:users'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
  );

  public function __construct()
  {
      $this->middleware('auth');
  }

    public function index()
    {
      return view('addemployees');
    }

    public function create(Request $request)
    {
      $validated = Validator::make($request->all(), $this->rules);
    if ($validated->fails()) {
        return response()->json(new Message($validated->errors(), '200', false, 'error', 'validation error', 'تحقق من المعلومات المدخلة'));
    }
    try {
        $region_data = array('name' => $request->name);
        $region = Region::create($region_data);
        $region->country()->create(array_diff($request->all(), $region_data));
        return response()->json(new Message($region->country->load('region'), '200', true, 'info', "the country above inserted successfully", 'تم إدخال البيانات بنجاح'));
    } catch (\Exception $e) {
        return response()->json(new Message($e->getMessage(), '100', false, 'error', 'error', 'خطأ'));
    }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
