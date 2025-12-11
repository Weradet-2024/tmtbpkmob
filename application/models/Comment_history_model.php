<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-26
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Comment_history_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
    }
    public function get_data($chk_com_id)
    {
        //define date
        $curr_month = date("m");
        $curr_year = date("Y");
        $arr_month_name = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
        $arr_month_name_eng = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $start_year = NULL;
        $num_years_back = 2;
        // กำหนดปีเริ่มต้น (ปีปัจจุบันถ้าไม่ได้ระบุ)
        if ($start_year === null) {
            $start_year = date("Y");  // ปีปัจจุบัน
        }

        // สร้าง array เก็บปีย้อนหลัง
        $years_array = [];
        for ($i = 0; $i <= $num_years_back; $i++) {
            $years_array[] = $start_year - $i;
        }

       // ลบค่าปีที่ซ้ำออกจาก array
       $years_array = array_unique($years_array);
        //get route
        $sql_route = "SELECT DISTINCT route_code,route_name 
                             FROM route where route_status='active' 
                             AND route_code NOT IN ('TMT000')  ORDER BY route_code";
        $query_route = $this->db->query($sql_route)->result();
        $arr_result = array();
        $ck_emp_lang = $this->input->cookie('ck_emp_lang');

        if ($ck_emp_lang == "th") {
            $arr_result['arr_month_name'] = $arr_month_name;
        } else {
            $arr_result['arr_month_name'] = $arr_month_name_eng;
        }
        $arr_result['curr_year'] = $curr_year;
        $arr_result['curr_month'] = $curr_month;
        $arr_result['query_str_year'] = $years_array;
        $arr_result['query_route'] = $query_route;
        return $arr_result;
    }
    public function chk_val($chk_emp_id, $year, $month)
    {
        //get data comment
        $this->db->select('*');
        $this->db->from('comment');
        $this->db->where('comment_by_id', $chk_emp_id);
        $this->db->where('SUBSTRING(comment_reg_date,1,4) = ', $year);
        $this->db->where('SUBSTRING(comment_reg_date,6,2) = ', $month);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_shift_data($chk_com_id)
    {
        $arr_result = [];
        $this->db->distinct();
        $this->db->select('shift_start');
        $this->db->where('shift_com_id', $chk_com_id);
        $query = $this->db->get('shift')->result();
        foreach ($query as $rs) {
            $row = [];
            $row['shift_type'] = $rs->shift_start;
            $row['shift_text'] = 'IN';
            $arr_result[] = $row;
        }
        //get shift end
        $this->db->distinct();
        $this->db->select('shift_end');
        $this->db->where('shift_com_id', $chk_com_id);
        $query2 = $this->db->get('shift')->result();
        foreach ($query2 as $rs) {
            $row = [];
            $row['shift_type'] = $rs->shift_end;
            $row['shift_text'] = 'OUT';
            $arr_result[] = $row;
        }
        return $arr_result;
    }
    public function get_time_id($time, $trip)
    {
        if ($trip == "in") {
            $this->db->select('top(1) shift_id');
            $this->db->from('shift');
            $this->db->where('shift_start', $time);
            $query = $this->db->get()->result();
            $time_id = '';
            foreach ($query as $rs) {
                $time_id = $rs->shift_id;
            }
            return $time_id;
        } else if ($trip == "out") {
            $this->db->select('top(1) shift_id');
            $this->db->from('shift');
            $this->db->where('shift_end', $time);
            $query = $this->db->get()->result();
            $time_id = '';
            foreach ($query as $rs) {
                $time_id = $rs->shift_id;
            }
            return $time_id;
        }
    }
    public function insert_comment(
        $data
    ) {
        $status = $this->db->insert('comment', $data);
        return $status;
    }
    
    public function get_emp_data($ck_emp_id)
    {
        $this->db->select('*');
        $this->db->from('emp');
        $this->db->join('dept', 'dept.dept_id = emp.emp_dept', 'left');
        $this->db->where('emp_id = ', $ck_emp_id);
        $query = $this->db->get();
        return $query->result();

    }
    public function get_route_name($route)
    {
        $this->db->select('route_name');
        $this->db->from('route');
        $this->db->where('route_code = ', $route);
        $query = $this->db->get();
        return $query->result();
    }
    public function get_admin_data_email()
    {

        //get admin email
        $this->db->select('emp_email');
        $this->db->from('emp');
        $this->db->where('emp_id = 1');
        $query = $this->db->get();
        return $query->result();
    }
    public function info_val($comment_id)
    {
        $this->db->select('*');
        $this->db->from('comment');
        $this->db->join('route', 'route.route_code = comment.comment_route AND route.route_trip = comment.comment_trip', 'left');
        // $this->db->join('shift', 'shift.shift_id = comment.comment_time', 'left');
        $this->db->join('emp', 'emp.emp_id = comment.comment_feedback_by_id', 'left');
        $this->db->where('comment_id = ', $comment_id);
        $query = $this->db->get();
        return $query->result();
    }
}
