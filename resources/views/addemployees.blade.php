@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Employee') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ url('/employee') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('phone number') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone">

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="branch_id" class="col-md-4 col-form-label text-md-end">{{ __('branch') }}</label>

                            <div class="col-md-6">
                                <select id="branch_id"  class="form-control @error('branch_id') is-invalid @enderror" name="branch_id" value="{{ old('branch_id') }}" required autocomplete="branch_id">
                                  @foreach (DB::table('branches')->where('company_id', Request::session()->pull('company'))->get() as $branch_id)
                                      <option value="{{ $branch_id->id }}">{{ $branch_id->name }}</option>
                                   @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-md-4 col-form-label text-md-end">{{ __('Roles') }}</label>

                            <div class="col-md-6">
                                <select id="role"  class="form-control @error('role') is-invalid @enderror" name="role" value="{{ old('role') }}" required autocomplete="role" multiple>
                                  @foreach (DB::table('roles')->get() as $role)
                                   @if($role->id < Auth::user()->roles->first()->id)
                                   @else
                                      <option value="{{ $role->name}}">{{ $role->name }}</option>
                                    @endelse
                                    @endif
                                   @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="permission" class="col-md-4 col-form-label text-md-end">{{ __('Permissions') }}</label>

                            <div class="col-md-6">
                                <select id="permission"  class="form-control @error('permission') is-invalid @enderror" name="permission" value="{{ old('permission') }}" required autocomplete="permission" multiple>
                                  @foreach (Auth::user()->roles->first()->permissions->pluck('name') as $permission)
                                      <option value="{{ $permission }}">{{ $permission }}</option>
                                   @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
