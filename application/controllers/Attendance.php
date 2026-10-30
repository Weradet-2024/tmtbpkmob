<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Attendance extends Authenticated_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Bangkok');
        $this->load->library('session');
        $this->load->library('Language_lib');
        $this->language_lib->setLanguage();
    }
    public function index()
    {
        $this->load->view('template/header_view');
        $this->load->view('attendance/attendance_view_js');
        $this->load->view('attendance/attendance_view');
        $this->load->view('template/footer_view');
    }
}
