<h1>Edit <?php echo($block['title']); ?></h1>
<?php 
$form_attributes = array('class' => 'niceform2', 'id' => 'form_content_edit');
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
            'value'       => $block['title'],
            'maxlength'   => '50'
        );
		echo form_input($field);
		?>
    <small>The title will not be displayed on your page. This is for administrative use only.</small> </li>
  <li>
    <label for="content">Content:</label>
    <?php 
		$field = array(
        	'name'        => 'content',
            'id'          => 'content',
			'class'       => 'editor',
            'value'       => $block['content']
        );
		if(!$block['data']['show_editor']){
			$field['class'] = '';
		}
		echo form_textarea($field);
		?>
  </li>
</ul>
<h3 class="collapse">Additional Options</h3>
<ul class="collapsible" style="display:none">
<li>
    <label for="keyword">Keyword:</label>
    <?php 
		$field = array(
        	'name'        => 'keyword',
            'id'          => 'keyword',
			'class'       => 'textfield',
            'value'       => $block['keyword'],
            'maxlength'   => '50'
        );
		echo form_input($field);
		?>
    <small>The key is an optional field you can use to refer to when calling this content from your website. This would be in place of calling the content from it's id</small> </li>
<li class="checkbox">
    <label for="show_editor"><?php echo form_checkbox('show_editor', '1', $block['data']['show_editor']); ?> Always show editor for this block (Uncheck and save to only view HTML)</label>
  </li>
</ul>
<ul><li class="submit"> <?php echo form_hidden('action', 'edit_content'); ?> <?php echo form_submit('submitbt', 'Save'); ?> </li></ul>
<?php echo form_close(); ?>
<hr/>
<h2>Revision History</h2>
<script type="text/javascript">
//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc'] 
$(function(){ 
  $("#list").jqGrid({
    url:'<?php echo (site_url("grid/content_history/".$block['id'])); ?>',
	editurl: '<?php echo (site_url("grid/content_history/".$block['id'])); ?>',
    datatype: 'xml',
    mtype: 'POST',
    colNames:['Preview','Date', 'Author','Revert'],
    colModel :[ 
      {name:'preview', index:'preview', width:60, editable:false, sortable:false, search:false}, 
      {name:'date', index:'date', width:40, editable:false, sortable:false, search:false}, 
      {name:'author', index:'author', width:100, editable:false, sortable:false, search:false}, 
      {name:'revert', index:'revert', width:60, editable:false, sortable:false, search:false}, 
    ],
    pager: '#pager',
    rowNum:50,
    rowList:[25,50,100],
    sortname: 'date',
    sortorder: 'desc',
    viewrecords: true,
	autowidth: true,
	//altRows:true,
    caption: 'History'
  }); 
  $("#list").jqGrid(
  	'navGrid',
	'#pager',
	{edit:false,add:false,del:false,search:false},//Options
	{reloadAfterSubmit:true, closeAfterEdit:true},//Edit Options
	{reloadAfterSubmit:true, closeAfterAdd:true},//Add Options
	{reloadAfterSubmit:true}//Delete Options
  ); 
}); 
</script>
<table id="list">
</table>
<div id="pager"></div>
