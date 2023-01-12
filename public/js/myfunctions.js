function enable(ids)
{
  ids.forEach(function(id){
    $(id).prop('disabled', false);
  })
}

function disable(ids)
{
  ids.forEach(function(id){
    $(id).prop('disabled', true);
  })
}

function show(ids)
{
  ids.forEach(function(id){
    $(id).show();
  })
}

function hide(ids)
{
  ids.forEach(function(id){
    $(id).hide();
  })
}
