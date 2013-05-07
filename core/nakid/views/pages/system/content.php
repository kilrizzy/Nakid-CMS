<script type="text/javascript">
//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] 
$(function(){ 
  $("#list").jqGrid({
    url:'<?php echo (site_url("grid/content")); ?>',
	editurl: '<?php echo (site_url("grid/content")); ?>',
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
    caption: 'Content Blocks'
  }); 
  $("#list").jqGrid(
  	'navGrid',
	'#pager',
	{edit:false,add:<?php echo($permission_cms_add); ?>,del:false,search:true},//Options
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
<h1>Edit Content</h1>
<table id="list"></table> 
<div id="pager"></div>
<dl>
<dd>Give your website editable content by adding <strong>content blocks</strong>. Content blocks allow you to have one or more sections of editable content to your site. For example, you may want to make a block for every page ("Home", "About", "Contact"). Alternatively, you may only want certain parts of your content to be editable. In this case you may have multiple blocks per page ("Home Bottom", "About Left", "About Top")</dd>
<dd><strong>Keywords</strong> are short unique identifiers for this block to make implimentation on your website easier. You do not need to create a keyword, but if you do it may help when linking Nakid CMS content to your website.</dd>
</dl>