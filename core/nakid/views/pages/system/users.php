<script type="text/javascript">
//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] 
$(function(){ 
  $("#list").jqGrid({
    url:'<?php echo (site_url("grid/users")); ?>',
	editurl: '<?php echo (site_url("grid/users")); ?>',
    datatype: 'xml',
    mtype: 'POST',
    colNames:['Username','Password', 'Email Address','First Name','Last Name','Permissions'],
    colModel :[ 
      {name:'username', index:'username', width:60, editable:true, sortable:true, searchoptions:{sopt: ['cn']}}, 
      {name:'password', index:'password', width:40, editable:true, sortable:false,search:false, hidden:true, editrules:{edithidden:true}}, 
      {name:'email', index:'email', width:100, editable:true, sortable:true, searchoptions:{sopt: ['cn']}}, 
      {name:'fname', index:'fname', width:60, editable:true, sortable:true, searchoptions:{sopt: ['cn']}}, 
	  {name:'lname', index:'lname', width:60, editable:true, sortable:true, searchoptions:{sopt: ['cn']}}, 
      {name:'permissions', index:'permissions', width:140, editable:false, sortable:false, search:false} 
    ],
    pager: '#pager',
    rowNum:50,
	height:200,
    rowList:[25,50,100],
    sortname: 'username',
    sortorder: 'asc',
    viewrecords: true,
	autowidth: true,
	//altRows:true,
    caption: 'Users'
  }); 
  $("#list").jqGrid(
  	'navGrid',
	'#pager',
	{edit:true,add:true,del:true,search:true},//Options
	{afterSubmit:processAddEdit, reloadAfterSubmit:true, closeAfterEdit:true},//Edit Options
	{afterSubmit:processAddEdit, reloadAfterSubmit:true, closeAfterAdd:true},//Add Options
	{reloadAfterSubmit:true}//Delete Options
  ); 
  //Colorbox
 // $(".framepop").colorbox({});
}); 
//Check for errors
function processAddEdit(response, postdata) {
  var success = true;
  var message = "";
  var respxml = response.responseText;
  if(respxml.indexOf("<error>") > 0){
	  success = false;
	  var str1 = respxml.split("<error>");
	  var str2 = str1[1].split("</error>");
	  message = str2[0];
  }
  var new_id = "1";
  return [success,message,new_id];
}
</script>
<h1>Edit Users</h1>
<table id="list"></table> 
<div id="pager"></div>
<dl>
<dd>To <strong>change a password</strong> when editing a user, swap "*****" with the new password, otherwise leave the field alone.</dd>
</dl>