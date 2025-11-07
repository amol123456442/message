<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function register($data) {
        $data['last_seen'] = date('Y-m-d H:i:s');
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
        $this->db->select('id, full_name, department, last_seen');
        $this->db->where('id !=', $id);
        $this->db->order_by('full_name', 'ASC');
        return $this->db->get('users')->result();
    }

    public function update_last_seen($user_id) {
        $this->db->where('id', $user_id);
        $this->db->update('users', ['last_seen' => date('Y-m-d H:i:s')]);
    }

    public function is_online($user_id) {
        $this->db->select('last_seen');
        $this->db->where('id', $user_id);
        $query = $this->db->get('users');
        $user = $query->row();
        if (!$user || !$user->last_seen) return false;
        $last_seen = strtotime($user->last_seen);
        return (time() - $last_seen) < 60; // 60 seconds = online
    }
}