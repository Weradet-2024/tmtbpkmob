<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2566-10-09
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Reserve_history_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function chkFeature($ck_emp_com_id,$ck_emp_plant_id){
        $this->db->where('feature_set_item_com_id', $ck_emp_com_id);
        $this->db->where('feature_set_item_plant_id', $ck_emp_plant_id);
        $sql = $this->db->get('feature_set_item')->result_array();
        return $sql;
    }

    public function get_data($ck_emp_id, $ck_emp_com_id)
    {

        $arr_result = [];
        $curr_date = date("Y-m-d");
        $lastDayThisMonth = date("Y-m-t");
        $curr_date_name = date("l");
        $curr_time = date("H:i:s");

        $this->db->select('calendar_date, calendar_type');
        $this->db->from('calendar');
        $this->db->where('calendar_date > ', $curr_date);
        $this->db->where('calendar_com_id', $ck_emp_com_id);
        $this->db->where('calendar_type = ', 'Weekday');
        $this->db->order_by('calendar_id');
        $this->db->limit(1);
        $query_next = $this->db->get();

        $query_close_time = $this->db->select('close_time_start, close_time_end')
            ->from('close_time')
            ->where('close_time_com_id', $ck_emp_com_id)
            ->where('close_time_date', $curr_date_name)
            ->get();
        $close_time_start = '';
        $close_time_end = '';
        if ($query_close_time->num_rows() > 0) {
            $result = $query_close_time->row();
            $close_time_start = $result->close_time_start;
            $close_time_end = $result->close_time_end;
        }

        $this->db->select('reserve_id, reserve_date, reserve_round, reserve_time, reserve_trip, reserve_shift, reserve_status, MAX(attendance_time) as attendance_time')
            ->distinct()
            ->from('reserve')
            ->join('attendance', 'attendance.attendance_mem_id = reserve.reserve_mem_id 
                               AND attendance.attendance_date = reserve.reserve_date 
                               AND attendance.attendance_trip_time = reserve.reserve_time 
                               AND attendance.attendance_trip = reserve.reserve_trip', 'left')
            ->where('reserve_mem_id', $ck_emp_id)
           ->where('reserve_date >=', $curr_date)
            ->where('reserve_status != ', 'Cancelled')
            ->group_by(['reserve_id', 'reserve_date', 'reserve_round', 'reserve_time', 'reserve_trip', 'reserve_shift', 'reserve_status'])
            ->order_by('reserve_date', 'ASC')
            ->order_by('reserve_time', 'ASC');
        $query_reserve = $this->db->get();
        $row = [];
        $row['reserve'] = $query_reserve->result();
        $row['query_next'] = $query_next->result();
        $row['close_time_start'] = $close_time_start;
        $row['close_time_end'] = $close_time_end;
        $row['curr_date'] = $curr_date;
        $row['curr_time'] = $curr_time;
        $arr_result = $row;
        return $arr_result;
    }
    public function get_trip_out_time_data($ck_emp_com_id){
        $this->db->select('shift_end');
        $query = $this->db->get_where('shift', ['shift_type' => 'night', 'shift_com_id' => $ck_emp_com_id])->result();
        $arr_result = [];
        foreach($query as $rs){
            array_push($arr_result, $rs->shift_end);
        }
        return $arr_result;
    } 
    public function chk_val($get_status, $get_date_start, $get_date_end, $ck_emp_id, $ck_emp_com_id)
    {
        $arr_result = [];
        $curr_date = date("Y-m-d");
        $lastDayThisMonth = date("Y-m-t");
        $curr_date_name = date("l");
        $curr_time = date("H:i:s");

        $this->db->select('calendar_date, calendar_type');
        $this->db->from('calendar');
        $this->db->where('calendar_date > ', $curr_date);
        $this->db->where('calendar_com_id', $ck_emp_com_id);
        $this->db->where('calendar_type = ', 'Weekday');
        $this->db->order_by('calendar_id');
        $this->db->limit(1);
        $query_next = $this->db->get();

        $query_close_time = $this->db->select('close_time_start, close_time_end')
            ->from('close_time')
            ->where('close_time_com_id', $ck_emp_com_id)
            ->where('close_time_date', $curr_date_name)
            ->get();
        $close_time_start = '';
        $close_time_end = '';
        if ($query_close_time->num_rows() > 0) {
            $result = $query_close_time->row();
            $close_time_start = $result->close_time_start;
            $close_time_end = $result->close_time_end;
        }

        $this->db->select('reserve_id, reserve_date, reserve_round, reserve_time, reserve_trip, reserve_status, MAX(attendance_time) as attendance_time')
            ->distinct()   
            ->from('reserve')
            ->join('attendance', 'attendance.attendance_mem_id = reserve.reserve_mem_id 
                               AND attendance.attendance_date = reserve.reserve_date 
                               AND attendance.attendance_trip_time = reserve.reserve_time 
                               AND attendance.attendance_trip = reserve.reserve_trip', 'left')
            ->where('reserve_mem_id', $ck_emp_id)
            ->where('reserve_date >=', $get_date_start)
            ->where('reserve_date <=', $get_date_end)
            ->where('reserve_status != ', 'Cancelled')
            ->group_by(['reserve_id', 'reserve_date', 'reserve_round', 'reserve_time', 'reserve_trip', 'reserve_status'])
            ->order_by('reserve_date', 'ASC');

        // Add condition based on $get_status
        if ($get_status != "All") {
            $this->db->where('reserve_status', $get_status);
        }
        $query_reserve = $this->db->get();

        $row = array();
        $row['reserve'] = $query_reserve->result();
        $row['query_next'] = $query_next->result();
        $row['close_time_start'] = $close_time_start;
        $row['close_time_end'] = $close_time_end;
        $row['curr_date'] = $curr_date;
        $row['curr_time'] = $curr_time;

        return $row;

    }

    //update insert transaction 
    //author: weradet nopsomboon
    public function cancel_select($curr_date, $curr_time, $ck_emp_id, $ck_emp_com_id, $get_reserve_id)
    {

        $arr_not_in = array('Arranged', 'Cancelled');

        $cur_date_time = date('Y-m-d H:i:s');

        $data_update = array(
            'reserve_status' => 'Cancelled',
            'reserve_ed_date' => $curr_date,
            'reserve_ed_time' => $curr_time,
            'reserve_ed_mem_id' => $ck_emp_id,
        );

        $this->db->where('reserve_com_id = ', $ck_emp_com_id);
        $this->db->where('reserve_id = ', $get_reserve_id);
        $this->db->where_not_in('reserve_status', $arr_not_in);
        $status = $this->db->update('reserve', $data_update);

        //get data reserve by get_reserve
        //because to get anotor data reserve
        $query_get_reserve = $this->db->get_where('reserve', ['reserve_id' => $get_reserve_id])->result();

        foreach ($query_get_reserve as $rs) {

            $reserve_date = $rs->reserve_date;
            $reserve_time = $rs->reserve_time;
            $reserve_trip = $rs->reserve_trip;
            $reserve_route = $rs->reserve_route;
            $reserve_point = $rs->reserve_point;
        }

        $status_reserve = "Cancelled";

        //insert transaction
        $data_insert = array(
            'transaction_reserve_id' => $get_reserve_id,
            'transaction_reserve_date' => $reserve_date,
            'transaction_reserve_time' => $reserve_time,
            'transaction_reserve_trip' => $reserve_trip,
            'transaction_reserve_route' => $reserve_route,
            'transaction_reserve_point' => $reserve_point,
            'transaction_reserve_status' => $status_reserve,
            'transaction_reserve_dev' => "Mobile",
            'transaction_reserve_mem_id' => $ck_emp_id,
            'transaction_date' => $cur_date_time,
            'transaction_type' => "Cancelled"
        );

        $this->db->insert('transaction_reserve', $data_insert);

        return $status;
    }
    //end update

    public function get_company_end_time($ck_emp_com_id)
    {
        $this->db->select('company_end_time, company_finished_time');
        $this->db->from('company');
        $this->db->where('company_id = ', $ck_emp_com_id);
        $query_company = $this->db->get();
        return $query_company->result();

    }

    public function get_reserve_date($ck_emp_com_id, $get_chk_reserve)
    {

        $this->db->select('reserve_date');
        $this->db->from('reserve');
        $this->db->where('reserve_com_id = ', $ck_emp_com_id);
        $this->db->where('reserve_id = ', $get_chk_reserve);

        $query_reserve_date = $this->db->get();

        return $query_reserve_date->result();

    }
}
