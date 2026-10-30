<?php
class Authenticated_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
        $this->check_session();
        $this->load->helper('cookie');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Bangkok');
        $this->load->library('session');
        $this->load->library('Language_lib');
        $this->language_lib->setLanguage();
    }
    public function check_session()
    {
        $session_id = session_id();
        $emp_id = $this->session->userdata('emp_id');
        // ตรวจสอบคุกกี้ "Remember Me"
        if (get_cookie('remember_me')) {
            $token = get_cookie('remember_me');
            $lang = get_cookie('ck_emp_lang');
            $cookie_expiration = 60 * 60 * 24 * 30;
            $this->input->set_cookie('ck_emp_lang', $lang, $cookie_expiration);

            $user = $this->Login_model->get_user_by_token($token);
            if ($user) {
                // ทำการล็อกอินอัตโนมัติ
                $this->session->set_userdata('emp_id', $user->emp_id);
                $this->session->set_userdata('emp_plant', $user->emp_plant);
                $session_id = session_id();
                $this->Login_model->create_session($user->emp_id, $session_id);
                $this->Login_model->update_login_status($user->emp_id, true);
                $this->Login_model->update_last_activity($session_id);
                return;
            }
        }
        if ($emp_id) {
            // ตรวจสอบเซสชันที่ยังใช้งานอยู่
            $active_session = $this->Login_model->get_active_session($emp_id);
            if ($active_session && $active_session->session_id == $session_id) {
                // อัปเดตเวลาของเซสชัน

                $token = get_cookie('remember_me');
                $lang = get_cookie('ck_emp_lang');
                $cookie_expiration = 60 * 60 * 24 * 30;
                $this->input->set_cookie('ck_emp_lang', $lang, $cookie_expiration);
                $this->Login_model->update_last_activity($session_id);
            } else {
                // เซสชันหมดอายุ
                $this->Login_model->delete_session($emp_id);
                $this->session->sess_destroy();
                //echo "Session หมดอายุ" . '  ' . $active_session->session_id . '//' . (time() - $this->config->item('sess_expiration')) . $session_id;
                redirect('Login');
            }
        } else {
            // ถ้าไม่มีเซสชัน ให้กลับไปที่หน้าล็อกอิน
            //echo "No session";
            redirect('Login');
        }
    }
}
?>