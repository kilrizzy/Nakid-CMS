<h1>Login</h1>
<?php 
$form_attributes = array('class' => 'niceform1', 'id' => 'form_login');
echo form_open(current_url(),$form_attributes); 
?>
<ul>
    <li>
        <label for="username">Username:</label>
        <?php 
		$field = array(
        	'name'        => 'username',
            'id'          => 'username',
			'class'       => 'textfield',
            'value'       => '',
            'maxlength'   => '25'
        );
		echo form_input($field);
		?>
    </li>
    <li>
        <label for="password">Password:</label>
        <?php 
		$field = array(
        	'name'        => 'password',
            'id'          => 'password',
			'class'       => 'textfield',
            'value'       => '',
            'maxlength'   => '50'
        );
		echo form_password($field);
		?>
    </li>
    <li class="submit">
    	<?php echo form_hidden('action', 'login'); ?>
    	<?php echo form_submit('submitbt', 'Login'); ?>
    </li>
</ul>
<?php echo form_close(); ?>
<p>Forgot your password? <a href="<?php echo(site_url("system/forgot_password")); ?>">Reset my password!</a></p>