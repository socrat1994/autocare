var cnt = 0;//for number of rows in the table
var count = 1;//for id of each row in the table
var isShowData = false;//if the showdata button is clicked 
var updateDelet = [];//the array of updated and deleted data from old data that have been shown 
var rowID = 0;//the container that catch row id when edit button is clicked for any row has been shown 
var isEdit = false;//if the button edit is clicked on any row in the table
var update = [];//for stor updated data befor turn it to jason.
/*
for comparing modified data by update button with row data in table
*/
var _id = 0;
var _name = "";
var _location = "";
var _geolocation = "";
/*
for comparing modified data by update button with row data in table
*/

//Edit button
function BindData(count) {
  //code for editing data 
  //git the data from table row and bind it in related textboxes
  $("#name").val($("#name_" + count).html());
  $("#location").val($("#location_" + count).html());
  $("#geolocation").val($("#geolocation_" + count).html());
  $("#add").prop('disabled',false);
  $("#add").attr("value", "Update");
  $("#add").html($("#up").html());
  $("#add").attr("onclick", "EditData(" + count + ")");

  if(isShowData){
    //here the code when show data button was clicked then edit row button clicked next
    
    _name = $("#name").val();
    _location = $("#location").val();
    _geolocation = $("#geolocation").val();
    //old row data was catched 

    var selector = "#id_"+ count;
    console.log(selector);
    _id = $(selector).html();// row id was catched too.
    console.log(_id);
    //isEdit = true;
  }
}

//update button
function EditData(count) {
  // here is the code when update butten is clicked

  if($("#name").val() == ""){
    //code for manibulate the case name's textbox is empty
    $("#nameReq").removeClass("dis-none");//show the message this field is required
    $("#name").focus();//git focus for the empty textbox.
  }

  else if($("#location").val() == ""){
    //code for manibulate the case location's textbox is empty
    $("#nameReq").addClass("dis-none");
    $("#locationReq").removeClass("dis-none");
    $("#location").focus();
  }
  else{
    $("#nameReq").addClass("dis-none");// hide the message this field is required
    $("#geolocationReq").addClass("dis-none");// hide the message this field is required
    $("#locationReq").addClass("dis-none");// hide the message this field is required

    if(isShowData){
          //handling the case if show data button was clicked

          $("#add").prop('disabled',true);//for disable add button becuase in this case is not useful.

          //creat updat array for sending it to server (it contains the data was updated befor click updat data button)

        if(_name !== $("#name").val() || _location !== $("#location").val() || _geolocation !== $("#geolocation").val()){
          //handling the case if any of this data was updated the row id must be pushed to the apdate array
          update.push("id");
          update.push(_id);
        }
        if(_name !== $("#name").val()){
          //handling the case if name was updated
          update.push("name");
          update.push($("#name").val());
        }
        if(_location !== $("#location").val()){
          //handling the case if location was updated
          update.push("location");
          update.push($("#location").val());
        }
        if(_geolocation !== $("#geolocation").val()){
          //handling the case if geolocation was updated
          update.push("geolocation");
          update.push($("#geolocation").val());
        }
        console.log(update);

        if(update.length != 0){
          //handling the case if update array contain updated data

          obj = {};//clear the object befor but the data into it.

          for(i = 0; i < update.length; i =i + 2){
            //converting array to object
            obj[update[i]] = update[i + 1];
          }
          console.log(obj);
          updateDelet.push(obj);// push the object to the new array for send it to server
          console.log(updateDelet);
          update = [];//clear the array from breveose data
        }
    }
    $("#name_" + count).html($("#name").val());//but the data in the selected row from txtbox
    $("#location_" + count).html($("#location").val());//but the data in the selected row from txtbox
    $("#geolocation_" + count).html($("#geolocation").val());//but the data in the selected row from txtbox
    $("#add").attr("value", "add");//??
    $("#add").html($("#ad").html());//set the text of the add button to "add"
    $("#add").attr("onclick", "AddData()");//make the add button do another function "add data" in next click on him
    //AddData(); /// ADD NEW DATA
    $("#name").val("");//clear text box
    $("#location").val("");//clear text box
    $("#geolocation").val("");//clear text box
  }
}
//code for Reset button onclick
function reset(){
  // make the warning messages disappered
  $("#nameReq").addClass("dis-none");//disapper name requiste message.
  $("#locationReq").addClass("dis-none");//disappear location request message.
}

//code for add data function on add button click
function AddData() {

  $("#subTable").html("Submit Data");//change the text of button to "Submit Data"
  var pranch_name = $("#name").val();//take the value of name txtbox in avariable
  var pranch_location = $("#location").val();//take the value of location txtbox in avariable
  var pranch_geolocation = $("#geolocation").val();//take the value of geolocation txtbox in avariable
  //......................validarion..............................
  //check for valid input:
  if(pranch_name == ""){
    //code if the name txtbox is  empty
    $("#nameReq").removeClass("dis-none");//show the alert message
    $("#name").focus();//git focuse on the empty txtbox
  }else{
          if(pranch_location == ""){
            //code if the location txtbox is  empty
            $("#nameReq").addClass("dis-none");//remove the alert message under the name txtbox
            $("#locationReq").removeClass("dis-none");//show the alert message under location txtbox
            $("#location").focus();//git focuse on the empty txtbox
          }else{
                    //$("#geolocationReq").addClass("dis-none");
                    $("#locationReq").addClass("dis-none");//remove the alert message.
                    $("#nameReq").addClass("dis-none");//remove the alert message.

                    //append the data in table's row :
                    $(".tablbody").append('<tr><td id="id_' + count + '">' + count + '</td><td id="name_' + count + '">' + pranch_name + '</td><td id="location_' +
                    count + '">' + pranch_location +
                    '</td><td id="geolocation_' + count + '">' + pranch_geolocation +'</td><td><button type="button" class="delete btn btn-primary">Delete</button></td><td><button type="button" id="edit" onclick="BindData('+ count +');" class="btn btn-primary">Edit</button></td>');
                    cnt++;//increase the number of rows in the table by one
                    count++;//increase the id number of the next row added
                    $("#name").val("");//clear txtbox data
                    //$("#product_names").val("");
                    $("#location").val("");//clear txtbox data
                    $("#geolocation").val("");//clear txtbox data
                    //check if the number of rows in the table is more than 0
                    if (cnt > 0) {
                      $(".table").show();//show the table with its data
                    }
                  
                }
        }
}
//..............................for submit table data:--------------------------------

