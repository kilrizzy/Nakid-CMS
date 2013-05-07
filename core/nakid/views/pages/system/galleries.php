<script type="text/javascript">
//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] 
$(function(){ 
  $("#list").jqGrid({
    url:'<?php echo (site_url("grid/galleries")); ?>',
	editurl: '<?php echo (site_url("grid/galleries")); ?>',
    datatype: 'xml',
    mtype: 'POST',
    colNames:['','Name','Keyword','',''],
    colModel :[ 
      {name:'edit', index:'edit', width:40, editable:false, sortable:false, search:false}, 
      {name:'title', index:'title', width:200, editable:true, sortable:true, searchoptions:{sopt: ['cn']}}, 
	  {name:'keyword', index:'keyword', width:150, editable:true, sortable:false, search:false}, 
      {name:'connect', index:'connect', width:60, editable:false, sortable:false, search:false},
	  {name:'delete', index:'delete', width:40, editable:false, sortable:false, search:false}
    ],
    pager: '#pager',
    rowNum:50,
	height:300,
    rowList:[25,50,100],
    sortname: 'title',
    sortorder: 'asc',
    viewrecords: true,
	autowidth: true,
	//altRows:true,
    caption: 'Image Galleries'
  }); 
  $("#list").jqGrid(
  	'navGrid',
	'#pager',
	{edit:false,add:<?php echo($permission_gallery); ?>,del:false,search:true},//Options
	{afterSubmit:processAddEdit, reloadAfterSubmit:true, closeAfterEdit:true},//Edit Options
	{afterSubmit:processAddEdit, reloadAfterSubmit:true, closeAfterAdd:true},//Add Options
	{reloadAfterSubmit:true}//Delete Options
  ); 
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
<h1>Edit Galleries</h1>
<table id="list"></table> 
<div id="pager"></div>
<dl>
<dd>Create photo galleries to display a group of images on your website</dd>
</dl>