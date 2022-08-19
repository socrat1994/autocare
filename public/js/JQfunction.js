var cnt = 0;
var count = 1;

function BindData(count) {
  $("#name").val($("#name_" + count).html());
  $("#location").val($("#location_" + count).html());
  $("#geolocation").val($("#geolocation_" + count).html());
  $("#add").attr("value", "Update");
  $("#add").html("{{ __('update')}}");
  $("#add").attr("onclick", "EditData(" + count + ")");
}

function EditData(count) {
  $("#name_" + count).html($("#name").val());
  $("#location_" + count).html($("#location").val());
  $("#geolocation_" + count).html($("#geolocation").val());
  $("#add").attr("value", "add");
  $("#add").html("{{ __('add')}}");
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
    '</td><td id="geolocation_' + count + '">' + pranch_geolocation +'</td><td><button type="button" class="delete btn btn-primary">Delete</button></td><td><button type="button" id="edit" onclick="BindData('+ count +');" class="btn btn-primary">Edit</button></td>');
  cnt++;
  count++;
  $("#name").val("");
  //$("#product_names").val("");
  $("#location").val("");
  $("#geolocation").val("");
 if (cnt > 0) {
    $(".table").show();
  }
}
//for submit table data:
function subTable(){
  var TableData;
  TableData = $.toJSON(storeTblValues());
  
  function storeTblValues()
  {
      var TableData = new Array();
  
      $('.table tbody tr').each(function(row, tr){
          TableData[row]={
              "Number" : $(tr).find('td:eq(0)').text()
              , "Name" :$(tr).find('td:eq(1)').text()
              , "Location" : $(tr).find('td:eq(2)').text()
              , "GeoLocation" : $(tr).find('td:eq(3)').text()
          }    
      }); 
      //console.log(TableData);
      return TableData;
  }
  console.log(TableData);
 $.ajax({
  type: "POST",
  url: $("#addpranch").attr("action"),
  data: "pTableData="+TableData+"token="+$("input[name=_token]").attr("value"),
  success: function(msg){
      // return value stored in msg variable
  }
});
}


$(document).ready(function() {
  $(".table").hide();
  $(document).on('click', '.delete', function() {
    var par = $(this).parent().parent(); //tr
    par.remove();
    cnt--;
    if (cnt == 0) {
      $(".table").hide();
    }
  });
  $(document).on('click','#subTable',subTable)
});