//code to submit data that has been intered to the table:
function subTable(){
  //check if the data in the table is from server as (show branch data)
  // ?? the exist of data is not important ??
  if(isShowData){
    //code if show data button was clicked
      if(updateDelet.length !== 0){
        //code for checking if updateDelet objects array is not empty and the updating data is there
          //send update json to server
          _updateDelet = $.toJSON(updateDelet);//converting objects array to json format
          $.ajax({//ajax function for send the json to the server
            type: "POST",
            url: $("#delEdiUrl").html(),//the url is in an html element content.
            data: "pTableData="+ _updateDelet +"&_token="+$("input[name=_token]").attr("value"),
            success: function(msg){
              //in case the connection between client and server is successful
              // ?? here we have to check out the response message "msg" to know if the 
              //data was written successfuly in data base ?? 
              if(msg.done){
                updateDelet = [];//make the array that contain old data empty.
                //clear the table rows
                $(".table tbody tr").each(function(row,tr){
                $(tr).remove();
                cnt=0;
                });

                $(".table").hide();//hide the table header.
                $("#add").prop('disabled',false);//enable add button.
                alert('data has been saved successfuly');//show alert message.
                isShowData = false;//return to default case.
                $("#show").html($("#shbr").html());//return to show branch button text.
                // return value stored in msg variable
                
                //maby you want to add the (clear code) here after you sure that data is recived to server
              }
              else{
                alert("data is not added");
              }
            },
            error: function(error){
              console.log("Error:");
              console.log(error);
              alert(error);
            }
          });
        }
      }
  else{
  var TableData;
  TableData = $.toJSON(storeTblValues());

  function storeTblValues()
  {
      var TableData = new Array();

      $('.table tbody tr').each(function(row, tr){
          TableData[row]={
              "id" : $(tr).find('td:eq(0)').text()
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
      $(".table tbody tr").each(function(row,tr){
      $(tr).remove();
      cnt=0;
      });
      $(".table").hide();
      alert(msg.data);
        // return value stored in msg variable
        //maby you want to add the (clear code) here after you sure that data is recived to server
    }
    });
    //for clear the table row after submit(clear code)

  }
}

//--------------getBranchData(show branches)------------------
function getBranchData(){
  //$("#geolocationReq").addClass("dis-none");
  $("#locationReq").addClass("dis-none");
  $("#nameReq").addClass("dis-none");
  if($("#show").html() == $("#back").html())
  { updateDelet = [];
    $(".table tbody tr").each(function(row,tr){
      $(tr).remove();
      cnt=0;
      });
      $(".table").hide();//hide the header row.
      isShowData = false;//return to default case.
      $("#subTable").html("Submit Data");//return the text of the button to defualt case.
      $("#add").prop('disabled',false); //activate add button.
      $("#show").html($("#shbr").html());//get the text of showbranch button from translated html contant.
  }
  else{
    //in case the 
      if(cnt==0){
        $.ajax({
          url: $("#brShowUrl").html(),
          type: "get",
          //type: "post"
          dataType: "json",
          //data: {"action": "loadall", "id": id},
          success: function(data){
              console.log(data);
              count = 0;
              geolocation = 0;
              console.log();
              data.data.forEach(element => {
                geolocation = element.latitude +","+ element.longitude;
                console.log(geolocation);
                $(".tablbody").append('<tr><td id="id_' + count + '">' + element.id + '</td><td id="name_' + count + '">' + element.name + '</td><td id="location_' +
                count + '">' + element.location +
                '</td><td id="geolocation_' + count + '">' + geolocation +'</td><td><button type="button" class="delete btn btn-primary">Delete</button></td><td><button type="button" id="edit" onclick="BindData('+ count +');" class="btn btn-primary">Edit</button></td>');
                cnt++;
                count++;
              });
              $(".table").show();
              isShowData = true;
              $("#subTable").html("Update Data");
              $("#add").prop('disabled',true); 
              $("#show").html($("#back").html());
          },
          error: function(error){
              console.log("Error:");
              console.log(error);
          }
      });
      }
    }
}
//------------------------------------------------------------
//............................................................
//............................................................
//------------------------------------------------------------
$(document).ready(function() {
  $("#add").prop('disabled',false);
  $("#subTable").html("Submit Data");
  $(".table").hide();
  $(document).on('click', '.delete', function() {
    var par = $(this).parent().parent(); //tr
    if(isShowData){
      if(confirm('Are you sure you want to delet this row?')){
          //var temp = par.find('td:eq(0)').text();
          updateDelet.push({id:par.find('td:eq(0)').text()});
          console.log(updateDelet);
          par.css("background-color","red");
          $(this).prop('disabled',true);
          par.find("#edit").prop('disabled',true);
      }
    }else{
      par.remove();
      cnt--;
      if (cnt == 0) {
        $(".table").hide();
      }
    }
  });
  $(document).on('click','#subTable',subTable);
  $(document).on('click','#reset',reset);
});
