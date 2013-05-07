<script type="text/javascript">
$(function(){ 
  $("#list").jqGrid({
    url:'<?php echo (site_url("grid/settings")); ?>',
	editurl: '<?php echo (site_url("grid/settings")); ?>',
    datatype: 'xml',
    mtype: 'POST',
    colNames:['Name','Value','Description'],
    colModel :[ 
      {name:'name', index:'name', width:80, editable:false, sortable:true}, 
      {name:'value', index:'value', width:60, editable:true, sortable:true}, 
      {name:'description', index:'description', width:250, editable:false, sortable:true}, 
    ],
    pager: '#pager',
    rowNum:50,
    rowList:[25,50,100],
    sortname: 'name',
    sortorder: 'asc',
    viewrecords: true,
	autowidth: true,
	//altRows:true,
    caption: 'System Settings'
  }); 
  $("#list").jqGrid(
  	'navGrid',
	'#pager',
	{edit:true,add:false,del:false,search:false},//Options
	{reloadAfterSubmit:true, closeAfterEdit:true},//Edit Options
	{reloadAfterSubmit:true, closeAfterAdd:true},//Add Options
	{reloadAfterSubmit:true}//Delete Options
  ); 
}); 
</script>
<h1>System Settings</h1>
<table id="list"></table> 
<div id="pager"></div>
<p>If you need to modify your database information or your website path (<em>currently <strong><?php echo(base_url()); ?></strong></em>)</strong> you must edit <em><strong>config.php</strong></em></p>
