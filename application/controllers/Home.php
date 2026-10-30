<?php
/*
 * Home
 * Home Controller Module 
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-08
 */
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Home extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        //call model here
        $this->load->model('Home_model', 'home');
        $this->load->helper('date_helper');
    }
    public function index()
    {
        //get menu
        $data = [];
        $data_menu = $this->home->get_menu_data();
        $data['menu'] = $data_menu;
        $this->load->view('template/header_view');
        $this->load->view('home/home_js');
        $this->load->view('home/home', $data);
        $this->load->view('template/footer_view');
    }
    public function chk_lang()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $set_lang = $this->input->post("set_lang");
        $this->input->set_cookie("ck_emp_lang", $set_lang, time() + 315360000);
        try {
            $data = $this->home->chk_lang($set_lang, $ck_emp_id);
            if ($data) {
                echo json_encode(['status' => 'ok']);
            } else {
                echo json_encode(['status' => 'error']);
            }
        } catch (Exception $e) {
            echo "Error:" . $e->getMessage();
        }
    }
    public function get_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $data = $this->home->get_data($ck_emp_id);
        $ck_emp_lang = $this->input->cookie('ck_emp_lang');
        $data['lang'] = $ck_emp_lang;
        if ($ck_emp_lang == "th") {
            $data['date'] = format_week_date_th_curr_date();
        } else {
            $data['date'] = format_week_date_en_curr_date();
        }
        echo json_encode($data);
    }
    public function get_trip()
    {
        $depature_date = date('Y-m-d');
        $ck_emp_id = $this->session->userdata('emp_id');
        $response = [];
        $data_trip = $this->home->get_trip($depature_date, $ck_emp_id);
        $ck_emp_lang = $this->input->cookie('ck_emp_lang');
        $response['lang'] = $ck_emp_lang;
        $response['data'] = $data_trip;
        echo json_encode($response);
    }

    public function get_tickets(){
        $depature_date = date('Y-m-d');
        $ck_emp_id = $this->session->userdata('emp_id');
        $response = [];
        $data_trip = $this->home->get_tickets_data($depature_date, $ck_emp_id);
        $ck_emp_lang = $this->input->cookie('ck_emp_lang');
        $response['lang'] = $ck_emp_lang;
        $response['data'] = $data_trip;
        echo json_encode($response);
    }

    public function get_fcm_token_by_emp_id()
    {
        $ck_emp_id = $this->session->userdata('emp_id'); // ดึง emp_id จาก session
        $response = [];
        
        // ดึงข้อมูล FCM Token
        $data = $this->home->get_fcm_token_by_emp_id($ck_emp_id);
        
        // ดึงภาษาจาก cookie
        $ck_emp_lang = $this->input->cookie('ck_emp_lang');
        
        // สร้าง response
        $response['lang'] = $ck_emp_lang;
        $response['data'] = $data ? $data : []; // ถ้าไม่มีข้อมูล ให้คืนค่า array ว่าง

        // ส่ง response กลับในรูปแบบ JSON
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    

}
