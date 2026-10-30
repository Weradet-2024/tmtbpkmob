<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Attendance_result extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_result_model', 'arm');
        $this->load->helper('company_data_helper');
    }
    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('attendance/attendance_result_view_js');
        $this->load->view('attendance/attendance_result');
        $this->load->view('template/footer_view');
    }
    public function get_data(){
        //get session data
        $ck_emp_id = $this->session->userdata('emp_id');
        $chk_com_id = get_comopany_by_userid($ck_emp_id);
        $key = $this->input->post('key');
        $data = explode("_", base64_decode($key));

        //print_r($data);

        if($data[2] != "TMT"){ //check data in vendor
            echo "Wrong_QRcode";
        }else{
            $status = $this->arm->get_data($data, $ck_emp_id, $chk_com_id->emp_com_id);
            if($status == "bus_service"){
                echo "bus_service";
            }else{
                echo json_encode($status);
            }
        }
    }
}
