<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class System extends CI_Controller {
	public function __construct(){
		parent::__construct();
		global $data, $system, $user,$upload_directory;
		//disable once done dev
		//$this->output->enable_profiler(TRUE);
		$data = array();
		$data['page'] = "home";
		$data['notes'] = array();
		$finalpage = false;
		//CHECK CONFIG.PHP SETUP
		check_config_settings(); //Verify config.php setup
		//CHECK INSTALLATION
		check_installation(true); //Verify Nakid CMS is installed
		//GET SYSTEM INFO
		$system = $this->SystemModel->get_settings_array();//Get array of system settings
		//CHECK LOGIN
		$user = $this->SystemModel->get_user($this->session->userdata('uid'));//check_login($this->session->userdata('uid'));
		if(!$user){
			$nonauth = array("login","forgot_password");
			if(!in_array($this->uri->segment(2),$nonauth)){
				redirect('system/login');
			}
		}else{
			//KCFINDER OPTIONS (PUT THIS ELSEWHERE)
			//Find Nakid install path
			$paths = explode($_SERVER["SERVER_NAME"],base_url());
			$nakid_install = $paths[1];
			//Get Upload Directory
			$kc_directory = $nakid_install."/uploads";
			//$upload_directory = NAKID_ROOT."/uploads";
			$upload_directory = "uploads";
			chmod($upload_directory, 0775);
			//Set Session
			session_start();
			$_SESSION['KCFINDER'] = array();
			$_SESSION['KCFINDER']['disabled'] = false;
			//$_SESSION['KCFINDER']['uploadURL'] = $upload_directory;
			$_SESSION['KCFINDER']['uploadURL'] = $kc_directory;
		}
		//BUILD MENU
		$data['menu'] = build_menu($user);
		$dbdata = array(
		   'order' => 55
		);
		//
		if(!licensed()){
			$data['notes'][] = array(base64_decode('PGRpdiBpZD0iaV9hbV9hX2Jyb2tlX2RldmVsb3BlciI+PHA+PGEgaHJlZj0iaHR0cDovL25ha2lkLm9yZy9kb25hdGUiIHRhcmdldD0iX2JsYW5rIj48c3Ryb25nPlRoaXMgdmVyc2lvbiBvZiBkb21haW4gaXMgY3VycmVudGx5IHVubGljZW5zZWQuPC9zdHJvbmc+IC0gSG93IG11Y2ggaXMgdGhpcyBwcm9ncmFtIHdvcnRoIHRvIHlvdT8gSWYgeW91IGxpa2UgTmFraWQsIHBsZWFzZSBkb25hdGUgdG8gc3VwcG9ydCBmdXR1cmUgdXBkYXRlcy4gQWZ0ZXIgZG9uYXRpbmcgdGhlIGFtb3VudCBvZiB5b3VyIGNob29zaW5nIHlvdSB3aWxsIHJlY2VpdmUgYSBkb21haW4gbGljZW5zZSBrZXkgc28geW91IGRvbid0IGhhdmUgdG8gc2VlIHRoaXMgbmFnIHNjcmVlbiBhbnltb3JlITwvYT48L3A+PC9kaXY+'),'alert');
		}
	}
	function index(){
		global $data, $user;
		//Load from content editor
		$block = $this->SystemModel->get_content_block(1);
		$data['content_body'] = $block->content; 
		$this->load->view('template',$data);
	}
	function coming_soon(){
		global $data, $user;
		$data['page'] = "system/coming_soon";
		$this->load->view('template',$data);
	}
	function users(){
		global $data, $user;
		if(permission('manage_users')){
			$data['page'] = "system/users";
			$this->load->view('template',$data);
		}else{
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}
	}
	function permissions(){
		global $data, $user;
		if(permission('manage_users')){
			$data['page'] = "permissions";
			$data['sent'] = false;
			if($this->input->post('action') && $this->input->post('action') == "edit_permissions"){
				//ONLY CHANGE PERMISSIONS THAT CURRENT USER HAS
				//---XXX
				//Check if edituser exists
				$edituser = $this->input->post('edit_user');
				$edituserexists = $this->SystemModel->check_user_exists($edituser);
				if (!$edituserexists){
					redirect('system/users');
				}
				//Remove current permissions we are editing
				$permissions_changing = explode(",",$this->input->post('permissions_changing'));
				foreach($permissions_changing as $permission){
					//Remove all items
					$this->SystemModel->remove_user_permission($edituser,$permission);
					//Add only if checked (post found)
					if(is_array($this->input->post('permissions')) && in_array($permission,$this->input->post('permissions'))){
						$this->SystemModel->add_user_permission($edituser,$permission);
					}
				}
				//
				$data['sent'] = true;
			}else{
				//PULL EDITING USER INFORMATION
				$edituserid = $this->uri->segment(3);
				$edituser = $this->SystemModel->get_user($edituserid);			
				if (!$edituser){
					//user not in db
					redirect('system/users');
				}
				//MAKE SURE USER IS ALLOWED TO BE HERE
				//USER CAN ONLY ADD WHAT THEY ARE ALLOWED TO DO
				$permissions_changing = array();
				$permissions = array();
				$permission_categories = $this->SystemModel->get_permission_categories();
				foreach ($permission_categories as $p_cat_row){
					$catpermissions = array();
					$get_permission_values = $this->SystemModel->get_permission_values($p_cat_row->id);
					foreach ($get_permission_values as $p_val_row){
						//See if currently checked
						$checked = $this->SystemModel->get_permission_user($edituser->id,$p_val_row->id);
						$catpermissions[] = array($p_val_row->id,$p_val_row->description,$p_val_row->key,$checked);
						$permissions_changing[] = $p_val_row->id;
					}
					if(count($catpermissions) > 0){
						$permissions[] = array($p_cat_row->name,$catpermissions);
					}
				}
				//
				$data['permissions_changing'] = implode(",",$permissions_changing);
				$data['permissions'] = $permissions;
				$data['edituser'] = $edituser;
			}
			$this->load->view('template_popup',$data);
		}else{
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}
	}
	function settings(){
		global $data, $user;
		if(permission("system_settings")){
			$data['page'] = "system/settings";
			//$data['website_path'] = 
			$this->load->view('template',$data);
		}else{
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}
	}

	function profile(){
		global $data, $user;
		$error = false;
		//Check if editing
		if($this->input->post('action') && $this->input->post('action') == "edit_profile"){
			//Check if username exists
			$usernameexists = $this->SystemModel->check_username_exists($this->input->post('username'),$user->id);
			if($usernameexists){
				$error = true;
				$data['notes'][] = array("Username already in use","error");
			}
			//Check if email exists
			$emailexists = $this->SystemModel->check_email_exists($this->input->post('email'),$user->id);
			if ($emailexists){
				$error = true;
				$data['notes'][] = array("Email already in use","error");
			}
			if(!$error){
				//Update info
				$dbdata = array(
				   'username' => $this->input->post('username'),
				   'email' => $this->input->post('email'),
				   'fname' => $this->input->post('fname'),
				   'lname' => $this->input->post('lname')
				);
				$postpw = $this->input->post('password');
				if(!strstr($postpw,"*") && !empty($postpw)){
					$dbdata['password'] = prep_password($postpw);
				}
				$this->SystemModel->update_user($user->id,$dbdata);
				//Refresh page
				redirect($this->uri->uri_string());
			}
		}
		$data['page'] = "system/profile";
		$data['user'] = $user;
		$this->load->view('template',$data);
	}
	/*---------------------------------------
	CONTENT
	---------------------------------------*/
	function content(){
		global $data, $user;
		$data['permission_cms_add'] = "true";
		if(!permission('cms_add')){
			$data['permission_cms_add'] = "false";
		}
		$data['page'] = "system/content";
		$this->load->view('template',$data);
	}

	function content_edit(){
		global $data, $user;
		//
		$editblock = array();
		$editblock['id'] = $this->uri->segment(3);
		$editblock['content'] = "";
		$editblock['title'] = "";
		$editblock['version'] = 0;
		$editblock['keyword'] = "";
		//Permissions
		$editable = true;
		//Default options
		$block_options['show_editor'] = true;
		$block_options['editable'] = true;
		$block_options['deletable'] = true;
		//Make sure this block is editable
		$block = $this->SystemModel->get_content_block($editblock['id']);
		if(!$block->editable){
			$editable = false;
		}
		//Make sure user has permissions to edit
		if(!permission('cms_edit')){
			$editable = false;
		}
		//Check if posting info
		if($this->input->post('action') && $this->input->post('action') == "edit_content" && $editable){
			$newcontent = $this->input->post('content');
			//OLD DATA
			$oldblock = $this->SystemModel->get_content_block($editblock['id']);
			$vid =  $oldblock->vid;
			$new_options = unserialize($oldblock->data);
			//OPTIONS
			if($this->input->post('show_editor')){
				$new_options['show_editor'] = true;
			}else{
				$new_options['show_editor'] = false;
			}
			//ADD BLOCK VERSION
			if($oldblock->content != $newcontent){
				$dbdata = array(
					'content' => $newcontent,
					'author' => $user->id,
					'type' => "content",
					'block' => $editblock['id']
				);
				$vid = $this->SystemModel->add_content_version($dbdata);
			}
			//UPDATE BLOCK
			$dbdata = array(
				'title' => $this->input->post('title'),
				'version' => $vid,
				'data' => serialize($new_options)
			);
			$this->SystemModel->update_content_block($editblock['id'],$dbdata);
			//UPDATE KEYWORD
			$this->SystemModel->update_keyword("content",$editblock['id'],$this->input->post('keyword'));
			//Notify saved + link to other pages
			$current_datetime = date("m/d/Y g:ia");
			$data['notes'][] = array("Content Saved ".$current_datetime.". <a href='".site_url("system/content")."'>Click here to edit another block</a>","message");
		}
		//Get block info
		$block = $this->SystemModel->get_content_block($editblock['id']);
		if(!$block){
			redirect('system/content');
		}else{
			$editblock['title'] = $block->title;
			$editblock['keyword'] = $block->keyword;
			$editblock['version'] = $block->vid;
			$editblock['data'] = unserialize($block->data);
			$editblock['content'] = $block->content;
		}
		//Send data to template
		if(is_array($editblock['data'])){
			$editblock['data'] = array_merge($block_options,$editblock['data']);
		}else{
			$editblock['data'] = $block_options;
		}
		$data['block'] = $editblock;
		$data['page'] = "system/content_edit";
		if(!$editable){
			$data['page'] = "system/access_denied";
		}
		$this->load->view('template',$data);
	}
	
	function content_delete(){
		global $data, $user;
		//Permissions
		$deletable = true;
		//
		$data['page'] = "content_delete";
		$data['sent'] = false;
		$bid = $this->uri->segment(3);
		$block = $this->SystemModel->get_content_block($bid);
		//Make sure this block is editable
		if(!$block->deletable){
			$deletable = false;
		}
		//Make sure user has permissions to edit
		if(!permission('cms_delete')){
			$deletable = false;
		}
		//
		if($block->deletable == 0){
			$deletable = false;
		}
		if($this->input->post('action') && $this->input->post('action') == "delete_block" && $deletable){
			//If block is deletable and user can delete
			$this->SystemModel->delete_content_block($bid);
			$data['sent'] = true;
		}else{
			$data['block_title'] = $block->title;
		}
		if(!$deletable){
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}else{
			$this->load->view('template_popup',$data);
		}
	}
	
	function content_preview(){
		global $data, $user;
		$data['page'] = "content_preview";
		$block = $this->SystemModel->get_content_block($this->uri->segment(3),$this->uri->segment(4));
		$data['content'] = $block->content;
		$this->load->view('template_popup',$data);
	}
	
	function content_revert(){
		global $data, $user;
		if(permission('cms_edit')){
			$bid = $this->uri->segment(3);
			$vid = $this->uri->segment(4);
			//Get content
			$block = $this->SystemModel->get_content_block($bid,$vid);
			//Add this data as a new version and update block
			$dbdata = array(
				'content' => $block->content,
				'author' => $user->id,
				'block' => $bid
			);
			$newvid = $this->SystemModel->add_content_version($dbdata);
			//Get version id
			$newvid = $this->db->insert_id();
			//Update title and version
			$dbdata = array(
				'version' => $vid
			);
			$this->SystemModel->update_content_block($bid,$dbdata);
			//Notify saved + link to other pages
			$current_datetime = date("m/d/Y g:ia");
			redirect("system/content_edit/".$bid);
		}else{
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}
	}
	/*---------------------------------------
	IMAGE GALLERIES
	---------------------------------------*/
	function galleries(){
		global $data, $user;
		$data['permission_gallery'] = "true";
		if(!permission('gallery')){
			$data['permission_gallery'] = "false";
		}
		$data['page'] = "system/galleries";
		$this->load->view('template',$data);
	}
	function gallery_edit(){
		global $data, $user,$upload_directory;
		$editgallery = array();
		$editgallery['id'] = $this->uri->segment(3);
		$editgallery['content'] = "";
		$editgallery['title'] = "";
		$editgallery['description'] = "";
		$editgallery['keyword'] = "";
		//
		//Default options
		$gallery_options = array();
		$gallery_options['image_titles'] = "none";
		$gallery_options['thumb_width'] = 150;
		$gallery_options['thumb_height'] = 100;
		$gallery_options['thumb_aspect'] = 'crop';
		$gallery_options['image_width'] = 800;
		$gallery_options['image_height'] = 600;
		$gallery_options['image_aspect'] = 'maintain';
		$gallery_options['category_display'] = "hide";
		$gallery_options['category_empty'] = "hide";
		$gallery_options['category_front'] = "all";
		//
		$gallery = $this->SystemModel->get_gallery($editgallery['id']);
		/*
		//Create a galleries directory here if one does not exist
		$galleries_folder = $upload_directory."/gallery";
		if(!is_dir($galleries_folder)){
			mkdir($galleries_folder);
			chmod($galleries_folder, 0775);
		}
		//Create a name for this folder based on title
		$foldername = preg_replace("/[^A-Za-z0-9]/", "", $gallery->title);
		//Create a gallery directory here if one does not exist
		$gallery_folder = $galleries_folder."/".$foldername;
		if(!is_dir($gallery_folder)){
			mkdir($gallery_folder);
			chmod($gallery_folder, 0775);
		}
		*/
		//Check if posting info
		if($this->input->post('action') && $this->input->post('action') == "resize_images"){
			$this->gallery_thumbsize();
			$current_datetime = date("m/d/Y g:ia");
			$data['notes'][] = array("Images Resized ".$current_datetime.".","message");
		}
		if($this->input->post('action') && $this->input->post('action') == "edit_gallery"){
			$newcontent = $this->input->post('content');
			//OLD DATA
			$oldgallery = $this->SystemModel->get_gallery($editgallery['id']);
			$new_options = unserialize($oldgallery->data);
			//OPTIONS
			$new_options['image_titles'] = $this->input->post('image_titles');
			$new_options['thumb_width'] = $this->input->post('thumb_width');
			$new_options['thumb_height'] = $this->input->post('thumb_height');
			$new_options['thumb_aspect'] = $this->input->post('thumb_aspect');
			$new_options['image_width'] = $this->input->post('image_width');
			$new_options['image_height'] = $this->input->post('image_height');
			$new_options['image_aspect'] = $this->input->post('image_aspect');
			$new_options['category_display'] = $this->input->post('category_display');
			$new_options['category_empty'] = $this->input->post('category_empty');
			$new_options['category_front'] = $this->input->post('category_front');
			//UPDATE
			$dbdata = array(
				'title' => $this->input->post('title'),
				'description' => $this->input->post('description'),
				'data' => serialize($new_options)
			);
			$this->SystemModel->update_gallery($editgallery['id'],$dbdata);
			//UPDATE KEYWORD
			$this->SystemModel->update_keyword("gallery",$editgallery['id'],$this->input->post('keyword'));
			//UPLOAD PHOTOS
			if($this->input->post('images')){
				$gallery_images = preg_replace('/\r\n|\r/', "\n", $this->input->post('images'));
				$gallery_image_array = explode("\n",$gallery_images);
				$ready_images = array();
				foreach($gallery_image_array as $image){
					$image = urldecode($image);
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$image) && !empty($image)){
						//Strip images so it is linked as "/files/images... from upload folder"
						$image_array = explode("/uploads/",$image);
						$ready_images[] = $image_array[1];
					}
				}
				foreach($ready_images as $ready_image){
					$this->SystemModel->add_gallery_image($ready_image,$editgallery['id']);
				}
				//XXX Make array of ready image id's and pass to this function
				//Generate thumbs
				$this->gallery_thumbsize();
			}
			//Notify saved + link to other pages
			$current_datetime = date("m/d/Y g:ia");
			$data['notes'][] = array("Gallery Saved ".$current_datetime.". <a href='".site_url("system/galleries")."'>Click here to edit another gallery</a>","message");
		}
		//Get gallery info
		$gallery = $this->SystemModel->get_gallery($editgallery['id']);
		if(!$gallery){
			redirect('system/galleries');
		}else{
			$editgallery['title'] = $gallery->title;
			$editgallery['keyword'] = $gallery->keyword;
			$editgallery['data'] = unserialize($gallery->data);
			$editgallery['description'] = $gallery->description;
		}
		//Send data to template
		if(is_array($editgallery['data'])){
			$editgallery['data'] = array_merge($gallery_options,$editgallery['data']);
		}else{
			$editgallery['data'] = $gallery_options;
		}
		$data['gallery'] = $editgallery;
		$data['page'] = "system/gallery_edit";
		$this->load->view('template',$data);
	}
	function gallery_image_categories(){
		global $data, $user;
		if(permission('gallery')){
			$data['page'] = "gallery_image_categories";
			$data['sent'] = false;
			$imageid = $this->uri->segment(3);
			$editimage = $this->SystemModel->get_gallery_image($imageid);	
			if($this->input->post('action') && $this->input->post('action') == "edit_gallery_image_categories"){
				$categories_changing = explode(",",$this->input->post('categories_changing'));
				//Remove current categories we are editing
				foreach($categories_changing as $category){
					//Remove all items
					$this->SystemModel->remove_gallery_image_category($editimage->gallery,$imageid,$category);
					//Add only if checked
					if(is_array($this->input->post('categories')) && in_array($category,$this->input->post('categories'))){
						$this->SystemModel->add_gallery_image_category($editimage->gallery,$imageid,$category);
					}
				}
				$data['sent'] = true;
			}else{
				$categories = array();
				$categories_changing = array();
				$all_categories = $this->SystemModel->get_gallery_categories($editimage->gallery);
				$image_categories = $this->SystemModel->get_gallery_image_categories($editimage->gallery,$editimage->id);
				foreach ($all_categories as $category){
					//See if currently checked
					$checked = false;
					foreach($image_categories as $image_category){
						if($image_category->id == $category->id){
							$checked = true;
						}
					}
					$categories_changing[] = $category->id;
					$categories[] = array($category->id,$category->title,$checked);
				}
				$data['categories'] = $categories;
				$data['categories_changing'] = implode(",",$categories_changing);
				$data['editimage'] = $editimage;
			}
			$this->load->view('template_popup',$data);
		}else{
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}
	}
	function gallery_delete(){
		global $data, $user;
		$data['page'] = "gallery_delete";
		$data['sent'] = false;
		$gid = $this->uri->segment(3);
		$gallery = $this->SystemModel->get_gallery($gid);
		//Make sure user has permissions to edit
		$deletable = true;
		if(!permission('gallery')){
			$deletable = false;
		}
		if($this->input->post('action') && $this->input->post('action') == "delete" && $deletable){
			//If block is deletable and user can delete
			$this->SystemModel->delete_gallery($gid);
			$data['sent'] = true;
		}else{
			$data['title'] = $gallery->title;
		}
		if(!$deletable){
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}else{
			$this->load->view('template_popup',$data);
		}
	}
	function gallery_thumbsize(){
		//THERE NEEDS TO BE A BUTTON TO CALL THIS, ALSO AN OPTION TO PASS AN ARRAY OF IMAGE ID'S INSTEAD OF GRABBING THEM ALL EVERY TIME
		global $data, $user,$upload_directory;
		$thumbdir = $upload_directory."/"."thumbs";
		$largedir = $upload_directory."/"."large";
		if(!is_dir($thumbdir)){
			mkdir($thumbdir);
			chmod($thumbdir, 0775);
		}
		if(!is_dir($largedir)){
			mkdir($largedir);
			chmod($largedir, 0775);
		}
		$this->load->library('image_lib');
		$config = array();
		$config['image_library'] = 'gd2';
		$config['create_thumb'] = FALSE;
		//$config['maintain_ratio'] = TRUE;
		//$config['width'] = 75;
		//$config['height'] = 50;
		//resize each
		//$this->load->library('image_lib', $config);
		$images = $this->SystemModel->get_gallery_images();
		$this->image_lib->resize();
		foreach($images as $image){
			//GET GALLERY OPTIONS
			//print($image->gallery);
			$gallery = $this->SystemModel->get_gallery($image->gallery);
			//print_r($gallery);
			if($gallery->data){
				$gallerydata = unserialize($gallery->data);
			}
			//
			$filename_arr = explode("/",$image->file);
			$filename = end($filename_arr);
			$sourceimg = $upload_directory."/".$image->file;
			$config['source_image'] = $sourceimg;
			$imagedimensions = getimagesize($config['source_image']);
			//THUMBS
			$config['width'] = $gallerydata['thumb_width'];
			$config['height'] = $gallerydata['thumb_height'];
			if($gallerydata['thumb_aspect'] == "crop"){
				//Both sizes need to be largest if cropping
				if($config['width'] > $config['height']){
					//$config['height'] = $config['width'];
					if($imagedimensions[0] > $imagedimensions[1]){
						$config['height'] = $config['width'];
					}else{
						$config['height'] = $config['width'];
						$config['master_dim'] = "width";
					}
				}
				if($config['height'] > $config['width']){
					if($imagedimensions[0] > $imagedimensions[1]){
						$config['width'] = $config['height'];
						$config['master_dim'] = "height";
					}else{
						$config['width'] = $config['height'];
						
					}
				}
			}
			$config['new_image'] = $thumbdir."/".$filename;
			$config['maintain_ratio'] = true;
			if($gallerydata['thumb_aspect'] == "ignore"){
				$config['maintain_ratio'] = false;
			}
			$this->image_lib->clear();
			$this->image_lib->initialize($config); 
			if ( !$this->image_lib->resize()){
				// an error occured
			}
			//crop to size if needed
			if($gallerydata['thumb_aspect'] == "crop"){
				$this->image_lib->clear();
				$config['source_image'] = $config['new_image'];
				//Crop from center
				$sizeddimensions = getimagesize($config['new_image']);
				$config['x_axis'] = ($sizeddimensions[0]-$gallerydata['thumb_width'])/2;
				$config['y_axis'] = ($sizeddimensions[1]-$gallerydata['thumb_height'])/2;
				//
				$config['width'] = $gallerydata['thumb_width'];
				$config['height'] = $gallerydata['thumb_height'];
				$config['maintain_ratio'] = false;
				$this->image_lib->initialize($config); 
				if (!$this->image_lib->crop()){
   					 //echo $this->image_lib->display_errors();
				}
			}
			//LARGE
			$config['source_image'] = $sourceimg;
			$config['width'] = $gallerydata['image_width'];
			$config['height'] = $gallerydata['image_height'];
			if($gallerydata['image_aspect'] == "crop"){
				//Both sizes need to be largest if cropping
				if($config['width'] > $config['height']){
					//$config['height'] = $config['width'];
					if($imagedimensions[0] > $imagedimensions[1]){
						$config['height'] = $config['width'];
					}else{
						$config['height'] = $config['width'];
						$config['master_dim'] = "width";
					}
				}
				if($config['height'] > $config['width']){
					if($imagedimensions[0] > $imagedimensions[1]){
						$config['width'] = $config['height'];
						$config['master_dim'] = "height";
					}else{
						$config['width'] = $config['height'];
						
					}
				}
			}
			$config['new_image'] = $largedir."/".$filename;
			//$config['maintain_ratio'] = (bool) $gallerydata['image_aspect'];
			$config['maintain_ratio'] = true;
			if($gallerydata['image_aspect'] == "ignore"){
				$config['maintain_ratio'] = false;
			}
			$this->image_lib->clear();
			$this->image_lib->initialize($config); 
			if ( ! $this->image_lib->resize()){
				// an error occured
			}
			//crop to size if needed
			if($gallerydata['image_aspect'] == "crop"){
				$this->image_lib->clear();
				$config['source_image'] = $config['new_image'];
				//Crop from center
				$sizeddimensions = getimagesize($config['new_image']);
				$config['x_axis'] = ($sizeddimensions[0]-$gallerydata['image_width'])/2;
				$config['y_axis'] = ($sizeddimensions[1]-$gallerydata['image_height'])/2;
				//
				$config['width'] = $gallerydata['image_width'];
				$config['height'] = $gallerydata['image_height'];
				$config['maintain_ratio'] = false;
				$this->image_lib->initialize($config); 
				if (!$this->image_lib->crop()){
   					 //echo $this->image_lib->display_errors();
				}
			}
		}
	}
	///////////////////
	function forgot_password(){
		global $data, $user;
		if($user){
			redirect('system');
		}
		//see if currently logging in
		if($this->input->post('action') && $this->input->post('action') == "forgot_password"){
			reset_password($this->input->post('email'));
		}
		$data['page'] = "forgot_password";
		$data['user'] = $user;
		$this->load->view('template',$data);
	}

	function login(){
		global $data, $user;
		if($user){
			redirect('system');
		}
		//see if currently logging in
		if($this->input->post('action') && $this->input->post('action') == "login"){
			login_process($this->input->post('username'),$this->input->post('password'));
			if($this->session->userdata('uid') > 0){
				redirect('system');
			}
		}
		$data['page'] = "login";
		$data['user'] = $user;
		$this->load->view('template',$data);
	}

	function logout(){

		global $data, $user;
		$this->session->unset_userdata('uid');
		redirect('system');
	}

	function code_content(){
		global $data, $user,$system;
		$options = $this->uri->uri_to_assoc(3);
		//DETECT codeblock info needed (ie if head needed...)
		$pass = array(
			'id' => $options['block'],
			'tool' => "content"
		);
		$passct = count($pass);
		$ct = 0;
		$passtxt = "array(";
		foreach($pass as $pk => $pv){
			$ct++;
			$passtxt .= "'".$pk."'=&gt;'".$pv."'";
			if($ct < $passct){
				$passtxt .= ",";
			}
		}
		$passtxt .= ")";
		$ident = "'block'=&gt;".$options['block'];
		//Check if block has a key
		$block = $this->SystemModel->get_content_block($options['block']);
		if(!empty($block->keyword)){
			$passtxt = "'".$block->keyword."'";
		}
		//
		$codeblocks = array();
		$codeblocks[] = array(
			'title' => "Initiate Nakid",
			'description' => "Put the following code at the very top of your page before your html tags (Only once per page)",
			'code' => "&lt;?php include(&quot;".$system['include_path']."nakid.php&quot;); ?&gt;"
		);
		$codeblocks[] = array(
			'title' => "Display Content",
			'description' => "Put the following code where you want the content to appear",
			'code' => "&lt;?php nakid(".$passtxt."); ?&gt;"
		);
		$content = "";
		//Make function
		foreach($codeblocks as $codeblock){
			$content .= "<div class=\"codeblock\">\n";
			$content .= "<h1>".$codeblock['title']."</h1>\n";
			$content .= "<p>".$codeblock['description']."</p>\n";
			$content .= "<textarea readonly=\"readonly\" onclick=\"this.select();\">".$codeblock['code']."</textarea>\n";
			$content .= "</div>\n";
		}
		$data['content_body'] = $content; 
		$this->load->view('code',$data);
	}
	function code_gallery(){
		global $data, $user,$system;
		$options = $this->uri->uri_to_assoc(3);
		//DETECT codeblock info needed (ie if head needed...)
		$pass = array(
			'id' => $options['gallery'],
			'tool' => "gallery"
		);
		$passct = count($pass);
		$ct = 0;
		$passtxt = "array(";
		foreach($pass as $pk => $pv){
			$ct++;
			$passtxt .= "'".$pk."'=&gt;'".$pv."'";
			if($ct < $passct){
				$passtxt .= ",";
			}
		}
		$passtxt .= ")";
		$ident = "'gallery'=&gt;".$options['gallery'];
		//Check if block has a key
		$gallery = $this->SystemModel->get_gallery($options['gallery']);
		if(!empty($gallery->keyword)){
			$passtxt = "'".$gallery->keyword."'";
		}
		//
		$codeblocks = array();
		$codeblocks[] = array(
			'title' => "Initiate Nakid",
			'description' => "Put the following code at the very top of your page before your html tags (Only once per page)",
			'code' => "&lt;?php include(&quot;".$system['include_path']."nakid.php&quot;); ?&gt;"
		);
		$codeblocks[] = array(
			'title' => "Head Code",
			'description' => "Put the following code in between the opening and closing <head></head> tags (Only once per page)",
			'code' => htmlentities('<!--NAKID CMS GALLERY-->
<script src="'.base_url().'assets/addons/jquery-1.4.4.min.js" type="text/javascript"></script>
<script src="'.base_url().'assets/addons/colorbox/jquery.colorbox-min.js" type="text/javascript"></script>
<script src="'.base_url().'assets/tools/gallery/scripts/main.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" media="screen" href="'.base_url().'assets/addons/colorbox/colorbox.css" />
<!--NAKID CMS GALLERY STYLES-->
<link rel="stylesheet" type="text/css" media="screen" href="'.base_url().'assets/tools/gallery/css/styles.css" />')
		);
		$codeblocks[] = array(
			'title' => "Display Content",
			'description' => "Put the following code where you want the content to appear",
			'code' => "&lt;?php nakid(".$passtxt."); ?&gt;"
		);
		$content = "";
		//Make function
		foreach($codeblocks as $codeblock){
			$content .= "<div class=\"codeblock\">\n";
			$content .= "<h1>".$codeblock['title']."</h1>\n";
			$content .= "<p>".$codeblock['description']."</p>\n";
			$content .= "<textarea readonly=\"readonly\" onclick=\"this.select();\">".$codeblock['code']."</textarea>\n";
			$content .= "</div>\n";
		}
		$data['content_body'] = $content; 
		$this->load->view('code',$data);
	}
}

/* End of file system.php */