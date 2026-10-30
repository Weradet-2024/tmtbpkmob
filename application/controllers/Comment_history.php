<?php
/*
 * Comment
 * Comment Controller Module 
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-23
 */
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Comment_history extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Bangkok');
        //call model here
        $this->load->model('Comment_history_model', 'comment');
        $this->load->model('Login_model');
        //call libray here
        $this->load->library('authentication_helper');
        $this->load->library('session');
        $this->load->library('Language_lib');
      //  $this->load->library('Email_lib');

        $this->language_lib->setLanguage();
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
    }
    public function index()
    {
        //get menu
        $this->load->view('template/header_view');
        $this->load->view('Comment_history/comment_history_view_js');
        //$this->load->view('Comment_history/comment_history_view');
        $this->load->view('Comment_history/comment_history_view_line_OA');
        $this->load->view('template/footer_view');
    }
    public function get_data()
    {
        $response = [];
        $ck_emp_id = $this->session->userdata('emp_id');
        $arr_emp_com_id = get_comopany_by_userid($ck_emp_id); 
        $data = $this->comment->get_data($arr_emp_com_id->emp_com_id);
        $response['data'] = $data;
        echo json_encode($response);
    }
    public function chk_val()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $data_comment = $this->comment->chk_val($ck_emp_id, $year, $month);
        $response = [];
        $response['data'] = $data_comment;
        echo json_encode($response);
    }
    public function chk_type()
    {
        $today = date("Y-m-d");
        echo $today;
    }
    public function get_shift()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $chk_com_id = get_comopany_by_userid($ck_emp_id);
        $data_shift = $this->comment->get_shift_data($chk_com_id->emp_com_id);
        $response = [];
        $response['data'] = $data_shift;
        echo json_encode($response);
    }

    public function comment() {
        // ตรวจสอบว่าได้รับข้อมูลจากฟอร์มหรือไม่
        if ($this->input->method() == 'post') {
            // รับข้อมูลจากฟอร์ม
            $modal_subject = $this->input->post('modal_subject');
            $modal_detail = $this->input->post('modal_detail');
			$modal_license_plate = $this->input->post('modal_license_plate');
			$modal_route = $this->input->post('modal_route');
			$modal_time = $this->input->post('modal_time');

            $ck_emp_id = $this->session->userdata('emp_id');

            $curr_date = date('Y-m-d');
            $curr_time = date('H:i:s');

            // จัดการการอัปโหลดไฟล์
            if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] == 0) {
                $file_path = $this->_upload_file($_FILES['fileUpload']);
            } else {
                $file_path = NULL; // ถ้าไม่มีไฟล์ หรือเกิดข้อผิดพลาด
            }

            // บันทึกข้อมูลลงในฐานข้อมูล
            $data = [
                'comment_subject' => $modal_subject,
                'comment_detail' => $modal_detail,
                'comment_user_file_path' => $file_path,
				'comment_license_plate' => $modal_license_plate,
				'comment_route' => $modal_route,
				'comment_time' => $modal_time,
                'comment_status' => "open",
                "comment_by_id" => $ck_emp_id,
                'comment_reg_date' => $curr_date,
                'comment_reg_time' => $curr_time
            ];

            $result = $this->comment->insert_comment($data);
            $result = true;
            // ส่งผลลัพธ์กลับไปยัง Frontend
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล']);
        }
    }

    // ฟังก์ชันสำหรับอัปโหลดไฟล์
    public function _upload_file($file) {
        // กำหนดเส้นทางที่จัดเก็บไฟล์
        $upload_path = './file/comment_feedback/';
        $file_name = time() . '_' . $_FILES['fileUpload']['name'];

        // กำหนดการตั้งค่าการอัปโหลดไฟล์
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|docx'; // รูปแบบไฟล์ที่อนุญาต
        $config['file_name'] = $file_name;
        $config['max_size'] = 1024 * 10; // ขนาดไฟล์สูงสุด 10MB

        // โหลดไลบรารีการอัปโหลดไฟล์
        $this->load->library('upload', $config);

        // ตรวจสอบการอัปโหลด
        if (!$this->upload->do_upload('fileUpload')) {
            return NULL; // ถ้ามีข้อผิดพลาดในการอัปโหลด
        } else {
            return $upload_path . $file_name; // คืนค่าเส้นทางของไฟล์ที่อัปโหลด
        }
    }

    public function info_val(){
        $ck_emp_id = $this->session->userdata('emp_id');
        $comment_id = $this->input->post("comment_id");
        $response = $this->comment->info_val($comment_id);
        echo json_encode(array('result' => $response, 'status' => 'success'));
    }
}
