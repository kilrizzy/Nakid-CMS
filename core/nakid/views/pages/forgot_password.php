<h1>Password Reset</h1>
<?php 
$form_attributes = array('class' => 'niceform1', 'id' => 'form_forgot_password');
echo form_open(current_url(),$form_attributes); 
?>
<ul>
    <li>
        <label for="username">Email Address:</label>
        <?php 
		$field = array(
        	'name'        => 'email',
            'id'          => 'email',
			'class'       => 'textfield',
            'value'       => ''
        );
		echo form_input($field);
		?>
    </li>
    <li class="submit">
    	<?php echo form_hidden('action', 'forgot_password'); ?>
    	<?php echo form_submit('submitbt', 'Reset Password'); ?>
    </li>
</ul>
<?php echo form_close(); ?>
<p>Remember your password? <a href="<?php echo(site_url("system")); ?>">Login!</a></p>