var getdatafrompage ='';
var getstayfrompage ='';
var pTableData = '';
var count = 0;
var col_count = 0;
var getdataarray =[];
var getstayarray =[];
var error = false;
var serror = false;
var color = '#CED4DA';
var todone = 0;
var errors =[];
var showdata = [];
var editdata = [];
var editrow = {};
var remove_exp = [];
var text = '';
var addurl = '';
var storetable = "";
var rowonclick = 0;
var countonclick = 0;
var editclicked = false;
var diacontent = '';
var datatable ={};
var order = [];
var tohide = [];
var tomove = [];
var datatable;

window.onload = function()
{
  diacontent = $('#dia').html();
  $("#loadshow").hide();
  $("#dexperror").hide();
  $("#back").hide();
  $("#loading").hide();
  todone = Number($('#todone').html());
  text = $('#text').html();
  addurl = $('#addurl').html();
  getdatafrompage = $('#data').html();
  getstayfrompage = $('#stay').html();
  getdataarray = getdatafrompage.split(",");
  col_count = getdataarray.length + 1;
  getstayarray = getstayfrompage.split(",").map(Number);
  $('#updatebutton').hide();
  tohide = ($('#tohide').html()).split(',').map(Number);
  tomove = ($('#tomove').html()).split(',').map(Number);
  storing();
}

function storing()
{

 var storetable = localStorage.getItem('storetable');
  //count = localStorage.getItem("count");
  if(count != 0)
  {
  //  $(".tablbody").append(storetable);
    $('#showb').prop('disabled', true);
}
  var storetable = localStorage.getItem('storetable');

  $(document).ready( function () {
    datatable = $('#mytable').DataTable();
  } );
}

function openform()
{
 $("#back").show();
 $('.cd-popup').addClass('is-visible');
 $('#deledit').hide();
 $('#addbutton').show();
}

function validate(item)
{
  const inpObj = document.getElementById(item);
  if (!inpObj.checkValidity())
  {
    error = true;
    document.getElementById(item + "error").innerHTML = (inpObj.validationMessage).fontcolor("red");
    border = document.getElementById(item);
    border.style.borderColor = "red" ;
  }
}

function formdata()
{
  var attrcounter = 0;
  var row = [];
  row.push(count);
  getdataarray.forEach(function(item){
    validate(item);
    row.push($('#'+item).val());
  })
  if(!error)
  {
    add(row);
    localStorage.setItem('storetable', $(".tablbody")[0].innerHTML);
    console.log($('#mytable'));
    reset();
    $('#showb').prop('disabled', true);
    localStorage.setItem('count', count);
  }
  error = false;
}

function add(row)
{
  $(".table").show();
  datatable.row.add(row).draw(false);
  count++;
  $('#tablecon')[0].scrollIntoView();
}

function moving(showurl, sendtourl)
{
  tomove.forEach(function(i){
    $('#d' + getdataarray[i]).hide();
  })
  $('#move').show();
  $('#del').hide();
  $('#edit').hide();
  show(showurl, sendtourl);
}

function del(dcount)
{

  if(showdata != '')
  {
    editdata.push({'id':showdata[dcount].id});
    remove_exp.push(Number(dcount));
  }
  $('.selected').addClass('d-none d');
  localStorage.setItem('storetable', $(".tablbody")[0].innerHTML);
}

function reset()
{
  getdataarray.forEach(function(item, index){
    if(!getstayarray.includes(index))
    {
      //  $("#" + item).val('');
    }
    $("#" + item + "error").html('');
    $("#dexperror").hide();
    $("#" + item ).css("border-color", color);
    $('.selected').removeClass('text-danger');
  })
}

function edit()
{

  $('#updatebutton').show();
  $('#addbutton').hide();
  $("#close").hide();
  $('#deledit').hide();
  editclicked = true;
  $('#subTable1').prop('disabled', true);
}

