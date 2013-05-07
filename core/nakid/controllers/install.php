<?php

class Install extends CI_Controller {

	public function __construct(){
		parent::__construct();
		global $data, $system, $user;
		$user = false;
		$data = array();
		$data['page'] = "install";
		$data['notes'] = array();
		check_config_settings(); //Verify config.php setup
		//CHECK INSTALLATION
		check_installation(false); //Verify Nakid CMS is installed
		//BUILD MENU
		$data['menu'] = build_menu($user);
	}

	function index(){
		global $data, $system, $user;
		//Get db info
		$data['dbhostname'] = NAKID_DBHOSTNAME;
		$data['dbusername'] = NAKID_DBUSERNAME;
		$data['dbdatabase'] = NAKID_DBDATABASE;
		$data['dbprefix'] = NAKID_TABLE_PREFIX;
		$data['postusername'] = "admin";
		$data['postemail'] = "";
		$data['installed'] = false;
		//Form Submission
		if($this->input->post('action') && $this->input->post('action') == "install"){
			$post_username = $this->input->post('username');
			$post_password = $this->input->post('password');
			$post_email = $this->input->post('email');
			$data['postusername'] = $this->input->post('username');
			$data['postemail'] = $this->input->post('email');
			$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[25]');
			$this->form_validation->set_rules('password', 'Password', 'required|trim|matches[password_confirm]');
			$this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|trim');
			$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
			if ($this->form_validation->run() == FALSE){
				$data['notes'][] = array("<strong>Error submitting the form:</strong> <br/>".validation_errors(),"error");
			}else{
				//Data is valid, install tables
				$this->load->dbforge();
				//SETTINGS TABLE
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'name' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					),
					'value' => array(
						'type' =>'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
					),
					'description' => array(
						'type' =>'TEXT',
						'null' => FALSE
					),
					'editable' => array(
						'type' => 'INT',
						'constraint' => 2,
						'unsigned' => TRUE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('settings');
				//USERS
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'username' => array(
						'type' => 'VARCHAR',
						'constraint' => '30',
						'null' => FALSE
					),
					'password' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
					),
					'email' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
					),
					'role' => array(
						'type' => 'INT',
						'constraint' => 3,
						'unsigned' => TRUE
					),
					'fname' => array(
						'type' => 'VARCHAR',
						'constraint' => '50',
						'null' => FALSE
					),
					'lname' => array(
						'type' => 'VARCHAR',
						'constraint' => '50',
						'null' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_field("`date` TIMESTAMP NOT NULL default CURRENT_TIMESTAMP");
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('users');
				//PERMISSION CATEGORIES
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'name' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('permission_categories');
				//PERMISSION VALUES
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'cid' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					),
					'key' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					),
					'description' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('permission_values');
				//PERMISSION USERS
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'uid' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					),
					'pid' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('permission_users');
				//KEYWORDS
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'keyword' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					),
					'tool' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					),
					'tid' => array(
						'type' => 'INT',
						'constraint' => 10,
						'null' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('keywords');
				//CONTENT_BLOCKS
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'version' => array(
						'type' => 'INT',
						'constraint' => 10,
						'null' => FALSE
					),
					'editable' => array(
						'type' => 'INT',
						'constraint' => 10,
						'default' => 1,
						'null' => FALSE
					),
					'deletable' => array(
						'type' => 'INT',
						'constraint' => 10,
						'default' => 1,
						'null' => FALSE
					),
					'title' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					),
					'data' => array(
						'type' => 'TEXT',
						'null' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('content_blocks');
				//CONTENT_VERSIONS
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'block' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					),
					'content' => array(
						'type' => 'MEDIUMTEXT',
						'null' => FALSE
					),
					'type' => array(
						'type' => 'VARCHAR',
						'constraint' => '50'
					),
					'author' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_field("`date` TIMESTAMP NOT NULL default CURRENT_TIMESTAMP");
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('content_versions');
				//GALLERIES
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'title' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					),
					'description' => array(
						'type' => 'MEDIUMTEXT',
						'null' => FALSE
					),
					'data' => array(
						'type' => 'TEXT',
						'null' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('gallery_galleries');
				//GALLERY IMAGES
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'gallery' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					),
					'title' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
					),
					'file' => array(
						'type' => 'VARCHAR',
						'constraint' => '255',
						'null' => FALSE
					),
					'order' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('gallery_images');
				//GALLERY CATEGORIES
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'gallery' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					),
					'title' => array(
						'type' => 'VARCHAR',
						'constraint' => '100',
						'null' => FALSE
					),
					'order' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => FALSE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('gallery_categories');
				//GALLERY CATEGORIES
				$fields = array(
					'id' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE,
						'auto_increment' => TRUE
					),
					'gallery' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					),
					'image' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					),
					'category' => array(
						'type' => 'INT',
						'constraint' => 10,
						'unsigned' => TRUE
					)
                );
				$this->dbforge->add_field($fields);
				$this->dbforge->add_key('id', TRUE);
				$this->dbforge->create_table('gallery_image_categories');
				/*-------------------------------------------
				ADD DEFAULT SETTINGS
				--------------------------------------------*/
				//primary email
				$this->db->set('name', 'primary_email');
				$this->db->set('value', $post_email);
				$this->db->set('description', 'Primary account administrator (Any system emails will be sent here)');
				$this->db->set('editable', 1);
				$this->db->insert('settings');
				
				//from email
				$this->db->set('name', 'from_email');
				$this->db->set('value', 'info@nakid.org');
				$this->db->set('description', 'Emails from the website will come from this address');
				$this->db->set('editable', 1);
				$this->db->insert('settings');
				
				//from name
				$this->db->set('name', 'from_name');
				$this->db->set('value', 'Nakid CMS');
				$this->db->set('description', 'Emails from the website will come from this name');
				$this->db->set('editable', 1);
				$this->db->insert('settings');
				
				//include path
				$this->db->set('name', 'include_path');
				$this->db->set('value', 'cms/');
				$this->db->set('description', 'The path to include nakid FROM your website');
				$this->db->set('editable', 1);
				$this->db->insert('settings');
				/*-------------------------------------------
				ADD PERMISSION CATEGORIES
				--------------------------------------------*/
				$this->db->set('id', 1);
				$this->db->set('name', 'System');
				$this->db->insert('permission_categories');

				$this->db->set('id', 2);
				$this->db->set('name', 'Content');
				$this->db->insert('permission_categories');

				$this->db->set('id', 3);
				$this->db->set('name', 'Catalog');
				$this->db->insert('permission_categories');

				/*-------------------------------------------
				ADD PERMISSION VALUES
				--------------------------------------------*/
				$this->db->set('cid', 1);
				$this->db->set('key', 'system_settings');
				$this->db->set('description', 'Manage System Settings');
				$this->db->insert('permission_values');

				$this->db->set('cid', 1);
				$this->db->set('key', 'view_code');
				$this->db->set('description', 'View Connector Link Code');
				$this->db->insert('permission_values');

				$this->db->set('cid', 1);
				$this->db->set('key', 'manage_users');
				$this->db->set('description', 'Add, Edit, and Delete users');
				$this->db->insert('permission_values');

				$this->db->set('cid', 2);
				$this->db->set('key', 'cms_add');
				$this->db->set('description', 'Add CMS Blocks');
				$this->db->insert('permission_values');

				$this->db->set('cid', 2);
				$this->db->set('key', 'cms_edit');
				$this->db->set('description', 'Edit CMS Blocks');
				$this->db->insert('permission_values');

				$this->db->set('cid', 2);
				$this->db->set('key', 'cms_delete');
				$this->db->set('description', 'Delete CMS Blocks');
				$this->db->insert('permission_values');
				
				$this->db->set('cid', 2);
				$this->db->set('key', 'gallery');
				$this->db->set('description', 'Manage Photo Galleries');
				$this->db->insert('permission_values');
				/*-------------------------------------------
				ADD ADMINISTRATIVE USER
				--------------------------------------------*/
				$this->db->set('username', $post_username);
				$this->db->set('password', prep_password($post_password));
				$this->db->set('email', $post_email);
				$this->db->set('role', 1);
				$this->db->insert('users');
				/*-------------------------------------------
				ADD PERMISSIONS FOR USER
				--------------------------------------------*/
				$permissions_query = $this->db->get('permission_values');
				foreach($permissions_query->result() as $permission){
					$this->db->set('uid', 1);
					$this->db->set('pid', $permission->id);
					$this->db->insert('permission_users');
				}
				/*-------------------------------------------
				ADD EDITABLE HOME CONTENT
				--------------------------------------------*/
				//Content Block
				$hometitle = "_System Home";
				$homekeyword = "Nakid System Home";
				$homecontent = "<h1>Welcome</h1>";
				$homecontent .= "<p>To get started editing content, click on 'Content Editor' in the top menu under 'Tools'. You can then create a new content area to manage. You will also see a content area called '_System Home' that you can edit. Click the edit link next to the title and you can edit this page!</p>";
				//Insert Block
				$this->db->set('id', 1);
				$this->db->set('version', 1);
				$this->db->set('editable', 1);
				$this->db->set('deletable', 0);
				$this->db->set('title', $hometitle);
				$this->db->insert('content_blocks');
				//Insert Keyword
				$this->db->set('id', 1);
				$this->db->set('keyword', $homekeyword);
				$this->db->set('tool', "content");
				$this->db->set('tid', 1);
				$this->db->insert('keywords');
				//Insert Version
				$this->db->set('id', 1);
				$this->db->set('block', 1);
				$this->db->set('content', $homecontent);
				$this->db->set('type', "content");
				$this->db->set('author', 1);
				$this->db->insert('content_versions');
				//
				$data['notes'][] = array("Database Tables Installed!","message");
				//
				$data['installed'] = true;
				/*-------------------------------------------
				GET SYSTEM INFO AND SEND EMAIL
				--------------------------------------------*/
				$system = $this->SystemModel->get_settings_array();//Get array of system settings
				//SEND EMAIL TO USER WITH LOGIN INFORMATION
				$message = array();
				$message[] = "Congratulations! You have successfully setup your Nakid CMS system. To login, use the username and password below:";
				$message[] = "<strong>Username:</strong> ".$post_username;
				$message[] = "<strong>Password:</strong> ".$post_password;
				$message[] = "This password can be changed once you log in";
				$message = implode("<br/>\n",$message);
				systememail($post_email,"Your website is NAKID!",$message);
			}
		}
		$this->load->view('template',$data);
	}
}
/* End of file install.php */