<?php
/*
 * Reserve_holiday
 * Reservation Workday Controller  
 * @author Weradet Nopsomboon
 * @Create Date 2567-09-06
 */
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Reserve_holiday_ad extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Reserve_holiday_model', 'ram');
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
        $this->load->helper('date_helper');
    }
    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('reserve_holiday/reserve_holiday_view_js');
        $this->load->view('reserve_holiday/reserve_holiday_view');
        $this->load->view('template/footer_view');
    }
    public function get_data()
    {
        $response = [];
        $today = date('Y-m-d');
        $ck_emp_id = $this->session->userdata('emp_id');
        $data = $this->ram->get_emp_name_by_id($ck_emp_id);
        $response['status'] = "ok";
        $response['today'] = $today;
        $response['name'] = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function get_time()
    {
        $response = [];
        $trip = $this->input->post('reserve_trip');
        $shift = $this->input->post("shift");
        $ck_emp_id = $this->session->userdata('emp_id');
        $ck_emp_plant = $this->session->userdata('emp_plant');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $data = $this->ram->get_time_data_by_trip($trip, $ck_emp_com_id, $ck_emp_plant, $shift);
        $response["data"] = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function chk_trip()
    {
        $response = [];
        $trip = $this->input->post('reserve_trip');
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $ck_emp_plant = $this->session->userdata('emp_plant');
        $data = $this->ram->get_route_and_point_by_trip($ck_emp_com_id, $ck_emp_id, $ck_emp_plant);
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function chk_point()
    {
        //data session
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $reserve_route = $this->input->post('reserve_route');
        $data = $this->ram->get_point_data_by_id($ck_emp_com_id, $reserve_route);
        $response = [];
        $response = $data;
        echo json_encode($response);
    }
    public function chk_point_out()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $reserve_route_out = $this->input->post('reserve_route_out');
        $data = $this->ram->get_point_out_data_by_id($ck_emp_com_id, $reserve_route_out);
        $response = [];
        $response = $data;
        echo json_encode($response);
    }
    public function save_data_reserve()
    {
        if (!$this->session->has_userdata("emp_id")) {
            redirect("Login");
            return;
        }

        $ck_emp_id = $this->session->userdata('emp_id');
        $ck_emp_com_id = get_comopany_by_userid($ck_emp_id)->emp_com_id;
        $ck_emp_plant = $this->session->userdata('emp_plant');

        $reserve_data = $this->security->xss_clean($this->input->post([
            'reserve_date',
            'reserve_route',
            'reserve_point',
            'reserve_route_out',
            'reserve_point_out',
            'reserve_time',
            'reserve_trip',
        ]));

        // Check holiday or duplicated data
        if (
            $this->ram->get_data_week($reserve_data['reserve_date'], $ck_emp_com_id, $ck_emp_plant) == "weekday"
        ) {
            echo json_encode(['data' => "Weekday"], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode($this->ram->save_reservation($reserve_data, $ck_emp_id, $ck_emp_com_id, $ck_emp_plant), JSON_UNESCAPED_UNICODE);

    }

}