function update()
{
  $('.cd-popup').removeClass('is-visible');
  var rowIndex = datatable.row('.selected').data()[0];
  colIndex = 1 ;
  getdataarray.forEach(function(item){
    cell = datatable.cell(rowIndex, colIndex);
    validate(item);
    if(!error)
    {
      if(showdata != '')
      {
        if((cell.data()).toString() != ($("#" + item).val()).toString())
        {
          editrow.id = showdata[rowIndex].id;
          editrow[item] = ($("#" + item).val()).toString();
        }
      }
      cell.data($("#" + item).val());
      colIndex++;
    }
  })
  if(!error)
  {
    if(showdata != '' && Object.keys(editrow).length > 0)
    {
      editdata.push(editrow);
      remove_exp.push(rowIndex);
      console.log(editdata);
      editrow ={};
    }
    reset();
    editclicked = false;
    $('#subTable1').prop('disabled', false);
    $('#updatebutton').hide();
    $('#addbutton').show();
    $("#close").show();
    //$('#del').show();
    $('.sd-popup').removeClass('is-visible');
  }
  error = false;
}

function buildjson()
{
  var pointer = 1;
  pTableData = [];
  order = [];
  addrow ={};
  serror = false;
  $('#mytable tr td').each(function(){
    if(pointer % col_count == 1)
    {
      order.push($(this).text());
    }
    if(pointer % col_count != 1)
    {
      addrow[getdataarray[(pointer-2)%col_count]] = $(this).text();
    }
    if(pointer % col_count == 0)
    {
      pTableData.push(addrow);
      addrow ={};
    }
    pointer++;
  })
  return JSON.stringify(pTableData);
}

function sendtoadd()
{
  if(datatable.rows().count() > 0)
  {
    errors = [];
    rows =[];
    $("#loading").show();
    $.ajax({
      type: "POST",
      url: addurl,
      data: "pTableData="+buildjson()+"&_token="+$("input[name=_token]").attr("value"),
      success: function(msg){
        console.log(msg);
        $("#loading").hide();
        pointer =0;
        done = 0;
        datatable.rows().every(function(rowIdx, tableLoop, rowLoop){
          row = this.data() ;
          seterror = true;
          getdataarray.forEach(function(item, index, arr){
            if(msg.data[pointer][item] == pTableData[pointer][item])
            {
              done++;
              console.log(pTableData[pointer][item]);
            }
            if(done == todone)
            {
              done = 0;
              seterror = false;
            }
          })
          if(seterror)
          {
            console.log(errors);
            rows.push(row);
            errors.push(msg.data[pointer]);
            serror = true;
          }
          pointer++;
        });
        datatable.clear().draw();
        rows.forEach((item, i) => {
          add(item);
          $('#mytable tr').eq(i + 1).addClass('text-danger');
        });
        if (datatable.rows().count() == 0)
        {
          count = 0;
          $('#showb').prop('disabled', false);
          localStorage.setItem('count', count);
          localStorage.setItem('storetable', '');
          errors = [];
          $('#addbutton').prop('disabled', false)
        }else
        {
          $('#addbutton').prop('disabled', true);
          localStorage.setItem('storetable', $(".tablbody")[0].innerHTML);
        }
      }
    });
  }
}

function sendtoedit(route)
{
  rows=[];
  if(editdata.length > 0)
  {
    $("#loading").show();
    pointer =0;
    order = [];
    showdata2 = [];
    $('.d').removeClass('d-none d');
    datatable.rows().every(function(){
      if (remove_exp.includes(this.data()[0]))
      {
        rows.push(this.data());
      }
      pointer++;
    });
    datatable.clear().draw();
    rows.forEach((item, i) => {
      add(item);
      order.push((item[0]).toString());
    });
    errors = [];
    $.ajax({
      type: "POST",
      url: route,
      data: "pTableData="+JSON.stringify(editdata)+"&_token="+$("input[name=_token]").attr("value"),
      success: function(data){
        $("#loading").hide();
        pointer =0;
        done = 0;
        subrows = [];
          rows.forEach((row, i) => {
          Object.keys(editdata[pointer]).forEach(function(item, index){
            console.log(item);
            todone = index+1;
            if(data.data[pointer][item] == editdata[pointer][item])
            {
              done++;
            }
          })
          if(done != todone)
          {
            subrows.push(row);
            errors.push(data.data[pointer]);
            serror = true;
          }
          done = 0;
          pointer++;
        })

        datatable.clear().draw();
        subrows.forEach((item, i) => {
          add(item);
          $('#mytable tr').eq(i + 1).addClass('text-danger');
        });
        if (subrows.length == 0)
        {
          serror = false;
          count = 0;
          resetshow();
        }else
        {
          editdata = [];
          remove_exp = [];
          $('#addbutton').prop('disabled', true);
        }
      }
    });
  }
}

