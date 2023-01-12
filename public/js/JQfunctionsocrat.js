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
  hide(["#loadshow", "#dexperror", "#back", "#loading"]);
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
  if(count != 0)
  {
    disable(['#showb']);
}
  var storetable = localStorage.getItem('storetable');

  $(document).ready( function () {
    datatable = $('#mytable').DataTable();
  } );
}

function openform()
{
 show(["#back", '#addbutton']);
 $('.cd-popup').addClass('is-visible');
 hide(['#deledit']);
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
    disable(['#showb', '#openmove']);
    localStorage.setItem('count', count);
  }
  error = false;
}

function add(row)
{
  $('.cd-popup').removeClass('is-visible');
  $(".table").show();
  datatable.row.add(row).draw(false);
  count++;
}

function moving(showurl, sendtourl)
{
  tomove.forEach(function(i){
    $('#d' + getdataarray[i]).hide();
  })
  show(['#move']);
  hide(['#del', '#edit']);
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
    hide(["#dexperror"]);
    $("#" + item ).css("border-color", color);
    $('.selected').removeClass('text-danger');
  })
}

function edit()
{

  show(['#updatebutton']);
  hide(['#addbutton', "#close", '#deledit']);
  editclicked = true;
  disable(['#subTable1']);
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
    enable(['#subTable1']);
    hide('#updatebutton');
    show(["#close", '#addbutton']);
    $('.sd-popup').removeClass('is-visible');
  }
  error = false;
}

function buildjson()
{
  pTableData = [];
  order = [];
  addrow ={};
  serror = false;
  datatable.rows().every(function(rowIdx, tableLoop, rowLoop){
    row = this.data();
    order.push(row[0]);
    row.forEach(function(item, index){
      if(index != 0)
      {
      addrow[getdataarray[index-1]] = row[index].toString();
    }
    })
    pTableData.push(addrow);
    addrow ={};
  })
  return JSON.stringify(pTableData);
}

function sendtoadd()
{
  if(datatable.rows().count() > 0)
  {
    errors = [];
    rows =[];
    show(["#loading"]);
    $.ajax({
      type: "POST",
      url: addurl,
      data: "pTableData="+buildjson()+"&_token="+$("input[name=_token]").attr("value"),
      success: function(msg){
        console.log(msg);
        hide(["#loading"]);
        pointer =0;
        done = 0;
        datatable.rows().every(function(rowIdx, tableLoop, rowLoop){
          row = this.data() ;
          seterror = true;
          getdataarray.forEach(function(item, index, arr){
            if(msg.data[pointer][item] == pTableData[pointer][item])
            {
              done++;
            }
            if(done == todone)
            {
              done = 0;
              seterror = false;
            }
          })
          if(seterror)
          {
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
          localStorage.setItem('count', count);
          localStorage.setItem('storetable', '');
          errors = [];
          enable(['#showb', '#addbutton']);
        }else
        {
          disable(['#addbutton']);
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
    show(["#loading"]);
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
        hide(["#loading"]);
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
          disable(['#addbutton']);
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
  $("#subTable1").attr("onclick","sendtoedit('"+ sendtourl +"')");
  disable(['#addbutton', '#openadd']);
  hide(["#openmove", '#addemp', '#showb']);
  show(["#loadshow", "#back"]);
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
  hide(['#move']);
  show(['#del', '#edit']);
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
    enable(['#showb', '#openmove', '#openadd']);
    datatable.clear().draw();
    count = 0;
    localStorage.setItem('count', count);
    editdata = [];
    errors = [];
    show(['#addemp']);
    hide(["#dexperror"]);
    resetshow();
  }
}

function resetshow()
{
  enable(['#addbutton'])
  editdata = [];
  $("#subTable1").attr("onclick","sendtoadd('"+ addurl +"')");
  show(['#showb', "#openmove"]);
  hide(["#back"]);
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
        countonclick = Number($(this).find("td:first").html());
        $('.cd-popup').addClass('is-visible');
        show(['#deledit']);
        hide(["#addemp", '#addbutton']);
        reset();
        exp = true;
        ecount = 1;
        tdcontent = datatable.row('.selected').data();
        getdataarray.forEach(function(item){
          content = tdcontent[ecount].toString();
          $("#" + item).val(content.split(","));
          ecount++;
          theError = errors[order.indexOf(countonclick)]
          if(serror && Object.keys(theError).indexOf(item)+1)
          {
            ;
            $("#" + item + "error").html(JSON.stringify(theError[item]));
            $("#" + item + "error").css('color', 'red');
            if(theError[item])
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
