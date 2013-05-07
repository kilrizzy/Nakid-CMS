<?php
class Grid extends CI_Controller {
	public function __construct(){
		parent::__construct();
		global $user,$upload_directory;
		//CHECK LOGIN
		$user = $this->SystemModel->get_user($this->session->userdata('uid'));
		if(!$user){
			$nonauth = array("login","forgot_password");
			if(!in_array($this->uri->segment(2),$nonauth)){
				redirect('system/login');
			}
		}
		$paths = explode($_SERVER["SERVER_NAME"],base_url());
		$nakid_install = $paths[1];
		$upload_directory = $nakid_install."uploads";
	}
	function index(){
		//
	}
	//////////////////////////////////////
	//USERS
	//////////////////////////////////////
	function users(){
		global $user;
		if(permission('manage_users')){
			$error = false;
			$operation = $this->input->post('oper');
			if($operation == "add" || $operation == "edit"){
				$dbdata = array(
				   'username' => $this->input->post('username'),
				   'email' => $this->input->post('email'),
				   'fname' => $this->input->post('fname'),
				   'lname' => $this->input->post('lname')
				);
			}
			//ADD
			if($operation == "add"){
				//Check if username exists
				$usernameexists = $this->SystemModel->check_username_exists($this->input->post('username'),0);
				if($usernameexists){
					$error = true;
					$data['error'] = "Username already in use";
					$this->load->view('grid',$data);
				}
				//Check if email exists
				$emailexists = $this->SystemModel->check_email_exists($this->input->post('email'),0);
				if($emailexists){
					$error = true;
					$data['error'] = "Email already in use";
					$this->load->view('grid',$data);
				}
				//Insert User (and add all available permissions)
				$dbdata['password'] = prep_password($this->input->post('password'));
				if(!$error){
					$this->SystemModel->add_user($dbdata,true,$user->id);
				}
			}
			//EDIT
			if($operation == "edit"){
				//Check if username exists
				$usernameexists = $this->SystemModel->check_username_exists($this->input->post('username'),$this->input->post('id'));
				if($usernameexists){
					$error = true;
					$data['error'] = "Username already in use";
					$this->load->view('grid',$data);
				}
				//Check if email exists
				$emailexists = $this->SystemModel->check_email_exists($this->input->post('email'),$this->input->post('id'));
				if($emailexists){
					$error = true;
					$data['error'] = "Email already in use";
					$this->load->view('grid',$data);
				}
				//Update User
				$postpw = $this->input->post('password');
				if(!strstr($postpw,"*") && !empty($postpw)){
					$dbdata['password'] = prep_password($postpw);
				}
				if(!$error){
					$this->SystemModel->update_user($this->input->post('id'),$dbdata);
				}
			}
			//DELETE
			if($operation == "del"){
				$this->SystemModel->delete_user($this->input->post('id'));
			}
			//VIEW
			if(empty($operation)){
				$data = array();
				$page = 1;
				$sidx = "id";
				$sord = "asc";
				$rows = 20;
				if($this->input->post('page')){
					$page = $this->input->post('page');
				}
				if($this->input->post('sidx')){
					$sidx = $this->input->post('sidx');
				}
				if($this->input->post('sord')){
					$sord = $this->input->post('sord');
				}
				if($this->input->post('rows')){
					$rows = $this->input->post('rows');
				}
				$totalpages = 0;
				$count = 0;
				$search = false;
				if($this->input->post('searchField')){
					$search = array($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
				}
				$user_data = $this->GridModel->get_users_grid($sidx, $sord, $page, $rows, $search);
				$totalpages = $user_data['total_pages'];
				$count = $user_data['total_rowct'];
				//Create rows
				$rows = array();
				foreach($user_data['rows'] as $row){
					//Get user permissions
					$user_permissions = $this->SystemModel->get_users_permissions($row->id);
					$user_permission_keys = array();
					$permissions = array();
					foreach($user_permissions as $user_permission){
						$user_permission_keys[] = $user_permission->key;
						$permissions[] = $user_permission->key;
					}
					$permissions = implode(", ",$permissions);
					if(empty($permissions)){
						$permissions = "<span style='color:red;'>NONE</span>";
					}
					//Permissions Link
					$permissions_url = site_url("system/permissions/".$row->id);
					$permissions_link = "<a href='".$permissions_url."' class='framepop' onclick='parent.$.colorbox({href:\"".$permissions_url."\",width:\"500\", height:\"600\", iframe:true,onClosed:function(){ $(\"#list\").trigger(\"reloadGrid\"); } }); return false;'>(edit)</a> ".$permissions."";
					//check if can edit permissions for this user
					if($row->id == 1){
						$permissions_link = "Full Access";
					}
					//Add row
					$rows[$row->id] = array();
					$rows[$row->id][] = $row->username;
					$rows[$row->id][] = "*****";
					$rows[$row->id][] = $row->email;
					$rows[$row->id][] = $row->fname;
					$rows[$row->id][] = $row->lname;
					$rows[$row->id][] = $permissions_link;
				}
				$data['page'] = $page;
				$data['total_pages'] = $totalpages;
				$data['count'] = $count;
				$data['grid'] = $rows;
				
				$this->load->view('grid',$data);
			}
		}else{
			$data['page'] = "system/access_denied";
			$this->load->view('template',$data);
		}
	}
	//////////////////////////////////////
	//SYSTEM SETTINGS
	//////////////////////////////////////
	function settings(){
		$operation = $this->input->post('oper');
		if($operation == "add" || $operation == "edit"){
			$dbdata = array(
			   'value' => $this->input->post('value')
			);
		}
		//EDIT
		if($operation == "edit"){
			$this->SystemModel->update_system_setting($this->input->post('id'), $dbdata);
		}
		//DELETE
		if($operation == "del"){
			$this->SystemModel->delete_system_setting($this->input->post('id'));
		}
		//VIEW
		if(empty($operation)){
			$data = array();
			$page = $this->input->post('page');
			$sidx = $this->input->post('sidx');
			$sord = $this->input->post('sord');
			$rows = $this->input->post('rows');
			$totalpages = 0;
			$count = 0;
			$search = false;
			if($this->input->post('searchField')){
				$search = array($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
			}
			$grid_data = $this->GridModel->get_system_settings_grid($sidx, $sord, $page, $rows, $search);
			$totalpages = $grid_data['total_pages'];
			$count = $grid_data['total_rowct'];
			//Create rows
			$rows = array();
			foreach($grid_data['rows'] as $row){
				//Add row
				$rows[$row->id] = array();
				$rows[$row->id][] = "<strong>".$row->name."</strong>";
				$rows[$row->id][] = $row->value;
				$rows[$row->id][] = $row->description;
			}
			$data['page'] = $page;
			$data['total_pages'] = $totalpages;
			$data['count'] = $count;
			$data['grid'] = $rows;
			
			$this->load->view('grid',$data);
		}
	}
	//////////////////////////////////////
	//CONTENT
	//////////////////////////////////////
	function content(){
		$error = false;
		$operation = $this->input->post('oper');
		if($operation == "add" || $operation == "edit"){
			$dbdata = array(
			   'title' => $this->input->post('title')
			);
			$keyword = $this->input->post('keyword');
		}
		//ADD
		if($operation == "add"){
			$this->SystemModel->add_content_block($dbdata,$keyword);
		}
		//VIEW
		if(empty($operation)){
			$data = array();
			$page = 1;
			$sidx = "id";
			$sord = "asc";
			$rows = 20;
			if($this->input->post('page')){
				$page = $this->input->post('page');
			}
			if($this->input->post('sidx')){
				$sidx = $this->input->post('sidx');
			}
			if($this->input->post('sord')){
				$sord = $this->input->post('sord');
			}
			if($this->input->post('rows')){
				$rows = $this->input->post('rows');
			}
			$totalpages = 0;
			$count = 0;
			$search = false;
			if($this->input->post('searchField')){
				$search = array($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
			}
			$grid_data = $this->GridModel->get_content_blocks_grid($sidx, $sord, $page, $rows, $search);
			$totalpages = $grid_data['total_pages'];
			$count = $grid_data['total_rowct'];
			//Create rows
			$rows = array();
			foreach($grid_data['rows'] as $row){
				//Add row
				$rows[$row->id] = array();
				$edit_url = site_url("system/content_edit/".$row->id);
				$edit_link = "<a href='".$edit_url."'>Edit Block</a>";
				//
				$showcode_url = site_url("system/code_content/block/".$row->id."");
				$showcode_link = "<a href='".$showcode_url."' class='framepop' onclick='parent.$.colorbox({href:\"".$showcode_url."\",width:\"500\", height:\"500\", iframe:true}); return false;'>Show Connector Code</a>";
				//
				$delete_url = site_url("system/content_delete/".$row->id."");
				$delete_link = "<a href='".$delete_url."' class='framepop' onclick='parent.$.colorbox({href:\"".$delete_url."\",width:\"500\", height:\"500\", iframe:true,onClosed:function(){ $(\"#list\").trigger(\"reloadGrid\"); }}); return false;' style='color:#ff0000;'>Delete</a>";
				//Check permissions
				if($row->editable == 0 || !permission('cms_edit')){
					$edit_link = "";
				}
				if($row->deletable == 0 || !permission('cms_delete')){
					$delete_link = "";
				}
				if(!permission('view_code') || $row->id == 1){
					$showcode_link = "";
				}
				//
				$rows[$row->id][] = $edit_link;
				$rows[$row->id][] = $row->title;
				$rows[$row->id][] = $row->keyword;
				$rows[$row->id][] = $showcode_link;
				$rows[$row->id][] = $delete_link;
			}
			$data['page'] = $page;
			$data['total_pages'] = $totalpages;
			$data['count'] = $count;
			$data['grid'] = $rows;
			
			$this->load->view('grid',$data);
		}
	}
	//////////////////////////////////////
	//CONTENT HISTORY
	//////////////////////////////////////
	function content_history(){		
		$error = false;
		$operation = $this->input->post('oper');
		$block = $this->uri->segment(3);
		if(empty($operation)){
			$data = array();
			$page = $this->input->post('page');
			$sidx = $this->input->post('sidx');
			$sord = $this->input->post('sord');
			$rows = $this->input->post('rows');
			$totalpages = 0;
			$count = 0;
			$search = false;
			if($this->input->post('searchField')){
				$search = array($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
			}
			$grid_data = $this->GridModel->get_content_history_grid($block, $sidx, $sord, $page, $rows, $search);
			$totalpages = $grid_data['total_pages'];
			$count = $grid_data['total_rowct'];
			//Create rows
			$rows = array();
			foreach($grid_data['rows'] as $row){
				$preview_url = site_url("system/content_preview/".$block."/".$row->id);
				$preview_link = "<a href='".$preview_url."' class='framepop' onclick='parent.$.colorbox({href:\"".$preview_url."\",width:\"80%\", height:\"80%\", iframe:true}); return false;'>Preview</a>";
				//$preview_link = "<a href='".$preview_url."'>Preview</a>";
				$date = date("m/d/Y g:i a",strtotime($row->date));
				$revert_url = site_url("system/content_revert/".$block."/".$row->id);
				$revert_link = "<a href='".$revert_url."'>Revert</a>";
				//ADD ROW
				$rows[$row->id] = array();
				$rows[$row->id][] = $preview_link;
				$rows[$row->id][] = $date;
				$rows[$row->id][] = "<strong>".$row->username."</strong>";
				$rows[$row->id][] = $revert_link;
			}
			$data['page'] = $page;
			$data['total_pages'] = $totalpages;
			$data['count'] = $count;
			$data['grid'] = $rows;
			
			$this->load->view('grid',$data);
		}
	}
	//////////////////////////////////////
	//GALLERIES
	//////////////////////////////////////
	function galleries(){
		$error = false;
		$operation = $this->input->post('oper');
		if($operation == "add" || $operation == "edit"){
			$dbdata = array(
			   'title' => $this->input->post('title')
			);
			$keyword = $this->input->post('keyword');
		}
		//ADD
		if($operation == "add"){
			$this->SystemModel->add_gallery($dbdata,$keyword);
		}
		//VIEW
		if(empty($operation)){
			$data = array();
			$page = 1;
			$sidx = "id";
			$sord = "asc";
			$rows = 20;
			if($this->input->post('page')){
				$page = $this->input->post('page');
			}
			if($this->input->post('sidx')){
				$sidx = $this->input->post('sidx');
			}
			if($this->input->post('sord')){
				$sord = $this->input->post('sord');
			}
			if($this->input->post('rows')){
				$rows = $this->input->post('rows');
			}
			$totalpages = 0;
			$count = 0;
			$search = false;
			if($this->input->post('searchField')){
				$search = array($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
			}
			$grid_data = $this->GridModel->get_galleries_grid($sidx, $sord, $page, $rows, $search);
			$totalpages = $grid_data['total_pages'];
			$count = $grid_data['total_rowct'];
			//Create rows
			$rows = array();
			foreach($grid_data['rows'] as $row){
				//Add row
				$rows[$row->id] = array();
				$edit_url = site_url("system/gallery_edit/".$row->id);
				$edit_link = "<a href='".$edit_url."'>Edit Gallery</a>";
				//
				$showcode_url = site_url("system/code_gallery/gallery/".$row->id."");
				$showcode_link = "<a href='".$showcode_url."' class='framepop' onclick='parent.$.colorbox({href:\"".$showcode_url."\",width:\"500\", height:\"500\", iframe:true}); return false;'>Show Connector Code</a>";
				//
				$delete_url = site_url("system/gallery_delete/".$row->id."");
				$delete_link = "<a href='".$delete_url."' class='framepop' onclick='parent.$.colorbox({href:\"".$delete_url."\",width:\"500\", height:\"500\", iframe:true,onClosed:function(){ $(\"#list\").trigger(\"reloadGrid\"); }}); return false;' style='color:#ff0000;'>Delete</a>";
				//
				$rows[$row->id][] = $edit_link;
				$rows[$row->id][] = $row->title;
				$rows[$row->id][] = $row->keyword;
				$rows[$row->id][] = $showcode_link;
				$rows[$row->id][] = $delete_link;
			}
			$data['page'] = $page;
			$data['total_pages'] = $totalpages;
			$data['count'] = $count;
			$data['grid'] = $rows;
			
			$this->load->view('grid',$data);
		}
	}
	//////////////////////////////////////
	//GALLERY CATEGORIES
	//////////////////////////////////////
	function gallery_categories(){
		$error = false;
		$operation = $this->input->post('oper');
		$gallery = $this->uri->segment(3);
		if($operation == "add" || $operation == "edit"){
			$dbdata = array(
			   'gallery' => $gallery,
			   'title' => $this->input->post('title'),
			   'order' => $this->input->post('order')
			);
		}
		//ADD
		if($operation == "add"){
			$this->SystemModel->add_gallery_category($dbdata);
		}
		//EDIT
		if($operation == "edit"){
			$this->SystemModel->update_gallery_category($this->input->post('id'), $dbdata);
		}
		//DELETE
		if($operation == "del"){
			$this->SystemModel->delete_gallery_category($this->input->post('id'));
		}
		//VIEW
		if(empty($operation)){
			$data = array();
			$page = 1;
			$sidx = "id";
			$sord = "asc";
			$rows = 20;
			if($this->input->post('page')){
				$page = $this->input->post('page');
			}
			if($this->input->post('sidx')){
				$sidx = $this->input->post('sidx');
			}
			if($this->input->post('sord')){
				$sord = $this->input->post('sord');
			}
			if($this->input->post('rows')){
				$rows = $this->input->post('rows');
			}
			$totalpages = 0;
			$count = 0;
			$search = false;
			if($this->input->post('searchField')){
				$search = array($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
			}
			$grid_data = $this->GridModel->get_gallery_categories_grid($gallery,$sidx, $sord, $page, $rows, $search);
			
			$totalpages = $grid_data['total_pages'];
			$count = $grid_data['total_rowct'];
			//Create rows
			$rows = array();
			foreach($grid_data['rows'] as $row){
				//Add row
				$rows[$row->id] = array();
				$rows[$row->id][] = $row->title;
				$rows[$row->id][] = $row->order;
			}
			$data['page'] = $page;
			$data['total_pages'] = $totalpages;
			$data['count'] = $count;
			$data['grid'] = $rows;
			
			$this->load->view('grid',$data);
		}
	}
	//////////////////////////////////////
	//IMAGES
	//////////////////////////////////////
	function gallery_images(){
		global $upload_directory;
		$error = false;
		$operation = $this->input->post('oper');
		$gid = $this->uri->segment(3);
		if($operation == "add" || $operation == "edit"){
			$dbdata = array(
			   'order' => $this->input->post('order'),
			   'title' => $this->input->post('title')
			);
		}
		//EDIT
		if($operation == "edit"){
			$this->SystemModel->update_gallery_image($this->input->post('id'),$dbdata);
		}
		//DELETE
		if($operation == "del"){
			$this->SystemModel->delete_gallery_image($this->input->post('id'));
		}
		//VIEW
		if(empty($operation)){
			$data = array();
			$page = 1;
			$sidx = "order";
			$sord = "desc";
			$rows = 20;
			if($this->input->post('page')){
				$page = $this->input->post('page');
			}
			if($this->input->post('sidx')){
				$sidx = $this->input->post('sidx');
			}
			if($this->input->post('sord')){
				$sord = $this->input->post('sord');
			}
			if($this->input->post('rows')){
				$rows = $this->input->post('rows');
			}
			$totalpages = 0;
			$count = 0;
			$search = false;
			if($this->input->post('searchField')){
				$search = array($this->input->post('searchField'),$this->input->post('searchOper'),$this->input->post('searchString'));
			}
			$grid_data = $this->GridModel->get_gallery_images_grid($gid,$sidx, $sord, $page, $rows, $search);
			$totalpages = $grid_data['total_pages'];
			$count = $grid_data['total_rowct'];
			//Create rows
			$rows = array();
			foreach($grid_data['rows'] as $row){
				//Add row
				$rows[$row->id] = array();
				//Get Image Info
				$filename = explode("/",$row->file);
				$filename = end($filename);
				$image = $upload_directory."/thumbs/".$filename."";
				$full = $upload_directory."/large/".$filename."";
				$image_link = "<a href='".$full."' class='framepop' onclick='parent.$.colorbox({href:\"".$full."\",width:\"500\", height:\"500\", iframe:false}); return false;'><img height='50' src='".$image."' /></a>";
				//Get Image Categories
				$image_categories = $this->SystemModel->get_gallery_image_categories($gid,$row->id);
				$categories = array();
				foreach($image_categories as $image_category){
					$categories[] = $image_category->title;
				}
				$categories = implode(", ",$categories);
				if(empty($categories)){
					$categories = "<span style='color:red;'>NONE</span>";
				}
				//Categories Link
				$categories_url = site_url("system/gallery_image_categories/".$row->id);
				$category_link = "<a href='".$categories_url."' class='framepop' onclick='parent.$.colorbox({href:\"".$categories_url."\",width:\"500\", height:\"600\", iframe:true,onClosed:function(){ $(\"#list\").trigger(\"reloadGrid\"); } }); return false;'>(edit)</a> ".$categories."";
				//
				$rows[$row->id][] = $image_link;
				$rows[$row->id][] = $row->title;
				$rows[$row->id][] = $row->order;
				$rows[$row->id][] = $category_link;
			}
			$data['page'] = $page;
			$data['total_pages'] = $totalpages;
			$data['count'] = $count;
			$data['grid'] = $rows;
			
			$this->load->view('grid',$data);
		}
	}
}
/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */