<?php if($sent){ ?>


<script type="text/javascript">
parent.$.fn.colorbox.close();
</script>
<h1>Permissions have been set!</h1>
<p><strong><a href="javascript:parent.$.fn.colorbox.close();">Click here to return.</a></strong></p>


<?php }else{ ?>


<h1>Set Permissions for <?php echo($edituser->username); ?></h1>
<?php 
$form_attributes = array('class' => 'niceform1', 'id' => 'form_permissions');
echo form_open(current_url(),$form_attributes); 
?>
<ul>
    <li>
		<?php
        foreach($permissions as $permissions_cat){
		?>
        <h3><?php echo($permissions_cat[0]); ?></h3>
        <ul>
			<?php
            foreach($permissions_cat[1] as $permission){
            ?>
            <li>
                <label><?php echo($permission[1]); ?></label>
                <?php 
                echo form_checkbox('permissions[]', $permission[0], $permission[3]);
                ?>
            </li>
            <?php 
            } 
            ?>
      	</ul>
		<?php 
		} 
		?>
    </li>
    <li class="submit">
    	<?php echo form_hidden('permissions_changing', $permissions_changing); ?>
    	<?php echo form_hidden('edit_user', $edituser->id); ?>
    	<?php echo form_hidden('action', 'edit_permissions'); ?>
    	<?php echo form_submit('submitbt', 'Save'); ?>
    </li>
</ul>
<?php echo form_close(); ?>


<?php } ?>