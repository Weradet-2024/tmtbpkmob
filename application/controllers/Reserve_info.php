<?php
/*
 * Reserve_info
 * info of Reservation Controller  
 * @author Weradet Nopsomboon
 * @Create Date 2567-09-16
 */

defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Reserve_info extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Reserve_info_model');
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
        $this->load->helper('date_helper');
    }

    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('reserve_info/reserve_info_view_js');
        $this->load->view('reserve_info/reserve_info_view');
        $this->load->view('template/footer_view');
    }

    public function get_data()
    {
        $reserve_id = $this->input->post("reserve_id");
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id) ? $obj_emp_com_id->emp_com_id : '';
        $data = $this->Reserve_info_model->get_data($ck_emp_com_id, $ck_emp_id, $reserve_id);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function get_round()
    {
        $reserve_id = $this->input->post("reserve_id");
        $ck_emp_id = $this->session->userdata('emp_id');
        $ck_emp_plant_id = $this->session->userdata('emp_plant');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id) ? $obj_emp_com_id->emp_com_id : '';
        //get data
        $data = $this->Reserve_info_model->get_round($reserve_id, $ck_emp_com_id, $ck_emp_plant_id);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function get_route()
    {
        $reserve_id = $this->input->post("reserve_id");
        $reserve_shift = $this->input->post("reserve_shift");
        $ck_emp_id = $this->session->userdata('emp_id');
        $ck_emp_plant_id = $this->session->userdata('emp_plant');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id) ? $obj_emp_com_id->emp_com_id : '';
        //get_data
        $data = $this->Reserve_info_model->get_route($reserve_id, $reserve_shift, $ck_emp_com_id, $ck_emp_plant_id);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function get_point()
    {
        // รับค่า reserve_id จาก HTTP POST
        $reserve_id = $this->input->post('reserve_id');
        // เรียกใช้โมเดล rhinfo เพื่อดึงข้อมูลตาม reserve_id
        $data = $this->Reserve_info_model->get_point($reserve_id);
        // สร้างตัวแปร $response เพื่อเก็บข้อมูลผลลัพธ์
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function chk_route()
    {
        $reserve_trip = $this->input->post("reserve_trip");
        $reserve_time = $this->input->post('reserve_time');
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id) ? $obj_emp_com_id->emp_com_id : '';
        $data = $this->Reserve_info_model->chk_route($reserve_trip, $ck_emp_com_id, $ck_emp_id, $reserve_time);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function chk_point()
    {
        $reserve_trip = $this->input->post("reserve_trip");
        $reserve_route = $this->input->post("reserve_route");

        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id) ? $obj_emp_com_id->emp_com_id : '';

        $data = $this->Reserve_info_model->chk_point($reserve_trip, $reserve_route, $ck_emp_id, $ck_emp_com_id);
        $response = array();
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    public function save_reservation()
    {
        //get input 
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id) ? $obj_emp_com_id->emp_com_id : '';

        $reserve_id    = $this->security->xss_clean($this->input->post("reserve_id"));
        $reserve_date  = $this->security->xss_clean($this->input->post("reserve_date"));
        $reserve_round = $this->security->xss_clean($this->input->post("reserve_round"));
        $reserve_trip  = $this->security->xss_clean($this->input->post("reserve_trip"));
        $reserve_route = $this->security->xss_clean($this->input->post("reserve_route"));
        $reserve_point = $this->security->xss_clean($this->input->post("reserve_point"));
        $ck_emp_plant_id = $this->session->userdata('emp_plant');

         //update config check data trip dupplicate
         $chk_trip_dup = $this->Reserve_info_model->check_data_trip_dupplicate($reserve_id, $reserve_date, $reserve_trip, $ck_emp_id);
        if($chk_trip_dup){
            echo json_encode(['status' => 'ok', 'data' => 'trip_dupplicate']);
        }else{
            $data = $this->Reserve_info_model->save_reservation_data($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $ck_emp_id, $ck_emp_com_id, $ck_emp_plant_id);
            echo json_encode(['status' => 'ok', 'data' => $data]);
        }
    }
    public function cancel_reservation(){

        //get input 
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = isset($obj_emp_com_id->emp_com_id) ? $obj_emp_com_id->emp_com_id : '';
        $ck_emp_plant_id = $this->session->userdata('emp_plant');

        $reserve_id     = $this->security->xss_clean($this->input->post('reserve_id'));
        $reserve_date   = $this->security->xss_clean($this->input->post('reserve_date'));
        $reserve_round  = $this->security->xss_clean($this->input->post('reserve_round'));
        $reserve_trip   = $this->security->xss_clean($this->input->post('reserve_trip'));
        $reserve_route  = $this->security->xss_clean($this->input->post('reserve_route'));
        $reserve_point  = $this->security->xss_clean($this->input->post('reserve_point'));

        $data = $this->Reserve_info_model->cancel_reservation_data($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $ck_emp_id, $ck_emp_com_id, $ck_emp_plant_id);
        echo json_encode(['status' => 'ok', 'data' => $data]);

    }
	
}
