<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Tracking_info extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
        $this->load->helper('date_helper');
        //call model here
        $this->load->model('Tracking_info_model', 'tim');
    }

    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('tracking_info/tracking_info_view_js');
        $this->load->view('tracking_info/tracking_info_view');
        $this->load->view('template/footer_view');
    }

    public function get_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $tracking_date = $this->input->post('tracking_date');
        $tracking_time = $this->input->post('tracking_time');
        $tracking_trip = $this->input->post('tracking_trip');
        $tracking_route = $this->input->post('tracking_route');
        $tracking_veh_id = $this->input->post('tracking_veh_id');
        $tracking_drv_id = $this->input->post('tracking_drv_id');

        $data = $this->tim->get_data(
            $ck_emp_id,
            $ck_emp_com_id,
            $tracking_date,
            $tracking_time,
            $tracking_trip,
            $tracking_route,
            $tracking_veh_id,
            $tracking_drv_id
        );

        $response = array();

        $response = $data;

        echo json_encode($response);
    }
    public function get_location()
    {

        $ck_emp_id = $this->session->userdata('emp_id');
        $ck_emp_plant = $this->session->userdata('emp_plant');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;

        $tracking_date = $this->input->post('tracking_date');
        $tracking_time = $this->input->post('tracking_time');
        $tracking_trip = $this->input->post('tracking_trip');
        $tracking_route = $this->input->post('tracking_route');
        $tracking_veh_id = $this->input->post('tracking_veh_id');
        $tracking_drv_id = $this->input->post('tracking_drv_id');

        $data = $this->tim->get_data_location(
            $ck_emp_id,
            $ck_emp_com_id,
            $ck_emp_plant,
            $tracking_date,
            $tracking_time,
            $tracking_trip,
            $tracking_route,
            $tracking_veh_id,
            $tracking_drv_id
        );

        $response = [];

        $response = $data;

        echo json_encode($response);
    }
    public function get_bus_location()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;

        $tracking_date = $this->input->post('tracking_date');
        $tracking_time = $this->input->post('tracking_time');
        $tracking_trip = $this->input->post('tracking_trip');
        $tracking_route = $this->input->post('tracking_route');
        $tracking_veh_id = $this->input->post('tracking_veh_id');
        $tracking_drv_id = $this->input->post('tracking_drv_id');
        $reserve_point = $this->input->post("reserve_point");

        $data = $this->tim->get_bus_location(
            $ck_emp_id,
            $ck_emp_com_id,
            $tracking_date,
            $tracking_time,
            $tracking_trip,
            $tracking_route,
            $tracking_veh_id,
            $tracking_drv_id,
            $reserve_point
        );

        echo json_encode($data);
    }

    public function get_company()
    {

        $ck_emp_id = $this->session->userdata('emp_id');
        $ck_emp_plant = $this->session->userdata('emp_plant');
        $data = $this->tim->get_company_data($ck_emp_plant);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function get_plant()
    {
        $departure_route = $this->input->post('departure_route');
        $ck_emp_id = $this->session->userdata('emp_id');
        $emp_lang = $this->input->cookie("ck_emp_lang");
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $data = $this->tim->get_plant_data($departure_route, $emp_lang);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function get_point_reserve()
    {

        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;

        $tracking_date = $this->input->post('tracking_date');
        $tracking_time = $this->input->post('tracking_time');
        $tracking_trip = $this->input->post('tracking_trip');
        $tracking_route = $this->input->post('tracking_route');
        $tracking_veh_id = $this->input->post('tracking_veh_id');
        $tracking_drv_id = $this->input->post('tracking_drv_id');

        $data = $this->tim->get_point_reserve(
            $ck_emp_id,
            $tracking_date,
            $tracking_time,
            $tracking_trip,
            $tracking_route,
            $tracking_veh_id,
            $tracking_drv_id
        );

        echo json_encode($data);
    }
}
