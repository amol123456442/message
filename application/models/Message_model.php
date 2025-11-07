<?php
class Message_model extends CI_Model
{

    public function send_message($sender_id, $receiver_id, $message)
    {
        $this->db->insert('messages', [
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);
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
