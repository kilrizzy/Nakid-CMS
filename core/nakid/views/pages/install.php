<h1>Install Nakid CMS</h1>
<?php
if($installed){
?>
<p>Nakid CMS has been installed! <a href="<?php echo site_url(); ?>">Click here to login</a></p>
<?php
}else{
?>
<h2>Nakid CMS is about to install tables on:</h2>
<p>
<strong>Hostname:</strong> <?php echo $dbhostname; ?><br/>
<strong>Database:</strong> <?php echo $dbdatabase; ?><br/>
<strong>Database Username:</strong> <?php echo $dbusername; ?><br/>
<strong>Database Password:</strong> ********<br/>
<em>using the table prefix of ("<?php echo $dbprefix; ?>")</em></p>
<p>If the above is incorrect, please modify &quot;config.php&quot;, otherwise fill out the form below to create an administrative account and install Nakid CMS</p>
<h2>Administrator Access</h2>
<?php 
$form_attributes = array('class' => 'niceform1', 'id' => 'form_install');
echo form_open('install',$form_attributes); 
?>
<ul>
    <li>
        <label for="username">Desired Username:</label>
        <?php 
		$field = array(
        	'name'        => 'username',
            'id'          => 'username',
			'class'       => 'textfield',
            'value'       => $postusername,
            'maxlength'   => '25'
        );
		echo form_input($field);
		?>
        <small>An administrative username to administer the cms</small>
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
        <small>Select a password to administer the cms</small>
    </li>
    <li>
        <label for="password_confirm">Confirm Password:</label>
        <?php 
		$field = array(
        	'name'        => 'password_confirm',
            'id'          => 'password_confirm',
			'class'       => 'textfield',
            'value'       => '',
            'maxlength'   => '50'
        );
		echo form_password($field);
		?>
    </li>
    <li>
        <label for="email">Email Address:</label>
        <?php 
		$field = array(
        	'name'        => 'email',
            'id'          => 'email',
			'class'       => 'textfield',
            'value'       => $postemail,
            'maxlength'   => '255'
        );
		echo form_input($field);
		?>
        <small>Used if you forget your password</small>
    </li>
    <li class="submit">
    	<?php echo form_hidden('action', 'install'); ?>
    	<?php echo form_submit('submitbt', 'Install Nakid'); ?>
    </li>
</ul>
<?php echo form_close(); ?>
<?php } ?>