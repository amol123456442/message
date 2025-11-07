<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
    }

    public function login() {
        $this->load->view('login');
    }

    public function register() {
        $this->load->view('register');
    }

  public function register_action() {
    $data = [
        'full_name' => $this->input->post('full_name'),
        'email' => $this->input->post('email'),
        'employee_id' => $this->input->post('employee_id'),
        'department' => $this->input->post('department'),
        'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
    ];

    $this->User_model->register($data);
    $this->session->set_flashdata('success', 'Registration successful!');
    redirect('auth/login');
}

    public function login_action() {
        $employee_id = $this->input->post('employee_id');
        $password = $this->input->post('password');

        $user = $this->User_model->login($employee_id, $password);
        if ($user) {
            $this->session->set_userdata([
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'logged_in' => TRUE
            ]);
            redirect('welcome');
        } else {
            $this->session->set_flashdata('error', 'Invalid Employee ID or Password');
            redirect('auth/login');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
    
}
