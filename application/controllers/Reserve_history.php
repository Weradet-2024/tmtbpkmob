<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Reserve_history extends Authenticated_controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('cookie');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Bangkok');
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
        $this->load->helper('date_helper');
        //call model here
        $this->load->model('Reserve_history_model', 'rhm');
        $this->load->library('session');
        $this->load->library('Language_lib');
        $this->language_lib->setLanguage();
    }

    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('reserve_history/reserve_history_view_js');
        $this->load->view('reserve_history/reserve_history_view');
        $this->load->view('template/footer_view');
    }

    public function chkFeature(){
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $ck_emp_plant_id = $this->session->userdata('emp_plant');
        $result = $this->rhm->chkFeature($ck_emp_com_id,$ck_emp_plant_id);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function get_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $data = $this->rhm->get_data($ck_emp_id, $ck_emp_com_id);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function get_trip_out_time(){
        $ck_emp_id      = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $data = $this->rhm->get_trip_out_time_data($ck_emp_com_id);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function chk_val()
    {
        $get_status     = $this->input->post('get_status');
        $get_date_start = $this->input->post('get_date_start');
        $get_date_end   = $this->input->post('get_date_end');
        $ck_emp_id      = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $data = $this->rhm->chk_val($get_status, $get_date_start, $get_date_end, $ck_emp_id, $ck_emp_com_id);
        $response = [];
        $response = $data;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function cancel_select()
    {

        $ck_emp_id      = $this->session->userdata('emp_id');
        $obj_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $obj_emp_com_id->emp_com_id;
        $hdn_max_line  = $this->input->post('hdnMaxLine');

        //define varible
        $chk_empty = 0;
        $curr_date = date('Y-m-d');
        $curr_time = date('H:i:s');

        for ($i = 1; $i <= $hdn_max_line; $i++) {
            $tmp_chk_reserve = $this->input->post('chk_reserve_' . $i);
            if ($tmp_chk_reserve != "") {
                $chk_empty++;
            }
        }

        if ($chk_empty == 0) {

            echo json_encode(['message' => 'not_select'], JSON_UNESCAPED_UNICODE);

        } else {

            //get company end time
            $data_com_time = $this->rhm->get_company_end_time($ck_emp_com_id);

            foreach ($data_com_time as $rs) {

                $company_end_time = $rs->company_end_time;
                $company_finished_time = $rs->company_finished_time;
            }

            $chk_error_date = 0;

            //check error data
            for ($i = 1; $i <= $hdn_max_line; $i++) {

                $get_chk_reserve = $this->input->post('chk_reserve_', $i);

                if ($get_chk_reserve != "") {

                    $data_reserve = $this->rhm->get_reserve_date($ck_emp_com_id, $get_chk_reserve);

                    foreach ($data_reserve as $rs1) {
                        $reserve_date = $rs1->reserve_date;
                    } //foreach

                    if ($reserve_date < $curr_date) {
                        $chk_error_date++;
                    }
                } //check get_chk_reserve

            } //for

            if ($chk_error_date > 0) {
             
                echo json_encode(['message' => 'before_date'], JSON_UNESCAPED_UNICODE);
            } else {

                //define varible
                $chk_error_time = 0;

                for ($i = 1; $i <= $hdn_max_line; $i++) {

                    $get_reserve_id = $this->input->post('chk_reserve_' . $i);


                    $data_reserve = $this->rhm->get_reserve_date($ck_emp_com_id, $get_reserve_id);

                    $rs2_reserve_date = '';
                    foreach ($data_reserve as $rs1) {

                        $rs2_reserve_date = $rs1->reserve_date;
                    } //foreach

                    if ($rs2_reserve_date == $curr_date) {

                        if ($curr_time >= $company_end_time && $curr_time <= $company_finished_time) {

                            $chk_error_time++;
                        } else {

                            //update data here
                            $status = $this->rhm->cancel_select($curr_date, $curr_time, $ck_emp_id, $ck_emp_com_id, $get_reserve_id);

                            if ($status != true) {

                                echo json_encode(['message' => 'Error'], JSON_UNESCAPED_UNICODE);

                                break;
                            }
                        }
                    } else if ($rs2_reserve_date > $curr_date) {

                        //update data here
                        $status = $this->rhm->cancel_select($curr_date, $curr_time, $ck_emp_id, $ck_emp_com_id, $get_reserve_id);

                        if ($status != true) {

                            echo json_encode(['message' => 'Error'], JSON_UNESCAPED_UNICODE);

                            break;
                        }
                    } // else if
                } //for

                if($chk_error_time <= 0){

                    echo json_encode(['message' => '1'], JSON_UNESCAPED_UNICODE);

                }else{

                    $response = [];
                    $response['chk_error_time'] = $chk_error_time;
                    $response['curr_date']      = $curr_date;

                }

            }
        }

        //$status = $this->rhm->cancel_select($ck_emp_id, $ck_emp_com_id, $hdn_max_line);

    }
}
