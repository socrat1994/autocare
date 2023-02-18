<script>
config = {
  'formElements':['name', 'location', 'geolocation'],
  'dontChang':[],
  'hideOnShow':[],
  'toSuccess':2,
  'addUrl':'{{route('branch.add')}}',
  'hideOnMove':[],
  'addAny':'{{__('Add Branch')}}',
  'showUrl':'{{route("branch.show")}}',
  'updateUrl':'{{route("branch.update")}}',
  'tableHeaders':"<table class='' id='mytable'><thead><tr><th scope='col'>#</th><th scope='col'>{{ __('Name') }}</th><th scope='col'>{{ __('Location') }}</th><th scope='col'>{{ __('Geo Location') }}</th></tr></thead><tbody class='tablbody' id='tabledata'></tbody></table>",
}
</script>
<div class="py-4" id="container">
  <div class="row mb-3" id='dname'>
    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

    <div class="col-md-6">
      <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
      <span id='nameerror'></span>
    </div>
  </div>

  <div class="row mb-3" id='dlocation'>
    <label for="location" class="col-md-4 col-form-label text-md-end">{{ __('location') }}</label>

    <div class="col-md-6">
      <input id="location" type="tel" class="form-control" name="location" value="{{ old('location') }}">
      <span id='locationerror'></span>
    </div>
  </div>

  <div class="row mb-3" id='dgeolocation'>
    <label for="geolocation" class="col-md-4 col-form-label text-md-end">{{ __('Geo location') }}</label>
    <div class="col-md-6">
      <input id="geolocation" type="tel" class="form-control" name="geolocation" value="{{ old('geolocation') }}">
      <span id='geolocationerror'></span>
    </div>
  </div>
