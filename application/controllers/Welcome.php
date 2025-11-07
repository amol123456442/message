<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['User_model', 'Message_model']);
		$this->load->library(['session', 'upload']);
		$this->load->helper(['url', 'form']);

		if (!$this->session->userdata('user_id')) {
			redirect('auth/login');
		}
	}

	public function index()
{
    $user_id = $this->session->userdata('user_id');
    $data['user_name'] = $this->session->userdata('user_name'); // ✅ use 'user_name'
    $data['users'] = $this->User_model->get_all_users_except($user_id);
    $this->load->view('home', $data);
}
	public function get_messages()
	{
		$user_id = $this->session->userdata('user_id');
		$receiver_id = $this->input->get('receiver_id');
		$messages = $this->Message_model->get_conversation($user_id, $receiver_id);
		echo json_encode($messages);
	}

	public function send_message()
	{
		$sender_id = $this->session->userdata('user_id');
		$receiver_id = $this->input->post('receiver_id');
		$message = $this->input->post('message');

		$this->Message_model->send_message($sender_id, $receiver_id, $message);
		echo json_encode(['status' => 'success']);
	}

	// 📎 File Upload API
	public function send_file_message()
	{
		$sender_id = $this->session->userdata('user_id');
		$receiver_id = $this->input->post('receiver_id');

		if (empty($receiver_id)) {
			echo json_encode(['status' => 'error', 'msg' => 'Receiver not selected']);
			return;
		}

		$config['upload_path']   = './uploads/';
		$config['allowed_types'] = 'jpg|jpeg|png|gif|mp4|mp3|wav|pdf|doc|docx|xls|xlsx|ppt|pptx';
		$config['max_size']      = 10000;
		$config['encrypt_name']  = TRUE;

		if (!is_dir('./uploads')) mkdir('./uploads', 0777, true);

		$this->upload->initialize($config);

		if (!$this->upload->do_upload('file')) {
			echo json_encode(['status' => 'error', 'msg' => $this->upload->display_errors()]);
			return;
		}

		$fileData = $this->upload->data();
		$file_url = base_url('uploads/' . $fileData['file_name']);
		$this->Message_model->send_message($sender_id, $receiver_id, $file_url);

		echo json_encode(['status' => 'success', 'file_url' => $file_url]);
	}
}
