@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add Branch') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('addbranches') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="location" class="col-md-4 col-form-label text-md-end">{{ __('location') }}</label>

                            <div class="col-md-6">
                                <input id="location" type="tel" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location') }}" required autocomplete="location">

                                @error('location')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="geolocation" class="col-md-4 col-form-label text-md-end">{{ __('gio location') }}</label>

                            <div class="col-md-6">
                                <input id="geolocation" type="text" class="form-control @error('geolocation') is-invalid @enderror" name="geolocation" value="{{ old('geolocation') }}" required autocomplete="geolocation">

                                @error('geolocation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="reset" class="btn btn-primary">
                                    {{ __('Reset') }}
                                </button>
                                <button type="button" id="add" class="btn btn-primary" onclick="AddData()">
                                    {{ __('add')}}
                                </button>
                            </div>
                        </div>
                    </form>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Location</th>
                            <th scope="col">Gio Location</th>
                            <th scope="col" colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="tablbody">
                            
                        </tbody>
                    </table>
                    <div  style="text-align: center;">
                        <button type="submit" class="btn btn-primary">
                                {{ __('submit')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