function show(showurl, sendtourl)
{
  row = [];
  tohidearr = [];
  tohide.forEach(function(i){
    datatable.column(i + 1).visible(false);
    $('#d' + getdataarray[i]).hide();
  })
  $("#openmove").hide();
  $("#loadshow").show();
  $('#addemp').hide();
  $("#subTable1").attr("onclick","sendtoedit('"+ sendtourl +"')");
  $('#addbutton').prop('disabled', true);
  $('#openadd').prop('disabled', true);
  $('#showb').hide();
  $("#back").show();
  $.ajax({
    url: showurl,
    type: "get",
    dataType: "json",
    success: function(msg){
      $("#loadshow").hide();
      showdata = msg.data;
      msg.data.forEach(function(r){
        row.push(count);
        getdataarray.forEach(function(item, index){
          console.log(index);
          if(tohide.indexOf(index) >= 0)
          {
            console.log(tohide.indexOf(index));
            row.push('#');
          }else{
            row.push(r[item]);
          }
        })
        add(row);
        row = [];
      })
      localStorage.setItem('storetable', '');
      localStorage.setItem('count', 0);
    }
  })
}

function resetmove()
{
  tomove.forEach(function(i){
    $('#d' + getdataarray[i]).show();
  })
  $('#move').hide();
  $('#del').show();
  $('#edit').show();
}

function backto()
{
  if(confirm(text) == true)
  {
    tohide.forEach(function(i){
      datatable.column(i + 1).visible(true);
      $('#d' + getdataarray[i]).show();
    })
    resetmove();
    $('#showb').prop('disabled', false);
    datatable.clear().draw();
    count = 0;
    localStorage.setItem('count', count);
    editdata = [];
    $('#openadd').prop('disabled', false);
    $('#addemp').show();
    $("#dexperror").hide();
    resetshow();
  }
}

function resetshow()
{
  $('#addbutton').prop('disabled', false);//
  editdata = [];//
  $("#subTable1").attr("onclick","sendtoedit('"+ addurl +"')");//
  $('#showb').show();//
  $("#back").hide();//
  $("#openmove").show();
}

$('#del').on('click', function(){
  del(countonclick);
  $('.cd-popup').removeClass('is-visible');
})

$('#edit').on('click', function(){
  edit();
})

$('#move').on('click', function(){
  edit();
})

$('.cd-popup').on('click', function(event){
  if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup') ) {
    event.preventDefault();
    $(this).removeClass('is-visible');
  }
});

$('#mytable tbody').on('click', 'tr', function () {
  if(!editclicked)
  {
  if ($(this).hasClass('selected')) {
    $(this).removeClass('selected');
  } else {
    if(datatable.rows().count() > 0)
    {
      datatable.$('tr.selected').removeClass('selected');
      $(this).addClass('selected');
        rowonclick = $(this).closest('tr').index();
        countonclick = $(this).find("td:first").html();
        $('.cd-popup').addClass('is-visible');
        $('#deledit').show();
        $("#addemp").hide();
        $('#addbutton').hide();
        reset();
        exp = true;
        ecount = 1;
        tdcontent = datatable.row('.selected').data();
        getdataarray.forEach(function(item){
          content = tdcontent[ecount].toString();
          $("#" + item).val(content.split(","));
          ecount++;
          if(serror)
          {
            theError = errors[order.indexOf(countonclick)][item];
            $("#" + item + "error").html(JSON.stringify(theError));
            $("#" + item + "error").css('color', 'red');
            if(theError)
            {
              $("#" + item ).css("border-color", 'red');
              exp = false;
            }
          }
        })
        if(exp && serror)
        {
          $("#dexperror").show();
          $("#experror").html(errors[order.indexOf(countonclick)]);
        }
        exp = true;
    }
  }
  }
});
$('#mytable').on( 'click', 'tbody td', function () {
} );
