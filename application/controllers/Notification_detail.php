<?php
/*
 * Home
 * Home Controller Module 
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-08
 */
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Notification_detail extends Authenticated_controller
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
        $this->load->view('notification/notification_detail_js');
        $this->load->view('notification/notification_detail');
        $this->load->view('template/footer_view');
    }

    // ดึงข้อมูลผ่าน AJAX
    public function get_data_detail() {

        $id = $this->input->post('id');
        $data = $this->home->get_notification($id);

        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode([]);
        }
    }


}
