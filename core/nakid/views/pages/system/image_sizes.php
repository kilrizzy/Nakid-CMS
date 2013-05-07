<script type="text/javascript">
$(function(){ 
  $("#list").jqGrid({
    url:'<?php echo (site_url("grid/image_sizes")); ?>',
	editurl: '<?php echo (site_url("grid/image_sizes")); ?>',
    datatype: 'xml',
    mtype: 'POST',
    colNames:['Name','Preview', 'Width','Height','Cropping'],
    colModel :[ 
      {name:'name', index:'name', width:80, editable:true, sortable:true}, 
      {name:'preview', index:'preview', width:60, editable:false, sortable:false}, 
      {name:'width', index:'width', width:100, editable:true, sortable:true}, 
      {name:'height', index:'height', width:80, editable:true, sortable:true}, 
      {name:'options', index:'options', width:80, editable:true, edittype:"select", sortable:true, editoptions:{ value:":No Crop;zc:Zoom Crop" }} 
    ],
    pager: '#pager',
    rowNum:50,
    rowList:[25,50,100],
    sortname: 'name',
    sortorder: 'asc',
    viewrecords: true,
	autowidth: true,
	//altRows:true,
    caption: 'Image Sizes'
  }); 
  $("#list").jqGrid(
  	'navGrid',
	'#pager',
	{edit:true,add:true,del:true,search:false},//Options
	{reloadAfterSubmit:true, closeAfterEdit:true},//Edit Options
	{reloadAfterSubmit:true, closeAfterAdd:true},//Add Options
	{reloadAfterSubmit:true}//Delete Options
  ); 
}); 
</script>
<h1>Edit Image Sizes</h1>
<table id="list"></table> 
<div id="pager"></div>
<dl>
<dd>This page gives you the ability to modify image sizes you will use throughout the CMS.</dd>
<dt>Name</dt>
<dd>Just a name to reference this image profile</dd>
<dt>Width / Height</dt>
<dd>The size (in pixels) of the image</dd>
<dt>Cropping</dt>
<dd>How the image will be cropped. If "No Crop" is set, the image will be resized based on the boundries of set width and height. If "Zoom Crop" is set, the image will be scaled and cropped to the exact dimensions set.</dd>
</dl>
