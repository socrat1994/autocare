//editclicked revision,, edit getarraydata for edit
var pTableData = '';
var count = 0;
var col_count = 0;
var formElements =[];
var dontChang =[];
var error = false;
var serror = false;
var color = '#CED4DA';
var toSuccess = 0;
var errors =[];
var showdata = [];
var editdata = [];
var updateOnError = [];
var editrow = {};
var remove_exp = [];
var addUrl = '';
var storetable = "";
var rowonclick = 0;
var countonclick = 0;
var editclicked = false;
var datatable ={};
var order = [];
var hideOnShow = [];
var hideOnMove = [];
var datatable;
var config = {};

window.onload = function(){
  hide(["#loadshow", "#loading", '#tabletools']);
}
function configer()
{
  toSuccess = config.toSuccess;
  addUrl = config.addUrl;
  formElements = config.formElements;
  col_count = formElements.length + 1;
  dontChang = config.dontChang;
  hideOnShow = config.hideOnShow;
  hideOnMove = config.hideOnMove;
  $('#addAny').html(config.addAny);
  hide(["#loadshow", "#dexperror", "#back", "#loading", '#updatebutton', '#move']);
  show(['#tabletools']);
  storing();
}

function storing()
{
  datatable = $('#mytable').DataTable();
  $(document).ready( function () {
    datatable = $('#mytable').DataTable();
    //getdbAll();
    if(datatable.rows().count() != 0)
    {
      disable(['#showb']);
    }
  });
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
  formElements.forEach(function(item){
    validate(item);
    row.push($('#'+item).val());
  })
  if(!error)
  {
    add(row);
    reset();
    disable(['#showb', '#openmove']);
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
  hideOnMove.forEach(function(i){
    $('#d' + formElements[i]).hide();
  })
  hide(['#del', '#edit']);
  showData(showurl, sendtourl);
  show(['#move']);
}

function del(dcount)
{
  if(showdata != '')
  {
    editdata.push({'id':showdata[dcount].id});
    remove_exp.push(Number(dcount));
  }
  $('.selected').addClass('d-none d');
  deletedb(dcount);
}

function reset()
{
  formElements.forEach(function(item, index){
    if(!dontChang.includes(index))
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

  var rowIndex = datatable.row('.selected').data()[0];
  colIndex = 1 ;
  formElements.forEach(function(item){
    cell = datatable.cell(datatable.row('.selected').index(), colIndex);
    validate(item);
    if(!error)
    {
      if(showdata != '')
      {
        exist = updateOnError[order.indexOf(rowIndex)];
        if(exist)
        {
          exist = exist[item];
        }
        if(((cell.data()).toString() != ($("#" + item).val()).toString()) || exist)
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
    keys = Object.keys(editrow);
    if(showdata != '' && keys.length > 0)
    {
      isExistInEdit = remove_exp.indexOf(rowIndex)+1;
      if(isExistInEdit)
      {
        keys.forEach(function(item){
          editdata[isExistInEdit-1][item] = editrow[item];
        })
      }else{
        editdata.push(editrow);
        remove_exp.push(rowIndex);
      }
      editrow ={};
    }
    reset();
    editclicked = false;
    enable(['#subTable1']);
  }
  hide(['#updatebutton']);
  show(["#close", '#addbutton']);
  $('.cd-popup').removeClass('is-visible');
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
    row.forEach(function(item, index){
      if(index != 0)
      {
        addrow[formElements[index-1]] = row[index].toString();
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
      url: addUrl,
      data: "pTableData="+buildjson()+"&_token="+$("input[name=_token]").attr("value"),
      success: function(msg){
        console.log(msg);
        hide(["#loading"]);
        pointer =0;
        done = 0;
        datatable.rows().every(function(rowIdx, tableLoop, rowLoop){
          row = this.data() ;
          seterror = true;
          formElements.forEach(function(item, index, arr){
            if(msg.data[pointer][item] == pTableData[pointer][item])
            {
              done++;
            }
            if(done == toSuccess)
            {
              done = 0;
              seterror = false;
            }
          })
          if(seterror)
          {
            rows.push(row);
            order.push(row[0]);
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
      order.push((item[0]));
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
            toSuccess = index+1;
            if(data.data[pointer][item] == editdata[pointer][item])
            {
              done++;
            }
          })
          if(done != toSuccess)
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
          updateOnError = editdata;
          editdata = [];
          remove_exp = [];
          disable(['#addbutton']);
        }
      }
    });
  }
}

function showPage(indexUrl)
{
  if(Object.keys(datatable).length == 0 || datatable.rows().count() == 0)
  {
    $.ajax({
      url: indexUrl,
      type: "get",
      success: function(msg){
        $( "#container" ).replaceWith( msg);
        $( "#mytable_wrapper" ).replaceWith( config.tableHeaders);
        configer();
      }})
    }else
    {
      alert(globalConfig.cantloadpage);
    }
  }

  function showData(showurl, sendtourl)
  {
    row = [];
    hideOnShowarr = [];
    hideOnShow.forEach(function(i){
      datatable.column(i + 1).visible(false);
      $('#d' + formElements[i]).hide();
    })
    $("#subTable1").attr("onclick","sendtoedit('"+ sendtourl +"')");
    disable(['#addbutton', '#openadd']);
    hide(["#openmove", '#addAny', '#showb', '#move']);
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
          formElements.forEach(function(item, index){
            console.log(index);
            if(hideOnShow.indexOf(index) >= 0)
            {
              console.log(hideOnShow.indexOf(index));
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
    hideOnMove.forEach(function(i){
      $('#d' + formElements[i]).show();
    })
    hide(['#move']);
    show(['#del', '#edit', '#close']);
  }

  function backto()
  {
    if(confirm(globalConfig.backmessage) == true)
    {
      hideOnShow.forEach(function(i){
        datatable.column(i + 1).visible(true);
        $('#d' + formElements[i]).show();
      })
      resetmove();
      enable(['#showb', '#openmove', '#openadd']);
      datatable.clear().draw();
      count = 0;
      localStorage.setItem('count', count);
      errors = [];
      show(['#addAny']);
      hide(["#dexperror"]);
      resetshow();
    }
  }

  function resetshow()
  {
    enable(['#addbutton', '#openadd'])
    editdata = [];
    updateOnError = [];
    remove_exp = [];
    serror = false;
    $("#subTable1").attr("onclick","sendtoadd('"+ addUrl +"')");
    show(['#showb', "#openmove",'#close']);
    hide(["#back", '#updatebutton']);
  }

  $('#del').live('click', function(){
    del(countonclick);
    $('.cd-popup').removeClass('is-visible');
  })

  $('#edit').live('click', function(){
    edit();
  })

  $('#move').live('click', function(){
    edit();
  })

  $('#close').live('click', function(event){
    $('.cd-popup').removeClass('is-visible');
    if( $(event.target).is('.cd-popup-close') /*|| $(event.target).is('.cd-popup')*/ ) {
      event.preventDefault();
      $(this).removeClass('is-visible');
    }
  });

  $('#mytable tbody tr').live('click', function () {
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
          hide(["#addAny", '#addbutton']);
          reset();
          exp = true;
          ecount = 1;
          tdcontent = datatable.row('.selected').data();
          formElements.forEach(function(item){
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
  $('#mytable').live( 'click', 'tbody td', function () {
  } );
