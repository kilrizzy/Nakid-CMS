<?php
//-----------------------
//Common System Functions
//-----------------------
//DB items here should be moved to the corresponding model: http://codeigniter.com/user_guide/general/models.html
//VERIFY CORRECT CONFIG.PHP
function check_config_settings(){
	global $data;
	if(NAKID_READY === true){
		return true;	
	}else{
		$data['notes'][] = array("config.php setting 'NAKID_READY' is either not found or set to false!","error");
		$data['page'] = "error";
		return false;
	}
}
//VERIFY NAKID CMS IS OR IS NOT INSTALLED
function check_installation($required){
	$ci=& get_instance();
	$installed = false;
	$table_settings_exists = $ci->SystemModel->check_table_exists('settings');
	if($table_settings_exists){
		$installed = true;
	}
	//If install is required and CMS is not installed
	if($required && !$installed){
		redirect('install');	
	}
	//If on install page and CMS is already installed
	if(!$required && $installed){
		redirect(site_url());	
	}
}
//PROCESS LOGIN FORM
function login_process($username, $password){
	global $data;
	$ci=& get_instance();
	$user = $ci->SystemModel->get_user_by_username($username);
	if($user){
		$uid = $user->id;
		$pass = $user->password;
		$password = prep_password($password);
		if($pass == $password){
			//Set logged in
			$ci->session->set_userdata('uid',$uid);
		}else{
			$data['notes'][] = array("Invalid Password","error");
		}
	}else{
		$data['notes'][] = array("Username Not Found","error");
	}
}
//USER HAS PERMISSIONS
function permission($pname, $uid=0){
	$ci=& get_instance();
	if(intval($uid) <= 0){
		//Get uid based on current user
		$user = $ci->SystemModel->get_user($ci->session->userdata('uid'));
		if($user){
			$uid = $user->id;
		}
	}
	//Check if permission exists
	if(!empty($uid)){
		return $ci->SystemModel->check_user_permission($pname,$uid);
	}else{
		return false;
	}
}
//SEND EMAIL
function systememail($to,$subject,$message){
	global $data, $system;
	$ci=& get_instance();
	//Apply message to template
	$emaildata['content'] = $message;
	$emailtemplate = $ci->load->view('email/template', $emaildata, TRUE);  
	$ci=& get_instance();
	$ci->load->library('email');
	$ci->email->from($system['from_email'], $system['from_name']);
	$ci->email->to($to);
	$ci->email->subject($subject);
	$ci->email->message($emailtemplate);
	$ci->email->send();
}
//RESET USER PASSWORD
function reset_password($email){
	global $data;
	$ci=& get_instance();
	$user = $ci->SystemModel->get_user_by_email($email);
	if($user){
		$uid = $user->id;
		$username = $user->username;
		$password = get_random_password();
		$dbdata = array('password' => prep_password($password));
		$ci->SystemModel->update_user($uid,$dbdata);
		//Email User
		$message = array();
		$message[] = "Your password to your Nakid CMS installation has been reset. You can now log in with:";
		$message[] = "<strong>Username:</strong> ".$username;
		$message[] = "<strong>Password:</strong> ".$password;
		$message[] = "This password can be changed once you log in";
		$message = implode("<br/>\n",$message);
		systememail($email,"Nakid CMS login",$message);
	}else{
		$data['notes'][] = array("Account not found","error");
	}
}
//PREP PROCESS
function prep_password($password){
	$ci=& get_instance();
	return sha1($password.$ci->config->item('encryption_key'));
}
//BUILD MENU
function build_menu($user){
	$menu = array();
	//System
	//Menu needs to reflect permissions + more dynamic setup (db based)
	if($user){
		//SYSTEM
		$mct = count($menu);
		if(permission('manage_users') || permission('system_settings')){
			if(permission('system_settings')){
				$parentlink = "system/settings";
			}else{
				$parentlink = "system/users";
			}
			$menu[$mct] = array(
				'name' => 'system',
				'title' => 'System',
				'link' => $parentlink,
				'children' => array()
			);
			if(permission('system_settings')){
				//System Settings
				$menu[$mct]['children'][] = array(
					'name' => 'system_settings',
					'title' => 'System Settings',
					'link' => 'system/settings'
				);
			}
			if(permission('manage_users')){
				//Users
				$menu[$mct]['children'][] = array(
					'name' => 'users',
					'title' => 'Users',
					'link' => 'system/users'
				);
			}
		}
		//TOOLS
		$mct = count($menu);
		$menu[$mct] = array(
			'name' => 'tools',
			'title' => 'Tools',
			'link' => '#',
			'children' => array()
		);
		if(permission('cms_add') || permission('cms_edit') || permission('cms_delete')){
			$menu[$mct]['children'][] = array(
				'name' => 'content_editor',
				'title' => 'Content Editor',
				'link' => 'system/content'
			);
		}
		if(permission('gallery')){
			$menu[$mct]['children'][] = array(
				'name' => 'gallery',
				'title' => 'Image Gallery',
				'link' => 'system/galleries'
			);
		}
		//USER
		$mct = count($menu);
		$menu[$mct] = array(
			'name' => 'user',
			'title' => $user->username,
			'link' => 'system/profile',
			'children' => array()
		);
		$menu[$mct]['children'][] = array(
			'name' => 'edit_profile',
			'title' => 'Edit Profile',
			'link' => 'system/profile'
		);
		$menu[$mct]['children'][] = array(
			'name' => 'logout',
			'title' => 'Logout',
			'link' => 'system/logout'
		);
	}
	//HELP
	$mct = count($menu);
	$menu[$mct] = array(
		'name' => 'help',
		'title' => 'Help',
		'link' => THEME_HELP_WEBSITE,
		'children' => array()
	);
	if(THEME_HELP_DOCS != ""){
		$menu[$mct]['children'][] = array(
			'name' => 'documentation',
			'title' => 'Docs / Tutorials',
			'link' => THEME_HELP_DOCS
		);
	}
	if(THEME_HELP_FORUMS != ""){
		$menu[$mct]['children'][] = array(
			'name' => 'forum',
			'title' => 'Forum',
			'link' => THEME_HELP_FORUMS
		);
	}
	if(THEME_HELP_BUGS != ""){
		$menu[$mct]['children'][] = array(
			'name' => 'bugs',
			'title' => 'Report a Bug',
			'link' => THEME_HELP_BUGS
		);
	}
	if(THEME_HELP_WEBSITE != ""){
		$menu[$mct]['children'][] = array(
			'name' => 'website',
			'title' => THEME_HELP_WEBSITE_NAME,
			'link' => THEME_HELP_WEBSITE
		);
	}
	return $menu;
}
function licensed($v=true){
	//V
	if(md5(NAKID_KEY) == "5c574d1684e1c58c3c4ec65542b687c1" && $v){
		return true;
	}
	//Support web developers :)
	$l = base64_encode(str_replace(array('http://','https://','www.'), '', NAKID_WEBSITE));
	if(NAKID_KEY === $l){
		return true;
	}
	return false;
}
















