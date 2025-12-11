<?php
defined('BASEPATH') or edit('No direct script access allowed');

class Authentication_helper
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database(); 
    }

    public function authenticateCustomerToken()
    {
        $token = $_COOKIE["token"];
        $this->CI->db->where('mem_status', 'active');
        $this->CI->db->get('mem');
        $query = $this->CI->db->get('mem');
        foreach ($query->result() as $rs){
            $mem_id = $rs->mem_id;
            $md5_mem_id = md5($mem_id);
            if ($token==$md5_mem_id){
                $chk_mem_id = $mem_id;
            }
        }
        $arr_result = array($chk_mem_id);
        return $arr_result;
    }
}