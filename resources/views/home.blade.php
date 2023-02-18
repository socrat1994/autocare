@extends('layouts.app')

@section('content')
<script>
globalConfig = {
  'cantloadpage':'{{__('you can not do that because there are data in table')}}',
  'backmessage':'{{ __('are you sure you want to reset table') }}',
};
</script>
<div class="py-4" >
  <div id='tabletools'>
    <button type="button" id="subTable1" class="btn btn-primary" onclick='sendtoadd()'>
      <i class="fa fa-spinner fa-spin" id='loading'></i>{{ __('save to server')}}
    </button>
    <button id='showb' type="button" class="btn btn-primary" onclick='showData(config.showUrl, config.updateUrl)'>
      {{ __('show all employees') }}
    </button>
    <button id='back' type="button" class="btn btn-primary" onclick='backto()'>
      <i class="fa fa-spinner fa-spin" id='loadshow'></i>{{ __('back') }}
    </button>
    <button id='openadd' type="button" class="btn btn-primary" onclick='openform()'>
      {{ __('add') }}
    </button>
    <button id='openmove' type="button" class="btn btn-primary" onclick='moving("{{route("employee.show")}}", "{{route("employee.move")}}")'>
      {{ __('moving employee') }}
    </button>
    <!--<button id='localSave' type="button" class="btn btn-primary" onclick='localSave()'>
    {{ __('Save locally') }}
  </button>-->
</div>
<div id='pages'>
  <button id='employeeIndex' type="button" class="btn btn-primary" onclick='showPage("{{route("employee.index")}}")'>
    {{ __('employees') }}
  </button>
  <button id='branchIndex' type="button" class="btn btn-primary" onclick='showPage("{{route("branch.index")}}")'>
    {{ __('branches') }}
  </button>
</div>
</div>
<div class="cd-popup" role="alert" >
  <div class="cd-popup-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <div id='deledit'>
                <button id='del' type="button" class="btn btn-primary">
                  {{ __('delete') }}
                </button>
                <button id='edit' type="button" class="btn btn-primary" >
                  {{ __('edit') }}
                </button>
                <button id='move' type="button" class="btn btn-primary" >
                  {{ __('move') }}
                </button>
              </div>
              <div id='addAny'></div>
            </div>
            <div class="card-body">
              <form id='employee' method="POST" action="">
                @csrf

                <div class="py-4" id="container"></div>

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

                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <a href="#0" id="close" class="cd-popup-close img-replace">Close</a>
  </div>
</div>
<div id="tablecon" style="overflow-y: scroll;">
  <div id="mytable_wrapper"></div>
</div>

<main class="py-4">

  @endsection
