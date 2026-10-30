<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Tracking_manual extends Authenticated_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
        $this->load->helper('date_helper');
        //call model here
        $this->load->model('Tracking_manual_model', 'tmm');
    }

    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('tracking_manual/tracking_manual_view_js');
        $this->load->view('tracking_manual/tracking_manual_view');
        $this->load->view('template/footer_view');
    }

    public function get_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id)? $obj_emp_com_id->emp_com_id : '';
        $ck_emp_lang = $this->input->cookie('lang');
        $data_plant = $this->tmm->get_plant_data();
        $response = [];
        $response['plant'] = $data_plant;
        $response['trip'] = ['in', 'out'];
        if ($ck_emp_lang == "th") {
            $response['date'] = format_week_date_th_curr_date();
        } else {
            $response['date'] = format_week_date_en_curr_date();
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function get_route(){
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id)? $obj_emp_com_id->emp_com_id : '';
        $ck_emp_lang = $this->input->cookie('lang');
        $plant_id =  $this->session->userdata('emp_plant');
        $trip = $this->input->post('trip');
        $response = [];
        $route_data = $this->tmm->get_data_route_by_trip($ck_emp_com_id, $trip, $plant_id);
        $response['data'] = $route_data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function chk_active_route()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id)? $obj_emp_com_id->emp_com_id : '';
        $trip = $this->input->post('trip');
        $plant_id =  $this->session->userdata('emp_plant');
        $route = $this->input->post('route');
        $curr_date = date('Y-m-d');
        $curr_time = date('H:i:s');

        $response = [];
        $data = $this->tmm->chk_active_route($ck_emp_com_id, $trip, $route, $curr_date, $curr_time, $plant_id);
        $response = $data;

        if ($data == "Cannot") {

            echo "Cannot";

        } else {

            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        }

    }
}
