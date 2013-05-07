<h1><?php echo($user->username); ?>'s Profile</h1>
<?php 
$form_attributes = array('class' => 'niceform1', 'id' => 'form_profile');
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
            'value'       => $user->username,
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
            'value'       => '********',
            'maxlength'   => '50'
        );
		echo form_password($field);
		?>
        <small>Leave password field alone to keep current</small>
    </li>
    <li>
        <label for="email">Email Address:</label>
        <?php 
		$field = array(
        	'name'        => 'email',
            'id'          => 'email',
			'class'       => 'textfield',
            'value'       => $user->email,
            'maxlength'   => '25'
        );
		echo form_input($field);
		?>
    </li>
    <li>
        <label for="fname">First Name:</label>
        <?php 
		$field = array(
        	'name'        => 'fname',
            'id'          => 'fname',
			'class'       => 'textfield',
            'value'       => $user->fname,
            'maxlength'   => '25'
        );
		echo form_input($field);
		?>
    </li>
    <li>
        <label for="lname">Last Name:</label>
        <?php 
		$field = array(
        	'name'        => 'lname',
            'id'          => 'lname',
			'class'       => 'textfield',
            'value'       => $user->lname,
            'maxlength'   => '25'
        );
		echo form_input($field);
		?>
    </li>
    <li class="submit">
    	<?php echo form_hidden('action', 'edit_profile'); ?>
    	<?php echo form_submit('submitbt', 'Save'); ?>
    </li>
</ul>
<?php echo form_close(); ?>