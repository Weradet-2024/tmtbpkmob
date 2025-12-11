<?php
/*
 * Main
 * Main Controller  
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-26
 */

defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Setting_bak extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Setting_model', 'setting');
        //call libray and helper here
        $this->load->helper('utility_helper');
        $this->load->helper('company_data_helper');
    }
    public function index()
    {
        //get menu
        $this->load->view('template/header_view');
        $this->load->view('setting/setting_view_js');
        $this->load->view('setting/setting_view');
        $this->load->view('template/footer_view');
    }
    public function get_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $data = $this->setting->get_data($ck_emp_id);
        $response = [];
        $response['data'] = $data;
        echo json_encode($response);
    }
    public function ed_name()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $data = $this->setting->get_ed_name($ck_emp_id);
        $response = array();
        $response['data'] = $data;
        echo json_encode($response);
    }
    public function ud_name()
    {
        if ($this->session->has_userdata("emp_id")) {
            $ck_emp_id = $this->session->userdata('emp_id');
            $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
            $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
            $modal_ed_emp_name = $this->input->post("modal_ed_emp_name");
            $modal_ed_emp_lname = $this->input->post("modal_ed_emp_lname");
            try {
                $status = $this->setting->ud_name($modal_ed_emp_name, $modal_ed_emp_lname, $ck_emp_com_id, $ck_emp_id);
                echo $status;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            redirect('Login');
        }
    }
    public function ed_tel()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $data = $this->setting->get_ed_tel($ck_emp_id);
        $response = array();
        $response['data'] = $data;
        echo json_encode($response);
    }
    public function update_tel()
    {
        if ($this->session->has_userdata("emp_id")) {
            $emp_id = $this->input->post("emp_id");
            $emp_tel = $this->input->post("emp_tel");
            try {
                $status = $this->setting->update_tel($emp_id, $emp_tel);
            } catch (Exception $e) {

                echo $e->getMessage();
            }
            echo $status;
        } else {
            redirect('Login');
        }
    }
    public function ed_pw()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $response = array();
        $data = $this->setting->ed_pw($ck_emp_id);
        $response['data'] = $data;
        echo json_encode($response);
    }
    public function ud_pw()
    {
        if ($this->session->has_userdata("emp_id")) {
            $emp_id = $this->input->post("emp_id");
            $emp_pname = md5($this->input->post('emp_pname'));
            try {
                $status = $this->setting->update_pw($emp_id, $emp_pname);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo $status;
        } else {
            redirect('Login');
        }
    }
    public function get_close_time()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
        $curr_date = date("Y-m-d");
        $curr_date_name = date("l");
        $curr_time = date("H:i");
        $data_close_time = $this->setting->get_close_time($ck_emp_com_id, $curr_date_name);
        $data_calendar = $this->setting->get_calendar($curr_date);
        // Check if data is not null or empty
        if ($data_close_time && $data_calendar && isset($data_close_time[0]->close_time_start) && isset($data_close_time[0]->close_time_end)) {
            $data = array(
                'calendar_type' => $data_calendar[0]->calendar_type,
                'close_time_start' => $data_close_time[0]->close_time_start,
                'close_time_end' => $data_close_time[0]->close_time_end
            );
        } else {
            // Handle the case where data is null or missing
            $data = array(
                'calendar_type' => '',  // Provide default values if needed
                'close_time_start' => '',
                'close_time_end' => ''
            );
            // Optionally, log or show an error message if needed
            // echo "Data is missing or invalid.";
        }
        echo json_encode(array('result' => $data, 'status' => 'success'));
    }
    public function modal_route_chk_route()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
        $route_id = $this->input->post('route_id');
        $pickup_data = $this->setting->modal_route_chk_route($ck_emp_com_id, $route_id);
        $response = array();
        $response['data'] = $pickup_data;
        echo json_encode($response);
    }
    public function modal_route_out_chk()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
        $route_out_id = $this->input->post('route_out_id');
        $drop_data = $this->setting->modal_route_out_chk($ck_emp_com_id, $route_out_id);
        $response = array();
        $response['data'] = $drop_data;
        echo json_encode($response);
    }
    public function update_route()
    {
        if ($this->session->has_userdata("emp_id")) {
            $ck_emp_id = $this->session->userdata('emp_id');
            $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
            $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
            $modal_route_emp_id = $this->input->post('modal_route_emp_id');
            $route_id = $this->input->post('modal_route_id');
            $pickup_id = $this->input->post('modal_pick_id');
            $status = $this->setting->update_route($ck_emp_id, $ck_emp_com_id, $modal_route_emp_id, $route_id, $pickup_id);
            echo $status;
        } else {
            redirect('Login');
        }
    }
    public function update_route_out()
    {
        if ($this->session->has_userdata("emp_id")) {
            $ck_emp_id = $this->session->userdata('emp_id');
            $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
            $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
            $modal_route_out_emp_id = $this->input->post('modal_route_out_emp_id');
            $route_out_id = $this->input->post('modal_route_out_id');
            $dropoff_id = $this->input->post('modal_drop_id');
            $status = $this->setting->update_route_out($ck_emp_id, $ck_emp_com_id, $modal_route_out_emp_id, $route_out_id, $dropoff_id);
            echo $status;
        } else {
            redirect('Login');
        }
    }
}