//CHECK INSTALLATION
//GENERATE PASSWORD
if ( ! function_exists('get_random_password'))
{
    /**
     * Generate a random password. 
     * 
     * get_random_password() will return a random password with length 6-8 of lowercase letters only.
     *
     * @access    public
     * @param    $chars_min the minimum length of password (optional, default 6)
     * @param    $chars_max the maximum length of password (optional, default 8)
     * @param    $use_upper_case boolean use upper case for letters, means stronger password (optional, default false)
     * @param    $include_numbers boolean include numbers, means stronger password (optional, default false)
     * @param    $include_special_chars include special characters, means stronger password (optional, default false)
     *
     * @return    string containing a random password 
     */    
    function get_random_password($chars_min=6, $chars_max=8, $use_upper_case=false, $include_numbers=true, $include_special_chars=false)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
        if($include_special_chars) {
            $selection .= "!@\"#$%&[]{}?|";
        }
                                
        $password = "";
        for($i=0; $i<$length; $i++) {
            $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];            
            $password .=  $current_letter;
        }                
        
        return $password;
    }
} 
//Get options from segment string
	function get_segment_options($opts, $option_string){
		$option_strings = explode("_",$option_string);
		foreach($option_strings as $ostring){
			$oparts = explode("-",$ostring);
			//See if they are allowed to change this value
			if(isset($opts[$oparts[0]]) || !$opts){
				$opts[$oparts[0]] = $oparts[1];
			}
		}
		return $opts;
	}