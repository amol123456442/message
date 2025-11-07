<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Message_model extends CI_Model
{

    public function send_message($sender_id, $receiver_id, $message)
    {
        $this->db->insert('messages', [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    // public function get_conversation($user_id, $receiver_id)
    // {
    //     $this->db->where("(sender_id = $user_id AND receiver_id = $receiver_id) 
    //                     OR (sender_id = $receiver_id AND receiver_id = $user_id)");
    //     $this->db->order_by('created_at', 'ASC');
    //     return $this->db->get('messages')->result();
    // }

    public function get_latest_unread_message($user_id)
    {
        $this->db->where('receiver_id', $user_id);
        $this->db->where('is_read', 0);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        return $this->db->get('messages')->row();
    }

    public function mark_as_read($id)
    {
        $this->db->where('id', $id);
        $this->db->update('messages', ['is_read' => 1]);
    }

    public function get_conversation($user_id, $receiver_id)
    {
        $this->db->select('m.*, u.full_name as sender_name');
        $this->db->from('messages m');
        $this->db->join('users u', 'u.id = m.sender_id');
        $this->db->where("(m.sender_id = $user_id AND m.receiver_id = $receiver_id)
                   OR (m.sender_id = $receiver_id AND m.receiver_id = $user_id)");
        $this->db->order_by('m.created_at', 'ASC');
        return $this->db->get()->result();
    }
}
