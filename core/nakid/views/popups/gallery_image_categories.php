<?php if($sent){ ?>


<script type="text/javascript">
parent.$.fn.colorbox.close();
</script>
<h1>Categories have been set!</h1>
<p><strong><a href="javascript:parent.$.fn.colorbox.close();">Click here to return.</a></strong></p>


<?php }else{ ?>


<h1>Image Categories</h1>
<?php 
$form_attributes = array('class' => 'niceform1', 'id' => 'form_permissions_gallery_image_categories');
echo form_open(current_url(),$form_attributes); 
?>
<ul>
	<li>
	<ul>
    <?php foreach($categories as $category){ ?>
    <li>
    	<label><?php echo($category[1]); ?></label>
        <?php echo form_checkbox('categories[]', $category[0], $category[2]); ?>
    </li>
    <?php } ?>
    </ul>
    </li>
    <li class="submit">
    	<?php echo form_hidden('categories_changing', $categories_changing); ?>
    	<?php echo form_hidden('edit_image', $editimage->id); ?>
    	<?php echo form_hidden('action', 'edit_gallery_image_categories'); ?>
    	<?php echo form_submit('submitbt', 'Save'); ?>
    </li>
</ul>
<?php echo form_close(); ?>


<?php } ?>