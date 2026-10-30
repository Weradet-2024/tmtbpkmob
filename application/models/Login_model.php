<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-08
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Login_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function get_login_data($emp_email, $emp_pname, $remember)
    {

        $username = trim($emp_email);
        $trm_emp_pname = trim($emp_pname);
        // $this->db->where('emp_code', $username);
		$this->db->where('emp_uname', $username);
        $this->db->where('emp_status', 'active');
        $user = $this->db->get('emp')->row();
        if ($user && md5($trm_emp_pname) == $user->emp_pname) {
            return $user;
        } else {
            return "ng";
        }
    }

    public function get_active_session($user_id)
    {
        $this->db->where('user_id', $user_id);

        // ตรวจสอบให้แน่ใจว่า last_activity ยังไม่เกินเวลาที่กำหนดใน sess_expiration
        $this->db->where('last_activity >', time() - $this->config->item('sess_expiration'));

        // ตรวจสอบให้แน่ใจว่า last_activity เป็นของวันเดียวกันกับปัจจุบัน
        $today_start = strtotime('today'); // เวลาเริ่มต้นของวันนี้ (00:00:00)
        $this->db->where('last_activity >=', $today_start);

        // ดึงข้อมูลเซสชันที่ตรงกับเงื่อนไข
        return $this->db->get('user_sessions')->row();
    }


    public function create_session($user_id, $session_id)
    {
        $data = array(
            'user_id' => $user_id,
            'session_id' => $session_id,
            'last_activity' => time()
        );
        $this->db->insert('user_sessions', $data);
    }

    public function update_last_activity($session_id)
    {
        $this->db->where('session_id', $session_id);
        $this->db->update('user_sessions', array('last_activity' => time()));
    }

    public function delete_session($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('user_sessions');
    }

    public function update_login_status($user_id, $status)
    {
        $this->db->where('emp_id', $user_id);
        $this->db->update('emp', array('is_logged_in' => $status));
    }

    public function update_remember_token($user_id, $token)
    {
        $this->db->where('emp_id', $user_id);
        $this->db->update('emp', array('remember_token_mobile' => $token));
    }

    public function get_user_by_token($token)
    {
        $this->db->where('remember_token_mobile', $token);
        return $this->db->get('emp')->row();
    }


    //added date 2025-06-17
    // check user employee email
    //author: Weradet Nopsomboon

    function check_email($user_email)
    {
        $sql = "SELECT * FROM emp WHERE emp_email = ?";

        $query = $this->db->query($sql, array($user_email));

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function update_pass_token($token, $emp_id)
    {
        $this->db->where('emp_id', $emp_id);
        $this->db->set('emp_forgot_token', $token);
        return $this->db->update('emp');
    }


}

