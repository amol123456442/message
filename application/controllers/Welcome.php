<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['User_model', 'Message_model']);
		$this->load->library('session');
		$this->load->helper('url');

		if (!$this->session->userdata('user_id')) {
			redirect('auth/login');
		}
	}

	public function index()
	{
		$user_id = $this->session->userdata('user_id');
		$data['user_name'] = $this->session->userdata('full_name');
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
	public function check_new_message()
	{
		$user_id = $this->session->userdata('user_id');
		$message = $this->Message_model->get_latest_unread_message($user_id);

		if ($message) {
			$this->Message_model->mark_as_read($message->id);
			echo json_encode([
				'new_message' => true,
				'sender_name' => $this->User_model->get_user_name($message->sender_id),
				'message' => $message->message
			]);
		} else {
			echo json_encode(['new_message' => false]);
		}
	}

	// Welcome.php controller mein add karo

	public function save_subscription()
	{
		$input = json_decode(file_get_contents('php://input'));
		$user_id = $this->session->userdata('user_id');

		$data = [
			'user_id' => $user_id,
			'endpoint' => $input->endpoint,
			'keys' => json_encode($input->keys)
		];

		$this->db->replace('push_subscriptions', $data);
	}

	// Send push to all devices of receiver
	private function sendPushNotification($receiver_id, $sender_name, $message)
	{
		$this->db->where('user_id', $receiver_id);
		$subs = $this->db->get('push_subscriptions')->result();

		foreach ($subs as $sub) {
			$subscription = [
				'endpoint' => $sub->endpoint,
				'keys' => json_decode($sub->keys)
			];

			$payload = json_encode([
				'sender' => $sender_name,
				'message' => $message
			]);

			$this->sendWebPush($subscription, $payload);
		}
	}

	private function sendWebPush($subscription, $payload)
	{
		$endpoint = $subscription['endpoint'];
		$auth = $subscription['keys']->auth;
		$p256dh = $subscription['keys']->p256dh;

		$vapidPublicKey = 'YOUR_PUBLIC_VAPID_KEY';
		$vapidPrivateKey = 'YOUR_PRIVATE_VAPID_KEY';

		$headers = [
			'Authorization: WebPush ' . $this->generateVapidToken($endpoint, $vapidPublicKey, $vapidPrivateKey),
			'Content-Type: application/octet-stream',
			'TTL: 2419200'
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_exec($ch);
		curl_close($ch);
	}

	private function generateVapidToken($audience, $publicKey, $privateKey)
	{
		// Simple VAPID generation (use library in production)
		// For now, we'll skip full JWT - use web-push-php library
		return 'vapid t=ey...'; // better to use library
	}
}
