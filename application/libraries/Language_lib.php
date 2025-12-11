<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Language_lib
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->helper('sessionLang');
    }

    public function setLanguage()
    {
        //$sessionId = get_cookie("token");
        $sessionLang = get_cookie("ck_emp_lang");

        // if (!isset($sessionId)) {
        //     redirect('/Main');
        // }

        $lang = $this->CI->input->cookie('ck_emp_lang');

        if ($lang === 'th') {
            $this->CI->session->set_userdata('ck_emp_lang', 'thai');
        } elseif ($lang === 'en') {
            $this->CI->session->set_userdata('ck_emp_lang', 'english');
        } else {
        
            $this->CI->session->set_userdata('ck_emp_lang', 'english');
        }

        $this->CI->lang->load('language_lib', $this->CI->session->userdata('ck_emp_lang'));
    }
}
?>
