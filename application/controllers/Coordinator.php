<?php
/*
 * Coordinator
 * Coordinator Controller Module 
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-23
 */
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Coordinator extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Bangkok');
        //call model here
        $this->load->model('Coordinator_model', 'coordinator');
        $this->load->model('Login_model');
        //call libray here
        $this->load->library('authentication_helper');
        $this->load->library('session');
        $this->load->library('Language_lib');
        $this->language_lib->setLanguage();
    }
    public function index()
    {
        //get menu
        $this->load->view('template/header_view');
        $this->load->view('coordinator/coordinator_js');
        $this->load->view('coordinator/coordinator');
        $this->load->view('template/footer_view');
    }
    public function get_data()
    {
        $response = [];
        $ck_emp_plant = $this->session->userdata('emp_plant');
        $data = $this->coordinator->get_coordinator_data($ck_emp_plant);
        $ck_emp_lang = $this->input->cookie('ck_emp_lang');
        $arr_result = [];
        foreach ($data as $rs) {
            $row = [];
            if ($ck_emp_lang == "th") {
                $coordinator_name = $rs->coordinator_name_th;
            } else {
                $coordinator_name = $rs->coordinator_name_en;
            }
            $row['coordinator_name'] = $coordinator_name;
            $row['coordinator_tel'] = $rs->coordinator_tel;
            $row['coordinator_type'] = $rs->coordinator_type;
            $arr_result[] = $row;
        }
        $response['status'] = "ok";
        $response['data'] = $arr_result;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
