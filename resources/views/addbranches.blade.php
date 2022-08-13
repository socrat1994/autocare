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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>

     var cnt = 0;
var count = 1;

function BindData(count) {
  $("#name").val($("#name_" + count).html());
  $("#location").val($("#location_" + count).html());
  $("#geolocation").val($("#geolocation_" + count).html());
  $("#add").attr("value", "Update");
  $("#add").attr("onclick", "EditData(" + count + ")");
}

function EditData(count) {
  $("#name_" + count).html($("#name").val());
  $("#location_" + count).html($("#location").val());
  $("#geolocation_" + count).html($("#geolocation").val());
  $("#add").attr("value", "add");
  $("#add").attr("onclick", "AddData()");
  //AddData(); /// ADD NEW DATA 
  $("#name").val("");
  $("#location").val("");
  $("#geolocation").val("");
}

function AddData() {
  var pranch_name = $("#name").val();
  var pranch_location = $("#location").val();
  var pranch_geolocation = $("#geolocation").val();
  
  $(".tablbody").append('<tr><td id="id_' + count + '">' + count + '</td><td id="name_' + count + '">' + pranch_name + '</td><td id="location_' +
   count + '">' + pranch_location +
    '</td><td id="geolocation_' + count + '">' + pranch_geolocation +'</td><td><button type="button" class="delete">Delete</button></td><td><button type="button" id="edit" onclick="BindData('+ count +');">Edit</button></td>');
  cnt++;
  count++;
  $("#name").val("");
  //$("#product_names").val("");
  $("#location").val("");
  $("#geolocation").val("");
 if (cnt > 0) {
    $("#dummy").hide();
  }
}
$(document).ready(function() {

  $(document).on('click', '.delete', function() {
    var par = $(this).parent().parent(); //tr
    par.remove();
    cnt--;
    if (cnt == 0) {
      $("#dummy").show();
    }
  });
});
    </script>
@endsection
