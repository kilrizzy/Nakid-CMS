<h1>Edit <?php echo($gallery['title']); ?></h1>
<?php 
$form_attributes = array('class' => 'niceform2', 'id' => 'form_gallery_edit', 'name' =>'form_gallery');
echo form_open(current_url(),$form_attributes); 
?>
<ul>
  <li>
    <label for="title">Title:</label>
    <?php 
		$field = array(
        	'name'        => 'title',
            'id'          => 'title',
			'class'       => 'textfield',
            'value'       => $gallery['title'],
            'maxlength'   => '50'
        );
		echo form_input($field);
		?>
    <small>The title will not be displayed on your page. This is for administrative use only.</small> </li>
  <li>
    <label for="description">Description:</label>
    <?php 
		$field = array(
        	'name'        => 'description',
            'id'          => 'description',
			'class'       => 'editor',
            'value'       => $gallery['description']
        );
		echo form_textarea($field);
		?>
  </li>
</ul>
<ul><li class="submit"> <?php echo form_submit('submitbt', 'Save'); ?> </li></ul>
<h3 class="collapse">Category Options</h3>
<div class="collapsible" style="display:none;">
<script type="text/javascript">
//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] 
$(function(){ 
  $("#category_list").jqGrid({
    url:'<?php echo (site_url("grid/gallery_categories/".$gallery['id'])); ?>',
	editurl: '<?php echo (site_url("grid/gallery_categories/".$gallery['id'])); ?>',
    datatype: 'xml',
    mtype: 'POST',
    colNames:['Name','Order'],
    colModel :[ 
      {name:'title', index:'title', width:400, editable:true, sortable:true, searchoptions:{sopt: ['cn']}}, 
	  {name:'order', index:'order', width:100, editable:true, sortable:false, search:false}
    ],
    pager: '#category_pager',
    rowNum:50,
	height:150,
    rowList:[25,50,100],
    sortname: 'order',
    sortorder: 'desc',
    viewrecords: true,
	autowidth: true,
	//altRows:true,
    caption: 'Categories'
  }); 
  $("#category_list").jqGrid(
  	'navGrid',
	'#category_pager',
	{edit:true,add:true,del:true,search:true},//Options
	{afterSubmit:category_processAddEdit, reloadAfterSubmit:true, closeAfterEdit:true},//Edit Options
	{afterSubmit:category_processAddEdit, reloadAfterSubmit:true, closeAfterAdd:true},//Add Options
	{reloadAfterSubmit:true}//Delete Options
  ); 
}); 
//Check for errors
function category_processAddEdit(response, postdata) {
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
<table id="category_list"></table> 
<div id="category_pager"></div>
<ul>
<li>
    <label for="category_display">Show Categories:</label>
    <?php 
		$options = array(
        	'hide'  => 'Hide Category List' ,
            'above'    => 'Above Images',
			'side'    => 'In Sidebar'
         );
		echo form_dropdown('category_display', $options, $gallery['data']['category_display']);
		?>
</li>
<li>
    <label for="category_empty">If Category Contains No Images:</label>
    <?php 
		$options = array(
        	'hide'  => 'Hide Category Link' ,
            'show'    => 'Show Category Link'
         );
		echo form_dropdown('category_empty', $options, $gallery['data']['category_empty']);
		?>
</li>
<li>
    <label for="category_front">Front Page:</label>
    <?php 
		$options = array(
        	'all'  => 'List All Images' ,
            'none'    => 'List No Images',
			'first'    => 'List Images From The First Category',
         );
		echo form_dropdown('category_front', $options, $gallery['data']['category_front']);
		?>
</li>
</ul>
</div>
<h3 class="collapse">Additional Options</h3>
<ul class="collapsible" style="display:none;">
<li>
    <label for="keyword">Keyword:</label>
    <?php 
		$field = array(
        	'name'        => 'keyword',
            'id'          => 'keyword',
			'class'       => 'textfield',
            'value'       => $gallery['keyword'],
            'maxlength'   => '50'
        );
		echo form_input($field);
		?>
    <small>The key is an optional field you can use to refer to when calling this content from your website. This would be in place of calling the content from it's id</small> </li>
<li>
    <label for="image_titles">Show Image Titles:</label>
    <?php 
		$options = array(
        	'above'  => 'Above Image',
            'below'    => 'Below Image',
            'none'   => 'None'
         );
		 echo form_dropdown('image_titles', $options, $gallery['data']['image_titles']);
		?>
    <small>When displaying image thumbnails, should image titles be shown above the image, below the image, or not display.</small> </li>
    <li>
    <label for="thumb_width">Thumbnail Width:</label>
    <?php 
		$field = array(
        	'name'        => 'thumb_width',
            'id'          => 'thumb_width',
			'class'       => 'textfield',
            'value'       => $gallery['data']['thumb_width'],
            'maxlength'   => '5'
        );
		echo form_input($field);
		?>
	</li>
    <li>
    <label for="thumb_height">Thumbnail Height:</label>
    <?php 
		$field = array(
        	'name'        => 'thumb_height',
            'id'          => 'thumb_height',
			'class'       => 'textfield',
            'value'       => $gallery['data']['thumb_height'],
            'maxlength'   => '5'
        );
		echo form_input($field);
		?>
	</li>
    <li>
    <label for="thumb_aspect">Thumbnail Aspect Ratio:</label>
    <?php 
		$options = array(
			'crop'    => 'Scale and Crop',
        	'maintain'  => 'Maintain Aspect Ratio' ,
            'ignore'    => 'Ignore Aspect Ratio'
			
         );
		 echo form_dropdown('thumb_aspect', $options, $gallery['data']['thumb_aspect']);
		?>
	</li>
    <li>
    <label for="image_width">Image Width:</label>
    <?php 
		$field = array(
        	'name'        => 'image_width',
            'id'          => 'image_width',
			'class'       => 'textfield',
            'value'       => $gallery['data']['image_width'],
            'maxlength'   => '5'
        );
		echo form_input($field);
		?>
	</li>
    <li>
    <label for="image_height">Image Height:</label>
    <?php 
		$field = array(
        	'name'        => 'image_height',
            'id'          => 'image_height',
			'class'       => 'textfield',
            'value'       => $gallery['data']['image_height'],
            'maxlength'   => '5'
        );
		echo form_input($field);
		?>
	</li>
    <li>
    <label for="image_aspect">Image Aspect Ratio:</label>
    <?php 
		$options = array(
			'crop'    => 'Scale and Crop',
        	'maintain'  => 'Maintain Aspect Ratio' ,
            'ignore'    => 'Ignore Aspect Ratio'
			
         );
		 echo form_dropdown('image_aspect', $options, $gallery['data']['image_aspect']);
		?>
	</li>
</ul>
<h2>Images</h2>
<div class="over">
        <label>Upload Image</label>
        <?php 
		$field = array(
        	'name'        => 'browse_server',
            'id'          => 'browse_server',
			'class'       => 'button',
            'content'       => 'Click Here to Browse the Server and Upload Images',
            'onclick'   => 'openKCFinder(form_gallery.images)'
        );
		echo form_button($field);
		?>
        <small>Upload files and hold the Ctrl key to select multiples. With multiples selected, right click one of them and choose "Select"</small> 
      </div>
      <div class="over">
        <label>Image Paths</label>
         <?php 
		$field = array(
        	'name'        => 'images',
            'id'          => 'images',
            'value'       => ''
        );
		echo form_textarea($field);
		?>
      </div>
<ul><li class="submit"> <?php echo form_hidden('action', 'edit_gallery'); ?> <?php echo form_submit('submitbt', 'Save'); ?> </li></ul>
<?php echo form_close(); ?>
<?php 
$form_attributes = array('class' => 'niceform2', 'id' => 'form_resize', 'name' =>'form_resize');
echo form_open(current_url(),$form_attributes); 
?>
<h1>Existing Images <?php echo form_hidden('action', 'resize_images'); ?> <?php echo form_submit('resizebt', 'Update Sizes'); ?></h1>
<?php echo form_close(); ?>
<script type="text/javascript">
//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] 
$(function(){ 
  $("#list").jqGrid({
    url:'<?php echo (site_url("grid/gallery_images")."/".$gallery['id']); ?>',
	editurl: '<?php echo (site_url("grid/gallery_images")."/".$gallery['id']); ?>',
    datatype: 'xml',
    mtype: 'POST',
    colNames:['','Title','Order','Categories'],
    colModel :[ 
      {name:'image', index:'image', width:80, editable:false, sortable:false, search:false},
	  {name:'title', index:'title', width:150, editable:true, sortable:false, search:false},
	  {name:'order', index:'order', width:80, editable:true, sortable:false, search:false},
	  {name:'categories', index:'categories', width:200, editable:false, sortable:false, search:false}
    ],
    pager: '#pager',
    rowNum:50,
	height:300,
    rowList:[25,50,100],
    sortname: 'order',
    sortorder: 'desc',
    viewrecords: true,
	autowidth: true,
	//altRows:true,
    caption: 'Images'
  }); 
  $("#list").jqGrid(
  	'navGrid',
	'#pager',
	{edit:true,add:false,del:true,search:false},//Options
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
<table id="list"></table> 
<div id="pager"></div>