<?php if($sent){ ?>

<script type="text/javascript">
parent.$.fn.colorbox.close();
</script>
<h1>Block Deleted</h1>
<p><strong><a href="javascript:parent.$.fn.colorbox.close();">Click here to return.</a></strong></p>

<?php }else{ ?>

<h1>Are you sure you want to delete this block ("<?php echo($block_title); ?>")?</h1>
<?php $form_attributes = array('class' => 'niceform1', 'id' => 'form_delete_block'); ?>
<ul>
<?php echo form_open(current_url(),$form_attributes); ?>
<?php echo form_hidden('action', 'delete_block'); ?>
<?php echo form_submit('submitbt', 'DELETE'); ?>
</ul>
<?php echo form_close(); ?>

<?php } ?>