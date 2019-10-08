<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('dbs');
	}

	public function index()
	{
		$data['css']    = base_url() . "aset/css/arjunane-message.css?" . acak_string();
		$data['io']     = base_url() . "node_modules/socket.io-client/dist/socket.io.js";
		$data['jquery'] = base_url() . "aset/js/jquery.js";
		$data['js']     = base_url() . "aset/js/arjunane-message.js?" . acak_string();
		$data['delete'] = site_url('main/delete/del-msg/');

		$id_user		= userdata('_admin') ? decrypt(userdata('_admin')) : decrypt(userdata('_user'));

		$data_user['user'] 		 = $this->dbs->result('user');
		$data_user['link'] 		 = site_url('main/permalink/user/');
		$data_user['photo_null'] = $this->_helper()['photo_null'];
		$data_user['id']		 = $id_user;

		$data_reg['login']       = form_open('main/user/login/',   'for="login"  class="form-log-daf"');
		$data_reg['daftar']      = form_open('main/user/daftar/', 'for="daftar" class="form-log-daf"');

		
		$data_header['profil']   = $this->dbs->row_array('id', $id_user, 'user');
		$data_header['logout']   = site_url('main/logout/');

		$data_settings['profil'] 	 = $data_header['profil'];
		$data_settings['photo_null'] = $data_user['photo_null'];

		$forward['link']		= site_url('main/forward/');

		$user['settings']		 = view('_helper/settings', $data_settings, TRUE);
		$user['view_profil']	 = view('_helper/profil', '', TRUE);
		$user['forward']	 	 = view('_helper/forward', $forward, TRUE);
		$user['alert']	 		 = view('_helper/alert', '', TRUE);
		$user['preview_image']	 = view('_helper/preview-image', '', TRUE);
		$user['view_user'] 		 = view('user',   	$data_user, 	TRUE);
		$user['header']    		 = view('_helper/header', 	$data_header, 	TRUE);
		//JIKA SUDAH LOGIN MAKA AKAN DI ALIHKAN KE MENU CHAT 
		//JIKA BELUM MAKA AKAN MENAMPILKAN MENU FORM LOGIN & DAFTAR
		$data['body'] = (login_admin() || login_user()) ? view('body-msg', $user, TRUE) : view('form-reg', $data_reg, TRUE);
		view('menu', $data);
	}

	//MENGIRIMKAN PESAN
	function send_message($id = NULL)
	{
		if($this->input->is_ajax_request())
		{
			$msg['error'] = true;
			$msg['pesan'] = [];
			set_rules('_to', 'Kepada', 'numeric');
			if(valid_run())
			{
				$_self  	= userdata('_admin') ? decrypt(userdata('_admin')) : decrypt(userdata('_user'));
				$user	    = $this->dbs->row_array('id', posts('_from'), 'user');
				$check_edit = $this->dbs->check_edit($id, $_self);

				//add message
				if($user && $_self == posts('_from') && $id == NULL)
				{
					$insert 	= [
						'_to'   => posts('_to'), //kepada
						'_from' => $user['id'], //pengirim
						'photo' => insert_photo('_photo','aset/photo/chat/'),
						'text'  => posts('text-message')
					];
					$this->dbs->insert($insert, 'chat');
					$last_id	= $this->dbs->row_array('id', $this->db->insert_id(), 'chat');
					$msg['error'] = false;
					//$msg['pesan'] untuk mengirimkan data ke socket.io EMIT
					$msg['pesan'] = [
						'photo'   => ($user['photo'] != NULL) ? $user['photo'] : $this->_helper()['photo_null'], //photo user
						'msg'	  => trim(posts('text-message'), "\n"),
						'img'	  => ($last_id['photo'] != NULL) ? photo_chat($last_id['photo']) : NULL, //gambar insert chat
						'to'  	  => posts('_to'), //kepada
						'isReply' => FALSE,
						'from' 	  => $user['id'], //pengirim
						'id'	  => $last_id['id']
					];
				}
				//edit message OR reply message
				else if($_self == posts('_from') && $id != NULL)
				{
					// reply message
					if(strlen(posts('_reply')) != 0 && posts('_reply') == 'reply')
					{
						$msg['category_msg'] = 'reply';

						$insert 	= [
							'_to'   			=> posts('_to'), //kepada
							'_from' 			=> $user['id'], //pengirim
							'reply'				=> $id, // get id for reply message
							'photo' 			=> insert_photo('_photo','aset/photo/chat/'),
							'text'  			=> posts('text-message')
						];

						$this->dbs->insert($insert, 'chat');

						$last_id	= $this->dbs->row_array('id', $this->db->insert_id(), 'chat');

						$reply		= $this->dbs->row_array('id', $last_id['reply'], 'chat');

						//$msg['pesan'] untuk mengirimkan data ke socket.io EMIT
						$msg['pesan'] = [
							'photo'   	=> ($user['photo'] != NULL) ? $user['photo'] : $this->_helper()['photo_null'], //photo user
							'msg'	  	=> trim(posts('text-message'), "\n"),
							'img'	  	=> ($last_id['photo'] != NULL) ? photo_chat($last_id['photo']) : NULL, //gambar insert chat
							'reply_msg'	=> $reply['text'],
							'reply_img' => ($reply['photo'] != NULL) ? photo_chat($reply['photo']) : NULL,
							'to'  	  	=> posts('_to'), //kepada
							'from' 	  	=> $user['id'], //pengirim
							'id'	  	=> $last_id['id'],
							'isReply'   => TRUE
						];
					}
					// edit message
					else if($check_edit)
					{
						$edit		= [
							'text'  => posts('text-message')
						];
						$msg['category_msg'] = 'edit';
						$update		= $this->dbs->update('id', $id, $edit, 'chat');

						$msg['pesan'] = [
							'msg'	  =>trim(posts('text-message'), "\n"),
							'id'	  => $id
						];
					} 

					$msg['error'] = false;
				}
			}
			$msg['yhow'] = validation_errors();
			echo json_encode($msg);
		}
	}

	//MENAMPILKAN CHAT USER
	function permalink($kategori = null, $id = null)
	{
		//membuka menu chat
		if($this->input->is_ajax_request() && $kategori == 'user' && $id != null)
		{
			$chat['i_photo']      = icon('photos.png');
			$chat['i_send']      = icon('paper-plane.png');
			$chat['i_delete']    = icon('delete.png');
			$chat['photo_null']  = $this->_helper()['photo_null'];;

			$_id_self        		= userdata('_admin') ? decrypt(userdata('_admin')) : decrypt(userdata('_user'));
			$chat['_friend'] 		= $this->dbs->row_array('id', $id, 'user'); //mendapatkan data Teman (user)
			$chat['_self']   		= $this->dbs->row_array('id', $_id_self, 'user'); //mendapatkan data diri sendiri
			$chat['link_profil']    = site_url('main/permalink/profil-user/' . $chat['_friend']['username']);
			if(!$chat['_friend']) die('-=[]=-');

			$chat['delete']		 	= site_url('main/delete/del-all-msg/' . $chat['_friend']['id']);

			$chat['right_position'] = "";
			$chat['isSelf'] 		= "";
			$chat['photo']			= ($chat['_self']['photo'] != NULL) ? $chat['_self']['photo'] : $chat['photo_null'];
			$chat['form']    		= form_open_multipart('main/send-message/', 'class="form-send" category="add" data-id=""');
			$chat['chat']    		= $this->dbs->chat($chat['_self']['id'], $chat['_friend']['id']);

			$data['menu'] 			= view('chat', $chat, TRUE);	
			
			echo json_encode($data);
		
		}
		elseif($this->input->is_ajax_request() && $kategori == 'profil-user' && $id != null)
		{
			$profil 	 = $this->dbs->row_array('username', $id, 'user');
			$photo_null  = $this->_helper()['photo_null'];
			if(!$profil) die('-=[]=-');
			$data   = [
				'name' 		=> $profil['name'],
				'username'  => $profil['username'],
				'status'	=> $this->_helper()['level'][$profil['level']],
				'photo'     => ($profil['photo'] != null) ? $profil['photo'] : $photo_null,
			];
			echo json_encode($data);
		}
	}

	protected function _helper()
	{
		$data['level'] 		= [ 1 => 'Admin', 2 => 'User'];
		$data['photo_null'] = 'http://files.softicons.com/download/toolbar-icons/vista-people-icons-by-icons-land/png/256x256/Age/Child_Male_Light.png';
		return $data;
	}

	//LOGIN & DAFTAR
	function user($kategori = null)
	{
		if($this->input->is_ajax_request() && $kategori == 'login')
		{

			$data['redirect'] = null; //redirect ke halaman menu utama jika login berhasil
			$data['login']    = FALSE; //jika user berhasil login, maka "redirect" akan di alihkan ke menu utama
			set_rules('_name', 'Nama',     'required');
			set_rules('_pass', 'Password', 'required');
			$data['error'] = TRUE;
			$data['msg_error'] = "";
			if(valid_run())
			{
				$check = $this->dbs->login(posts('_name', TRUE), posts('_pass', TRUE));
				if($check) 
				{
					$data['error'] 	  = FALSE;
					$data['login'] 	  = TRUE;
					$data['redirect'] = site_url();
					$level 			  = $check['level'] == 1 ? '_admin' : '_user';
					set_userdata($level, encrypt($check['id']));
				}
				else
				{
					$data['msg_error'] = 'Username || Password Salah !!!';
				}
			}
			$data['error'] = [
				'name'   => form_error('_name'),
				'pass'   => form_error('_pass'),
			];
			echo json_encode($data);
		}
		else if($this->input->is_ajax_request() && $kategori == 'daftar')
		{
			$data['redirect'] = null; //redirect ke halaman menu utama jika login berhasil
			$data['login']    = FALSE; //jika user baru daftar, dan berhasil daftar maka akan di alihkan ke menu login. Dimana nilai $login = TRUE (akan di alihkan)
			set_rules('_name', 'Nama',     'required|max_length[10]');
			set_rules('_uname', 'Usernama',     'required');
			set_rules('_pass', 'Password', 'required|matches[_repass]');
			set_rules('_repass', 'Re-Password', 'required');
			set_error_delimiters('<div class="info-error"><span>','</span></div>');
			$data['error'] = TRUE;
			$data['msg_error'] = "";
			if(valid_run())
			{
				$check = $this->dbs->row_array('username', posts('_uname', TRUE), 'user');
				if(!$check) 
				{
					$data['error'] 	  = FALSE;
					$data['login'] 	  = TRUE;
					$insert = [
						'name' 	   => 		  posts('_name',  TRUE),
						'username' => 		  posts('_uname', TRUE),
						'password' => encrypt(posts('_pass',  TRUE)),
						'level'    => 2
					];
					$this->dbs->insert($insert, 'user');
				}
				else
				{
					$data['msg_error'] = 'Username ' . posts('_uname', TRUE) . 'sudah ada !!!';
				}
			}
			$data['error'] = [
				'name'   => form_error('_name'),
				'uname'  => form_error('_uname'),
				'pass'   => form_error('_pass'),
				'repass' => form_error('_repass'),
			];
			
			echo json_encode($data);
		}
	}

	function forward($id_msg = NULL, $id_users = NULL)
	{
		if($id_msg != NULL && $id_users != NULL && $this->input->is_ajax_request())
		{
			// explode $id_users by "-" to get each id
			$ex = explode("-", $id_users);

			$get_data 				= $this->dbs->row_array('id', $id_msg, 'chat');
			$msg['msg']				= $get_data['text'];
			$msg['photo_msg']		= ($get_data['photo'] != NULL) ? photo_chat($get_data['photo']) : NULL;

			$id_from				= userdata('_admin') ? decrypt(userdata('_admin')) : decrypt(userdata('_user'));
			$data_from				= $this->dbs->row_array('id', $id_from, 'user');
			$msg['name']			= $data_from['name'];
			$msg['from']			= $data_from['id'];
			$msg['photo']			= ($data_from['photo'] != NULL) ? $data_from['photo'] : $this->_helper()['photo_null'];
			 
			$msg['id_user']    		= [];
			$msg['isReply']			= FALSE;
			for($i = 0; $i < count($ex); $i++)
			{
				$msg['id_user'][] 	= $ex[$i];

				// insert chat forward
				$insert	= [
					'_from' => $data_from['id'],
					'_to'   => $ex[$i],
					'text'	=> $get_data['text'],
					'photo' => $get_data['photo']
				];
				$this->dbs->insert($insert, 'chat');
			}
			echo json_encode($msg);
		}
	}

	function delete($kategori = null, $id = null)
	{
		if(($kategori == 'del-all-msg' && $id != null) && $this->input->is_ajax_request())
		{
			$id_self 	 = userdata('_admin') ? decrypt(userdata('_admin')) : decrypt(userdata('_user'));
			$delete 	 = $this->dbs->delete_all_chat($id, $id_self);
			$data['msg'] = $delete ? 'success' : 'error';
			echo json_encode($data);
		}
		elseif(($kategori == 'del-msg' && $id != null) && $this->input->is_ajax_request())
		{
			$id_self 	   = userdata('_admin') ? decrypt(userdata('_admin')) : decrypt(userdata('_user'));
			$delete 	   = $this->dbs->delete_chat($id, $id_self);
			$data['msg']   = $delete ? 'success' : 'error';
			$data['pesan'] = $delete ? ['id' => $id, 'msg' => "Pesan telah dihapus."] : ['id' => NULL, "msg" => NULL];
			echo json_encode($data);
		}
	}

	function logout()
	{
		$this->session->sess_destroy();
		redirect();
	}
	
}
