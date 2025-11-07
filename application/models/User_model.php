<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function register($data) {
        return $this->db->insert('users', $data);
    }

    public function login($employee_id, $password) {
        $query = $this->db->get_where('users', ['employee_id' => $employee_id]);
        $user = $query->row();
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function get_all_users_except($id) {
        return $this->db->where('id !=', $id)->get('users')->result();
    }

    public function get_user_name($user_id) {
        $query = $this->db->select('full_name')->where('id', $user_id)->get('users')->row();
        return $query ? $query->full_name : 'Unknown';
    }
    
}
