var cnt = 0;//for number of rows in the table
var count = 1;//for id of each row in the table
var isShowData = false;//if the showdata button is clicked 
var updateDelet = [];//the array of updated and deleted data from old data that have been shown 
var rowID = 0;//the container that catch row id when edit button is clicked for any row has been shown 
var isEdit = false;//if the button edit is clicked on any row in the table
//Edit button
function BindData(count) {
  $("#name").val($("#name_" + count).html());
  $("#location").val($("#location_" + count).html());
  $("#geolocation").val($("#geolocation_" + count).html());
  $("#add").prop('disabled',false);
  $("#add").attr("value", "Update");
  $("#add").html($("#up").html());
  $("#add").attr("onclick", "EditData(" + count + ")");
  if(isShowData){
    var selector = "#id_"+ count;
    console.log(selector);
    rowID = $(selector).html();
    console.log(rowID);
    isEdit = true;
  }
}
//update button
function EditData(count) {
  if(isShowData){
    $("#add").prop('disabled',true);
    //make apdate jason from update array
    if(update.length != 0){
      obj = {};
      for(i = 0; i < update.length; i =i + 2){
        obj[update[i]] = update[i + 1];
      }
      updateDelet.push(obj);
      console.log(updateDelet);
    }
  }
  $("#name_" + count).html($("#name").val());
  $("#location_" + count).html($("#location").val());
  $("#geolocation_" + count).html($("#geolocation").val());
  $("#add").attr("value", "add");
  $("#add").html($("#ad").html());
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
  //......................validarion..............................
  if(pranch_name == ""){
    $("#nameReq").removeClass("dis-none");
    $("#name").focus();
  }else{
          $("#nameReq").addClass("dis-none");
          if(pranch_location == ""){
            $("#locationReq").removeClass("dis-none");
            $("#location").focus();
          }else{
                  $("#locationReq").addClass("dis-none");
                  if(pranch_geolocation == ""){
                    $("#geolocationReq").removeClass("dis-none");
                    $("#geolocation").focus();
                  }
                  //...............................end validation................................
                  else
                  {
                    $("#geolocationReq").addClass("dis-none");
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
                }
        }
}
//..............................for submit table data:--------------------------------
function subTable(){
  if(isShowData){
    //send update json to server
    updateDelet = $.toJSON(updateDelet);
    $.ajax({
      type: "POST",
      url: $("#delediurl").html(),
      data: "pTableData="+TableData+"&_token="+$("input[name=_token]").attr("value"),
      success: function(msg){
          // return value stored in msg variable
          //maby you want to add the (clear code) here after you sure that data is recived to server
      }
    });
    //for clear the table row after submit(clear code)
    $(".table tbody tr").each(function(row,tr){
      $(tr).remove();
      cnt=0;
    });
    $(".table").hide();
    //clear flages
  }
  else{
  var TableData;
  TableData = $.toJSON(storeTblValues());

  function storeTblValues()
  {
      var TableData = new Array();

      $('.table tbody tr').each(function(row, tr){
          TableData[row]={
              "Number" : $(tr).find('td:eq(0)').text()
              , "name" :$(tr).find('td:eq(1)').text()
              , "location" : $(tr).find('td:eq(2)').text()
              , "geolocation" : $(tr).find('td:eq(3)').text()
          }
      });
      console.log(TableData);
      return TableData;
  }
  console.log(TableData);
 $.ajax({
  type: "POST",
  url: $("#addpranch").attr("action"),
  data: "pTableData="+TableData+"&_token="+$("input[name=_token]").attr("value"),
  success: function(msg){
      // return value stored in msg variable
      //maby you want to add the (clear code) here after you sure that data is recived to server
  }
});
//for clear the table row after submit(clear code)
$(".table tbody tr").each(function(row,tr){
  $(tr).remove();
  cnt=0;
});
$(".table").hide();
}
}
//--------------getBranchData(show branches)------------------
function getBranchData(){
  
  console.log( '' );
if(cnt==0){
  $.ajax({
    url: $("#brshowurl").html(),
    type: "get",
    //type: "post"
    dataType: "json",
    //data: {"action": "loadall", "id": id},
    success: function(data){
        console.log(data);
        count = 0;
        data.forEach(element => {
          $(".tablbody").append('<tr><td id="id_' + count + '">' + element.Number + '</td><td id="name_' + count + '">' + element.Name + '</td><td id="location_' +
          count + '">' + element.location +
          '</td><td id="geolocation_' + count + '">' +element.geoLocation +'</td><td><button type="button" class="delete btn btn-primary">Delete</button></td><td><button type="button" id="edit" onclick="BindData('+ count +');" class="btn btn-primary">Edit</button></td>');
          cnt++;
          count++;
        });
        $(".table").show();
        isShowData = true;
        $("#subTable").html("Update Data");
        $("#add").prop('disabled',true); 
    },
    error: function(error){
         console.log("Error:");
         console.log(error);
    }
});
}
}
//------------------------------------------------------------
var update = [];
function Change(id){
    if(isEdit){
    update.push("id");
    update.push(rowID);
    var name = $("#"+id).attr("id").toString();
    var valueChanged = $("#"+id).val();
    update.push(name);
    update.push(valueChanged);
    console.log(update);
    }
  }

//------------------------------------------------------------
$(document).ready(function() {
  $("#add").prop('disabled',false);
  $("#subTable").html("Submit Data");
  $(".table").hide();
  $(document).on('click', '.delete', function() {
    var par = $(this).parent().parent(); //tr
    if(isShowData){
      var temp = par.find('td:eq(0)').text();
      updateDelet.push({id:temp});
      console.log(updateDelet);
      par.css("background-color","red");
      $(this).prop('disabled',true);
      par.find("#edit").prop('disabled',true);
    }else{
      par.remove();
      cnt--;
      if (cnt == 0) {
        $(".table").hide();
      }
    }
  });
  $(document).on('click','#subTable',subTable)
});
