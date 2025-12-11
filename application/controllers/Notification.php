<?php
/*
 * Home
 * Home Controller Module 
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-08
 */
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Notification extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        //call model here
        $this->load->model('Notification_model', 'home');
        $this->load->helper('date_helper');
    }
    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('notification/notification_js');
        $this->load->view('notification/notification');
        $this->load->view('template/footer_view');
    }

    public function get_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $data = $this->home->get_notification_data($ck_emp_id);
        $ck_emp_lang = $this->input->cookie('ck_emp_lang');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}
