<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//FOR VIEW GRID FUNCTIONS, USE ONE FUNCTION WITH DIFFERENT SQL
class GridModel extends CI_Model {

    function __construct(){
        // Call the Model constructor
        parent::__construct();
    }
	/*--------------------------
	USERS
	---------------------------*/
	function get_users_grid($sort, $order, $page, $rows, $search){
		///////////////$this->db->where('email', $email); 
		$data = array();
		$total_rowct = 0;
		$rowct = 0;
		$total_pages = 0;
		$limit_start = 0;
		$where = false;
		$like = false;
		if($search){
			$like = array($search[0]=>$search[2]);
		}
		//Get total amount of rows
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$query = $this->db->get('users');
		$total_rowct = $query->num_rows();
		//Calculate Pages
		$total_pages = ceil($total_rowct/$rows);
		//Current Page
		if ($page > $total_pages){
			$page = $total_pages;
		}
		//Get Limit Start
		$limit_start = $rows * $page - $rows;
		if($limit_start < 0){ 
			$limit_start = 0;
		}
		//Query
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$this->db->order_by($sort.' '.$order); 
		$this->db->limit($rows, $limit_start);
		$query2 = $this->db->get('users');
		$rowct = $query2->num_rows();
		//Rows
		$data['rows'] = array();
		foreach($query2->result() as $result){
			$data['rows'][] = $result;
		}
		//
		$data['total_pages'] = $total_pages;
		$data['total_rowct'] = $total_rowct;
		$data['rowct'] = $rowct;
		return $data;
	}
	/*--------------------------
	SYSTEM SETTINGS
	---------------------------*/
	function get_system_settings_grid($sort, $order, $page, $rows, $search){
		///////////////$this->db->where('email', $email); 
		$data = array();
		$total_rowct = 0;
		$rowct = 0;
		$total_pages = 0;
		$limit_start = 0;
		$where = array('editable'=>1);
		$like = false;
		if($search){
			$like = array($search[0]=>$search[2]);
		}
		//Get total amount of rows
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$query = $this->db->get('settings');
		$total_rowct = $query->num_rows();
		//Calculate Pages
		$total_pages = ceil($total_rowct/$rows);
		//Current Page
		if ($page > $total_pages){
			$page = $total_pages;
		}
		//Get Limit Start
		$limit_start = $rows * $page - $rows;
		if($limit_start < 0){ 
			$limit_start = 0;
		}
		//Query
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$this->db->order_by($sort.' '.$order); 
		$this->db->limit($rows, $limit_start);
		$query2 = $this->db->get('settings');
		$rowct = $query2->num_rows();
		//Rows
		$data['rows'] = array();
		foreach($query2->result() as $result){
			$data['rows'][] = $result;
		}
		//
		$data['total_pages'] = $total_pages;
		$data['total_rowct'] = $total_rowct;
		$data['rowct'] = $rowct;
		return $data;
	}
	/*--------------------------
	CONTENT
	---------------------------*/
	function get_content_blocks_grid($sort, $order, $page, $rows, $search){
		///////////////$this->db->where('email', $email); 
		$data = array();
		$total_rowct = 0;
		$rowct = 0;
		$total_pages = 0;
		$limit_start = 0;
		$where = false;
		$like = false;
		if($search){
			$like = array($search[0]=>$search[2]);
		}
		//Get total amount of rows
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$query = $this->db->get("content_blocks");
		$total_rowct = $query->num_rows();
		//Calculate Pages
		$total_pages = ceil($total_rowct/$rows);
		//Current Page
		if ($page > $total_pages){
			$page = $total_pages;
		}
		//Get Limit Start
		$limit_start = $rows * $page - $rows;
		if($limit_start < 0){ 
			$limit_start = 0;
		}
		//Select and Join
		$this->db->select('content_blocks.*, keywords.keyword');
		$this->db->from('content_blocks');
		$this->db->join('keywords', 'keywords.tid = content_blocks.id','left');
		$this->db->where("keywords.tool = 'content'"); 
		//Query
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$this->db->order_by($sort.' '.$order); 
		$this->db->limit($rows, $limit_start);
		$query2 = $this->db->get();
		$rowct = $query2->num_rows();
		//Rows
		$data['rows'] = array();
		foreach($query2->result() as $result){
			$data['rows'][] = $result;
		}
		//
		$data['total_pages'] = $total_pages;
		$data['total_rowct'] = $total_rowct;
		$data['rowct'] = $rowct;
		return $data;
	}
	/*--------------------------
	CONTENT HISTORY
	---------------------------*/
	function get_content_history_grid($block, $sort, $order, $page, $rows, $search){
		///////////////$this->db->where('email', $email); 
		$data = array();
		$total_rowct = 0;
		$rowct = 0;
		$total_pages = 0;
		$limit_start = 0;
		//$where = false;
		$where = array('block'=>$block);
		$like = false;
		if($search){
			$like = array($search[0]=>$search[2]);
		}
		//Get total amount of rows
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		
		$this->db->select('content_versions.id, content_versions.date, users.username');
		$this->db->from('content_versions');
		$this->db->join('users', 'content_versions.author = users.id');
		$query = $this->db->get();
		
		$total_rowct = $query->num_rows();
		//Calculate Pages
		$total_pages = ceil($total_rowct/$rows);
		//Current Page
		if ($page > $total_pages){
			$page = $total_pages;
		}
		//Get Limit Start
		$limit_start = $rows * $page - $rows;
		if($limit_start < 0){ 
			$limit_start = 0;
		}
		//Query
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$this->db->order_by($sort.' '.$order); 
		$this->db->limit($rows, $limit_start);
		
		$this->db->select('content_versions.id, content_versions.date, users.username');
		$this->db->from('content_versions');
		$this->db->join('users', 'content_versions.author = users.id');
		$query2 = $this->db->get();

		$rowct = $query2->num_rows();
		//Rows
		$data['rows'] = array();
		foreach($query2->result() as $result){
			$data['rows'][] = $result;
		}
		//
		$data['total_pages'] = $total_pages;
		$data['total_rowct'] = $total_rowct;
		$data['rowct'] = $rowct;
		return $data;
	}
	/*--------------------------
	GALLERIES
	---------------------------*/
	function get_galleries_grid($sort, $order, $page, $rows, $search){
		///////////////$this->db->where('email', $email); 
		$data = array();
		$total_rowct = 0;
		$rowct = 0;
		$total_pages = 0;
		$limit_start = 0;
		$where = false;
		$like = false;
		if($search){
			$like = array($search[0]=>$search[2]);
		}
		//Get total amount of rows
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$query = $this->db->get("gallery_galleries");
		$total_rowct = $query->num_rows();
		//Calculate Pages
		$total_pages = ceil($total_rowct/$rows);
		//Current Page
		if ($page > $total_pages){
			$page = $total_pages;
		}
		//Get Limit Start
		$limit_start = $rows * $page - $rows;
		if($limit_start < 0){ 
			$limit_start = 0;
		}
		//Select and Join
		$this->db->select('gallery_galleries.*, keywords.keyword');
		$this->db->from('gallery_galleries');
		$this->db->join('keywords', 'keywords.tid = gallery_galleries.id','left');
		$this->db->where("keywords.tool = 'gallery'"); 
		//Query
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$this->db->order_by($sort.' '.$order); 
		$this->db->limit($rows, $limit_start);
		$query2 = $this->db->get();
		$rowct = $query2->num_rows();
		//Rows
		$data['rows'] = array();
		foreach($query2->result() as $result){
			$data['rows'][] = $result;
		}
		//
		$data['total_pages'] = $total_pages;
		$data['total_rowct'] = $total_rowct;
		$data['rowct'] = $rowct;
		return $data;
	}
	/*--------------------------
	GALLERIES
	---------------------------*/
	function get_gallery_images_grid($gid,$sort, $order, $page, $rows, $search){
		///////////////$this->db->where('email', $email); 
		$data = array();
		$total_rowct = 0;
		$rowct = 0;
		$total_pages = 0;
		$limit_start = 0;
		$where = false;
		$like = false;
		if($search){
			$like = array($search[0]=>$search[2]);
		}
		//Get total amount of rows
		$this->db->where("gallery",$gid);
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$query = $this->db->get("gallery_images");
		$total_rowct = $query->num_rows();
		//Calculate Pages
		$total_pages = ceil($total_rowct/$rows);
		//Current Page
		if ($page > $total_pages){
			$page = $total_pages;
		}
		//Get Limit Start
		$limit_start = $rows * $page - $rows;
		if($limit_start < 0){ 
			$limit_start = 0;
		}
		//Query
		$this->db->where("gallery",$gid);
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$this->db->order_by($sort.' '.$order); 
		$this->db->limit($rows, $limit_start);
		$query2 = $this->db->get("gallery_images");
		$rowct = $query2->num_rows();
		//Rows
		$data['rows'] = array();
		foreach($query2->result() as $result){
			$data['rows'][] = $result;
		}
		//
		$data['total_pages'] = $total_pages;
		$data['total_rowct'] = $total_rowct;
		$data['rowct'] = $rowct;
		return $data;
	}
	/*--------------------------
	GALLERIES
	---------------------------*/
	function get_gallery_categories_grid($gid,$sort, $order, $page, $rows, $search){
		///////////////$this->db->where('email', $email); 
		$data = array();
		$total_rowct = 0;
		$rowct = 0;
		$total_pages = 0;
		$limit_start = 0;
		$where = false;
		$like = false;
		if($search){
			$like = array($search[0]=>$search[2]);
		}
		//Get total amount of rows
		$this->db->where("gallery",$gid);
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$query = $this->db->get("gallery_categories");
		$total_rowct = $query->num_rows();
		//Calculate Pages
		$total_pages = ceil($total_rowct/$rows);
		//Current Page
		if ($page > $total_pages){
			$page = $total_pages;
		}
		//Get Limit Start
		$limit_start = $rows * $page - $rows;
		if($limit_start < 0){ 
			$limit_start = 0;
		}
		//Query
		$this->db->where("gallery",$gid);
		if($where){
			$this->db->where($where); 
		}
		if($like){
			$this->db->like($like); 
		}
		$this->db->order_by($sort.' '.$order); 
		$this->db->limit($rows, $limit_start);
		$query2 = $this->db->get("gallery_categories");
		$rowct = $query2->num_rows();
		//Rows
		$data['rows'] = array();
		foreach($query2->result() as $result){
			$data['rows'][] = $result;
		}
		//
		$data['total_pages'] = $total_pages;
		$data['total_rowct'] = $total_rowct;
		$data['rowct'] = $rowct;
		return $data;
	}
}
/*
END MODEL
*/