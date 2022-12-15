@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Employee') }}</div>

                <div class="card-body">
                    <form id='employee' method="POST" action="">
                        @csrf
                        <p class="d-none" id="data">name,phone,branch_id,moved_at,role,permission,password,password_confirmation</p>
                        <p class="d-none" id="stay">2,4,5,6,7</p>
                        <p class="d-none" id="tohide">6,7</p>
                        <p class="d-none" id="todone">6</p>
                        <p class="d-none" id="addurl">{{ route('employee.add') }}</p>
                        <p class="d-none" id="text">{{ __('sure') }}</p>

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                <span id='nameerror'></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('phone number') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required autocomplete="phone">
                                <span id='phoneerror'></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="branch_id" class="col-md-4 col-form-label text-md-end">{{ __('branch') }}</label>

                            <div class="col-md-6">
                                <select id="branch_id"  class="form-control" name="branch_id" value="{{ old('branch_id') }}" required autocomplete="branch_id">
                                  @foreach (DB::table('branches')->where('company_id', Request::session()->pull('company'))->get() as $branch_id)
                                      <option value="{{ $branch_id->id }}">{{ $branch_id->name }}</option>
                                   @endforeach
                                </select>
                                <span id='branch_iderror'></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-md-4 col-form-label text-md-end">{{ __('Roles') }}</label>

                            <div class="col-md-6">
                                <select id="role"  class="form-control" name="role" value="{{ old('role') }}" required multiple>
                                  @foreach (DB::table('roles')->get() as $role)
                                   @if($role->id < Auth::user()->roles->first()->id)
                                   @else
                                      <option value="{{$role->name}}">{{ $role->name }}</option>
                                    @endelse
                                    @endif
                                   @endforeach
                                </select>
                                <span id='roleerror'></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="permission" class="col-md-4 col-form-label text-md-end">{{ __('Permissions') }}</label>

                            <div class="col-md-6">
                                <select id="permission"  class="form-control" name="permission" value="{{ old('permission') }}" required autocomplete="permission" multiple>
                                  @foreach (Auth::user()->roles->first()->permissions->pluck('name') as $permission)
                                      <option value="{{ $permission }}">{{ $permission }}</option>
                                   @endforeach
                                </select>
                                <span id='permissionerror'></span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="moved_at" class="col-md-4 col-form-label text-md-end">{{ __('Moved at') }}</label>

                            <div class="col-md-6">
                                <input id="moved_at" type="date" class="form-control @error('moved_at') is-invalid @enderror" name="moved_at" required >
                                <span id='moved_aterror'></span>
                            </div>
                        </div>

                        <div class="row mb-3" id='dpassword'>
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" minlength="8" name="password" required >
                                <span id='passworderror'></span>
                            </div>
                        </div>

                        <div class="row mb-3" id="dpassword_confirmation">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password_confirmation" type="password" class="form-control" minlength="8" name="password_confirmation" required >
                                <span id='password_confirmationerror'></span>
                            </div>
                        </div>


                            <div id="dexperror" class=" alert alert-danger col-md-12">
                                <p id="experror"  style="color:red;text-align:center;">huihuih</p>
                            </div>


                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button id='addbutton' type="button" class="btn btn-primary" onclick='formdata()'>
                                    {{ __('add') }}
                                </button>
                                <button id='updatebutton' type="button" class="btn btn-primary" onclick='update()'>
                                    {{ __('update') }}
                                </button>
                                <button id='showb' type="button" class="btn btn-primary" onclick='show("{{route("employee.show")}}", "{{route("employee.update")}}")'>
                                    {{ __('show all employees') }}
                                </button>
                                <button id='back' type="button" class="btn btn-primary" onclick='backto()'>
                                    <i class="fa fa-spinner fa-spin" id='loadshow'></i>{{ __('back') }}
                                </button>
                            </div>
                        </div>
                    </form>

                  <button type="button" id="subTable1" class="btn btn-primary" onclick='sendtoadd()'>
                        <i class="fa fa-spinner fa-spin" id='loading'></i>{{ __('submit')}}
                  </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="tablecon" >
<table class="" id='mytable'><!--table table-striped table-dark table-hover table-responsive style="overflow-y:scroll;height:300px;"-->
    <thead>
        <tr>
        <th scope="col">#</th>
        <th scope="col">{{ __('Name') }}</th>
        <th scope="col">{{ __('PHONE') }}</th>
        <th scope="col">{{ __('BRANCH') }}</th>
        <th scope="col">{{ __('MOVED AT') }}</th>
        <th scope="col">{{ __('ROLES') }}</th>
        <th scope="col">{{ __('PERMISSIONS') }}</th>
        <th scope="col">{{ __('PASSWORD') }}</th>
        <th scope="col">{{ __('CONFIRMTION') }}</th>
        </tr>
    </thead>
    <tbody class="tablbody" id='tabledata'>

    </tbody>
</table>
</div>
<main class="py-4">

@endsection
