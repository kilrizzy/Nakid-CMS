<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Connector extends CI_Controller {
	public function __construct(){
		parent::__construct();
		global $data, $system, $user, $options,$upload_directory;
		$options = unserialize(base64_decode($_GET['options']));
		$options = array_map("urldecode", $options);
		//Get tool and pass on info
		$tool = "";
		if(isset($options['tool'])){
			$tool = $options['tool'];
		}else if( isset($options['keyword']) ){
			$keyword = $this->SystemModel->get_keyword($options['keyword']);
			if($keyword){
				$tool = $keyword->tool;	
			}
		}
		//
		$paths = explode($_SERVER["SERVER_NAME"],base_url());
		$nakid_install = $paths[1];
		$upload_directory = $nakid_install."uploads";
		//Redirect to correct function
		if($tool == "content"){
			$this->content();
		}else if($tool == "gallery"){
			$this->gallery();
		}else{
			echo("Tool Parameter not selected");
		}
	}
	function index(){
		global $data, $user, $options;
	}
	function content(){
		global $data, $user, $options;
		//print_r($options);
		$blockid = 0;
		if(isset($options['id'])){
			$blockid = $options['id'];
		}
		$keyword = "";
		if(isset($options['keyword'])){
			$keyword = $options['keyword'];
		}
		//Load from content editor
		$content = $this->SystemModel->get_content_block($blockid,0,$keyword);
		$data['content_body'] = $content->content; 
		$this->load->view('connector/content',$data);
		if(!licensed(false)){	
			$this->load->view('connector/license',$data);
		}
	}
	function gallery(){
		global $data, $user, $options,$upload_directory;
		//print_r($options);
		$galleryid = 0;
		if(isset($options['id'])){
			$galleryid = $options['id'];
		}
		$keyword = "";
		if(isset($options['keyword'])){
			$keyword = $options['keyword'];
		}
		//Load from content editor
		$gallery = $this->SystemModel->get_gallery($galleryid,0,$keyword);
		$galleryid = $gallery->id;
		$gallerydata = unserialize($gallery->data);
		//Display Images From Gallery
		$images = $this->SystemModel->get_gallery_images($galleryid);
		$imagelinks = array();
		foreach($images as $ikey => $image){
			//IMAGE LINKS
			$filename = explode("/",$image->file);
			$filename = end($filename);
			$thumb = $upload_directory."/thumbs/".$filename."";
			$full = $upload_directory."/large/".$filename."";
			$imagelink = "";
			if($gallerydata['image_titles'] == "above"){
				$imagelink .= "<label>".$image->title."</label>";
			}
			$imagetitle = "";
			if(!empty($image->title)){
				$imagetitle = " title='".$image->title."' ";
			}
			$imagelink .= "<a href='".$full."' class='lightbox' rel='nakid_gallery_".$gallery->id."' ".$imagetitle."><img src='".$thumb."' /></a>";
			if($gallerydata['image_titles'] == "below"){
				$imagelink .= "<label>".$image->title."</label>";
			}
			$imagelinks[] = $imagelink;
			$images[$ikey]->link = $imagelink;
			//IMAGE CATEGORIES
			$image_categories = $this->SystemModel->get_gallery_image_categories($gallery->id,$image->id);
			$images[$ikey]->categories = $image_categories;
			//CATEGORY CLASSES
			$catclasses = array();
			foreach($image_categories as $image_category){
				$catclasses[] = "category_".$image_category->id;
			}
			$images[$ikey]->catclasses = implode(" ",$catclasses);
		}
		//Display Categories
		$categories = $this->SystemModel->get_gallery_categories($galleryid);
		//
		$galleryclasses = array();
		$galleryclasses[] = "nakid_gallery";
		$galleryclasses[] = "title_".$gallerydata['image_titles'];
		//
		$datagallery = array();
		$datagallery['description'] = $gallery->description; 
		$datagallery['classes'] = implode(" ",$galleryclasses); 
		$data['gallery'] = $datagallery;
		$data['images'] = $images;
		$data['categories'] = $categories;
		$data['options'] = $gallerydata;
		//$data['content_body'] = $content->content; 
		$this->load->view('connector/gallery',$data);
		if(!licensed(false)){	
			$this->load->view('connector/license',$data);
		}
	}
}

/* End of file connector.php */

/* Location: ./system/application/controllers/connector.php */