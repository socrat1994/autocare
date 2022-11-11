var datatable =[];
var getdatafrompage ='';
count = 0;
window.onload = function(){getdatafrompage = $('#data').html();}
function BindData2()
{
  $(".table").show();
  getdataarray = getdatafrompage.split(",");
  var row=[];
  getdataarray.forEach(function(item) {
  row.push($('#'+item).val());
 });
  datatable.push(row);
  addtotable(row);
  console.log(datatable);
}

function addtotable(row)
{

  $(".tablbody").append('<tr id="tid_' + count + '"><td id="id_' + count + '">' +
   count + '</td><td id="name_' + count + '">' + pranch_name + '</td><td id="location_' +
  count + '">' + pranch_location +
  '</td><td id="geolocation_' + count + '">' +
   pranch_geolocation +
   '</td><td><button type="button" class="delete btn btn-primary">Delete</button></td><td><button type="button" id="edit" onclick="BindData('+ count +');" class="btn btn-primary">Edit</button></td>');
  count++;
}
