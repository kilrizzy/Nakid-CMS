<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class SystemModel extends CI_Model {

    function __construct(){
        // Call the Model constructor
        parent::__construct();
    }
	
	/*--------------------------------------
	TABLES
	----------------------------------------*/
	function check_table_exists($table){
		$tableexists = $this->db->table_exists($this->db->dbprefix($table));
		return $tableexists;
	}
	
	/*--------------------------------------
	SYSTEM SETTINGS
	----------------------------------------*/
	function get_settings_array(){
		$system = array();
		$system_query = $this->db->get('settings');
		foreach ($system_query->result() as $row){
			$system[$row->name] = $row->value;
		}	
		return $system;
	}
	
	function update_system_setting($sid,$dbdata){
		$this->db->where('id', $sid);
		$this->db->update('settings', $dbdata); 	
	}
	//
	function delete_system_setting($sid){
		$this->db->delete('settings', array('id' => $sid)); 
	}
	/*--------------------------------------
	USERS
	----------------------------------------*/
	function check_user_exists($uid){
		$this->db->where('id', $uid); 
		$user_query = $this->db->get('users');
		if ($user_query->num_rows() > 0){
			return true;
		}else{
			return false;	
		}
	}
	function check_username_exists($uname,$uid){
		$this->db->where('username', $uname); 
		$this->db->where('id !=', $uid); 
		$query = $this->db->get('users');
		if ($query->num_rows() > 0){
			return true;
		}else{
			return false;	
		}
	}
	function check_email_exists($email,$uid){
		$this->db->where('email', $email); 
		$this->db->where('id !=', $uid); 
		$query = $this->db->get('users');
		if ($query->num_rows() > 0){
			return true;
		}else{
			return false;	
		}
	}
	
	function add_user($db_data,$addpermissions=false,$creator=0){
		$this->db->insert('users', $db_data); 
		$uid = $this->db->insert_id();
		if($addpermissions){
			//Get all allowed permissions from the creator and add to this user
			$allowed_permissions = $this->get_users_permissions($creator);
			foreach($allowed_permissions as $allowed_permission){
				$this->add_user_permission($uid,$allowed_permission->id);
			}
		}
	}
	
	function update_user($uid,$dbdata){
		$this->db->where('id', $uid);
		$this->db->update('users', $dbdata); 	
	}
	
	function delete_user($uid){
		$this->db->delete('users', array('id' => $uid)); 
		$this->db->delete('permission_users', array('uid' => $uid)); 
	}
	
	function get_user($uid){
		$this->db->where('id', $uid); 
		$user_query = $this->db->get('users');
		if ($user_query->num_rows() > 0){
			$user = $user_query->row();
			return $user;
		}else{
			return false;	
		}
	}
	function get_users_permissions($uid){
		$permissions = array();
		$this->db->select('permission_values.key,permission_values.id,permission_values.description');
		$this->db->from('permission_values');
		$this->db->join('permission_users', 'permission_values.id = permission_users.pid');
		$this->db->where('permission_users.uid',$uid); 
		$query = $this->db->get();
		foreach($query->result() as $permission){
			$permissions[] = $permission;//$permission->key;
		}
		return $permissions;
	}
	//
	function check_user_permission($pname,$uid){
		
		
		$this->db->select('permission_users.id');
		$this->db->from('permission_values');
		$this->db->join('permission_users', 'permission_values.id = permission_users.pid');
		$this->db->where('permission_users.uid',$uid); 
		$this->db->where('permission_values.key',$pname); 
		$query = $this->db->get();
		if ($query->num_rows() > 0){
			return true;
		}else{
			return false;	
		}
	}
	//
	function get_user_by_username($username){
		$this->db->where('username', $username); 
		$query = $this->db->get('users',1);
		if ($query->num_rows() > 0){
			$user = $query->row();
			return $user;
		}else{
			return false;	
		}
	}
	
	function get_user_by_email($email){
		$this->db->where('email', $email); 
		$query = $this->db->get('users',1);
		if ($query->num_rows() > 0){
			$user = $query->row();
			return $user;
		}else{
			return false;	
		}
	}	
	
	function remove_user_permission($uid,$pid){
		$this->db->where('uid', $uid);
		$this->db->where('pid', $pid);
		$this->db->delete('permission_users'); 
	}
	
	function add_user_permission($uid,$pid){
		$db_data = array(
			'pid' => $pid,
			'uid' => $uid
		);
		$this->db->insert('permission_users', $db_data); 
	}
	
	function get_permission_categories(){
		$this->db->order_by("name", "asc"); 
		$p_cat_query = $this->db->get('permission_categories');
		return $p_cat_query->result();
	}
	
	function get_permission_values($cid){
		$this->db->where('cid', $cid); 
		$this->db->order_by("description", "asc"); 
		$p_val_query = $this->db->get('permission_values');
		return $p_val_query->result();
	}
	
	function get_permission_user($uid,$pid){
		$this->db->where('uid', $uid); 
		$this->db->where('pid', $pid); 
		$p_checked_query = $this->db->get('permission_users');
		if($p_checked_query->num_rows() > 0){
			return true;
		}else{
			return false;	
		}
	}
	/*--------------------------------------
	CONTENT
	----------------------------------------*/
	function add_content_block($db_data,$keyword = ""){
		$this->db->insert('content_blocks', $db_data); 
		//XXX-Make sure keyword is unique, if not, attach number to it
		$key_data = array(
		   'keyword' => $keyword,
		   'tool' => "content",
		   'tid' => $this->db->insert_id()
		);
		$this->db->insert('keywords', $key_data); 
	}
	
	function update_content_block($bid,$dbdata){
		$this->db->where('id', $bid);
		$this->db->update('content_blocks', $dbdata); 	
	}
	
	function delete_content_block($bid){
		//Delete Keys
		$this->db->delete('keywords', array('tool' => 'content', 'tid' => $bid));
		//Delete Versions
		$this->db->delete('content_versions', array('type' => 'content', 'block' => $bid));
		//Delete Content Block
		$this->db->delete('content_blocks', array('id' => $bid)); 
	}
	function get_content_block($bid,$vid=0,$keyword=""){
		//Get Block by id or keyword
		if(!empty($keyword)){
			$this->db->where('keywords.keyword', $keyword); 
		}else{
			$this->db->where('content_blocks.id', $bid); 
		}
		//
		$this->db->select('content_blocks.*, keywords.keyword');
		$this->db->from('content_blocks');
		$this->db->join('keywords', 'keywords.tid = content_blocks.id','left');
		$this->db->where("keywords.tool = 'content'"); 
		//
		$block_query = $this->db->get();
		$block = $block_query->row();
		//???$block->vid = $block->id;
		//Get Version
		if($vid > 0){
			$this->db->where('id', $vid); 
		}
		$this->db->where('type', "content"); 
		$this->db->where('block', $block->id); 
		$this->db->order_by("id", "desc"); 
		$version_query = $this->db->get('content_versions');
		if($version_query->num_rows() > 0){
			$version = $version_query->row();
			$version->vid = $version->id;
			//Combine objects
			$content = (object) array_merge((array) $version,(array) $block);
		}else{
			$block->vid = 0;
			$block->content = "";
			$content = $block;
		}
		return $content;
	}
	function add_content_version($dbdata){
		$this->db->insert('content_versions', $dbdata); 
		//Get version id
		$vid = $this->db->insert_id();
		return $vid;
	}
	/*--------------------------------------
	GALLERIES
	----------------------------------------*/
	function add_gallery($db_data,$keyword = ""){
		$this->db->insert('gallery_galleries', $db_data); 
		//XXX-Make sure keyword is unique, if not, attach number to it
		$key_data = array(
		   'keyword' => $keyword,
		   'tool' => "gallery",
		   'tid' => $this->db->insert_id()
		);
		$this->db->insert('keywords', $key_data); 
	}
	function update_gallery($gid,$dbdata){
		$this->db->where('id', $gid);
		$this->db->update('gallery_galleries', $dbdata); 	
	}
	
	function delete_gallery($gid){
		$this->db->delete('gallery_galleries', array('id' => $gid)); 
	}
	function get_gallery($gid,$vid=0,$keyword=""){
		//Get Block by id or keyword
		if(!empty($keyword)){
			$this->db->where('keywords.keyword', $keyword); 
		}else{
			$this->db->where('gallery_galleries.id', $gid); 
		}
		//
		$this->db->select('gallery_galleries.*, keywords.keyword');
		$this->db->from('gallery_galleries');
		$this->db->join('keywords', 'keywords.tid = gallery_galleries.id','left');
		$this->db->where("keywords.tool = 'gallery'"); 
		//
		$gallery_query = $this->db->get();
		$gallery = $gallery_query->row();
		return $gallery;
	}
	//GALLERY CATEGORIES
	function add_gallery_category($db_data){
		$this->db->insert('gallery_categories', $db_data); 
	}
	function update_gallery_category($gid,$dbdata){
		$this->db->where('id', $gid);
		$this->db->update('gallery_categories', $dbdata); 	
	}
	function delete_gallery_category($gid){
		$this->db->delete('gallery_categories', array('id' => $gid)); 
		//Delete linkers as well
	}
	function get_gallery_categories($gid){
		$this->db->where('gallery', $gid); 
		$this->db->order_by("order", "desc"); 
		$query = $this->db->get('gallery_categories');
		return $query->result();
	}
	function get_gallery_image_categories($gid,$iid){
		$categories = array();
		$this->db->select('gallery_categories.id,gallery_categories.title');
		$this->db->from('gallery_categories');
		$this->db->join('gallery_image_categories', 'gallery_categories.id = gallery_image_categories.category');
		$this->db->where('gallery_image_categories.image',$iid); 
		$this->db->where('gallery_image_categories.gallery',$gid); 
		$query = $this->db->get();
		foreach($query->result() as $category){
			$categories[] = $category;
		}
		return $categories;
	}
	function remove_gallery_image_category($gid,$iid,$cid){
		$this->db->where('gallery', $gid);
		$this->db->where('image', $iid);
		$this->db->where('category', $cid);
		$this->db->delete('gallery_image_categories'); 
	}
	
	function add_gallery_image_category($gid,$iid,$cid){
		$db_data = array(
			'gallery' => $gid,
			'image' => $iid,
			'category' => $cid
		);
		$this->db->insert('gallery_image_categories', $db_data); 
	}
	//IMAGES
	function add_gallery_image($file,$gid){
		//Make title
		$db_data = array(
			//'title' => '',
			'file' => $file,
			'gallery' => $gid
		);
		$this->db->insert('gallery_images', $db_data); 
	}
	function get_gallery_images($gid=0){
		if($gid > 0){
			$this->db->where('gallery', $gid); 
		}
		$this->db->order_by("order", "desc"); 
		$query = $this->db->get('gallery_images');
		return $query->result();
	}
	function get_gallery_image($iid=0){
		$this->db->where('id', $iid); 
		$query = $this->db->get('gallery_images');
		$image = $query->row();
		return $image;
	}
	function update_gallery_image($iid,$dbdata){
		$this->db->where('id', $iid);
		$this->db->update('gallery_images', $dbdata); 	
	}
	function delete_gallery_image($iid){
		$this->db->delete('gallery_images', array('id' => $iid)); 
	}
	/*--------------------------------------
	KEYWORDS
	----------------------------------------*/
	function get_keyword($keyword){
		$this->db->where('keyword', $keyword); 
		$query = $this->db->get('keywords');
		if ($query->num_rows() > 0){
			return $query->row();
		}else{
			return false;	
		}
	}
	function update_keyword($tool,$tid,$keyword){
		$dbdata = array(
			'keyword' => $keyword
		);
		$this->db->where('tool', $tool);
		$this->db->where('tid', $tid);
		$this->db->update('keywords', $dbdata); 	
	}
}
/*
END MODEL
*/