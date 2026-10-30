<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2567-09-16
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Reserve_info_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_data($ck_emp_com_id, $ck_emp_id, $reserve_id)
    {
        $this->db->select('
            r.reserve_id, 
            r.reserve_date, 
            r.reserve_trip, 
            r.reserve_route, 
            r.reserve_status, 
            e.emp_name, 
            e.emp_lname
        ');
        $this->db->from('reserve r');
        $this->db->join('emp e', 'e.emp_id = r.reserve_mem_id', 'left');
        $this->db->where('r.reserve_id', $reserve_id);
        $this->db->where('e.emp_id', $ck_emp_id);

        $query = $this->db->get();

        $result = array();

        if ($query->num_rows() > 0) {
            $data = $query->row();
            $result[] = [
                'reserve_id' => $data->reserve_id,
                'reserve_status' => $data->reserve_status,
                'reserve_name' => trim($data->emp_name . ' ' . $data->emp_lname),
                'reserve_date' => $data->reserve_date,
                'reserve_trip' => $data->reserve_trip
            ];
        }

        return $result;
    }
    public function get_round($reserve_id, $emp_com_id, $emp_plant)
    {
        // Get reserve data
        $query_reserve = $this->db->select('reserve_trip, reserve_round, reserve_time')
            ->from('reserve')
            ->where('reserve_id', $reserve_id)
            ->get();

        if ($query_reserve->num_rows() > 0) {
            $reserve_data = $query_reserve->row();
            $reserve_trip = $reserve_data->reserve_trip;
            $reserve_time = $reserve_data->reserve_time;

            // Get shift data based on trip type
            $shift_column = $reserve_trip == "in" ? 'shift_start' : 'shift_end';
            $query_shift = $this->db->distinct()
                ->select($shift_column)
                ->from('shift')
                ->where('shift_com_id', $emp_com_id)
                ->where('shift_plant_id', $emp_plant)
                ->where('shift_status', 'active')
                ->where('shift_start IS NOT NULL')
                ->where('shift_end IS NOT NULL')
                ->get();

            $shifts = array_column($query_shift->result_array(), $shift_column);

            return [
                'shift' => $shifts,
                'reserve_time' => $reserve_time
            ];
        }

        return null; // If no reserve data is found
    }

    private function check_cut_off_time($arr_cut_of_time, $curr_time)
    {

        if (!empty($arr_cut_of_time) && is_array($arr_cut_of_time)) {
            foreach ($arr_cut_of_time as $cut_of_time) {
                if (isset($cut_of_time['close_time_start'], $cut_of_time['close_time_end'])) {
                    if ($curr_time >= $cut_of_time['close_time_start'] && $curr_time <= $cut_of_time['close_time_end']) {
                        return true; // อยู่ในช่วงเวลา
                    }
                }
            }
        }
        return false; // ไม่อยู่ในช่วงเวลา
    }

    public function get_route($reserve_id, $reserve_shift, $ck_emp_com_id, $emp_plant)
    {
        // Get reserve data
        $query_reserve = $this->db->select('reserve_mem_id, reserve_trip, reserve_route, reserve_point, reserve_route_zone')
            ->from('reserve')
            ->where('reserve_id', $reserve_id)
            ->get();

        if ($query_reserve->num_rows() > 0) {
            $reserve_data = $query_reserve->row();  // ดึงแถวเดียว
            $reserve_trip = $reserve_data->reserve_trip;
            // $reserve_route_zone = $reserve_data->reserve_route_zone;
            $reserve_route_zone = $reserve_data->reserve_route;

            if ($reserve_trip == "in") {
                // Get reserve route
                $query_route = $this->db->select('route_id, route_code, route_name')
                    ->from('route')
                    ->where('route_com_id', $ck_emp_com_id)
                    ->where('route_status', 'active')
                    ->where('route_vendor_id !=', 0)
                    ->group_start()
                    ->where('route_plant', $emp_plant)
                    ->or_where("',' + route_plant_member + ',' LIKE '%,$emp_plant,%'")
                    ->group_end()
                    ->order_by('route_plant, route_code')
                    ->get();
            } else {

                $query_route = $this->db->select('route_out.route_out_id, route_out.route_out_code, route_out.route_out_name, ro.route_out_shift_id')
                    ->from('route_out')
                    ->join('route_out as ro', 'route_out.route_out_id = ro.route_out_zone_id', 'left')
                    ->where('route_out.route_out_com_id', $ck_emp_com_id)
                    ->where('route_out.route_out_status', 'active')
                    ->where('route_out.route_out_vendor_id !=', 0)
                    ->group_start()
                    ->where('route_out.route_out_plant', $emp_plant)
                    ->or_where("',' + route_out.route_out_plant_member + ',' LIKE '%,$emp_plant,%'")
                    ->group_end()
                    ->order_by('route_out.route_out_plant, route_out.route_out_code')
                    ->get();

            }

            $arr_result = [];
            foreach ($query_route->result() as $rs1) {
                $route_id = $reserve_trip == "in" ? $rs1->route_id : $rs1->route_out_id;
                $route_code = $reserve_trip == "in" ? $rs1->route_code : $rs1->route_out_code;
                $route_name = $reserve_trip == "in" ? $rs1->route_name : $rs1->route_out_name;
                $route_shift = $reserve_trip == "out" ? $rs1->route_out_shift_id : "";


                $row = [];
                $row['route_id'] = $route_id;
                $row['route_code'] = $route_code;
                $row['route_name'] = $route_name;
                $row['route_shift'] = $route_shift;

                $arr_result[] = $row;
            }


            return [
                'reserve_route' => $reserve_route_zone,
                'route' => $arr_result // คืนค่าผลลัพธ์ทั้งหมดเป็นอาร์เรย์
            ];
        }

        return null; // หากไม่มีข้อมูลการจอง
    }
    public function get_point($reserve_id)
    {
        // Get data from reserve
        $query_reserve = $this->db->select('reserve_trip, reserve_route, reserve_point, reserve_route_zone')
            ->from('reserve')
            ->where('reserve_id', $reserve_id)
            ->get();

        if ($query_reserve->num_rows() > 0) {
            $reserve_data = $query_reserve->row();
            $reserve_trip = $reserve_data->reserve_trip;
            $reserve_route = $reserve_data->reserve_route;
            $reserve_route_zone = $reserve_data->reserve_route_zone;
            $reserve_point = $reserve_data->reserve_point;

            $arr_point = $this->get_points($reserve_trip, $reserve_route);

            return [
                'reserve_point' => $reserve_point,
                'arr_point' => $arr_point
            ];
        }

        return null; // No reserve data found
    }
    public function chk_route($reserve_trip, $ck_emp_com_id, $ck_emp_id, $reserve_time)
    {
        $shift_id = '';
        if ($reserve_time !== null) {
            $shift_id = $this->get_shift_id_by_time($reserve_time, $reserve_trip);
        }

        // ดึงข้อมูลพนักงาน
        $this->db->select('e.emp_id, e.emp_plant, e.emp_route_id, e.emp_route_out_id');
        $this->db->from('emp e');
        $this->db->where('e.emp_id', $ck_emp_id);
        $query_emp = $this->db->get();
        $emp_data = $query_emp->row();

        if (!$emp_data) {
            return array('error' => 'Employee not found');
        }

        // -------------------------------
        // ดึงข้อมูลเส้นทาง (IN / OUT)
        // -------------------------------
        if ($reserve_trip == "in") {
            $this->db->select('r.route_id, r.route_code, r.route_name');
            $this->db->from('route r');
            $this->db->where('r.route_status', 'active');
            $this->db->where('r.route_com_id', $ck_emp_com_id);
            $this->db->where('r.route_vendor_id !=', 0);
            $this->db->group_start()
                ->where('r.route_plant', $emp_data->emp_plant)
                ->or_where("',' + r.route_plant_member + ',' LIKE '%,$emp_data->emp_plant,%'")
                ->group_end();
        } else {
            $this->db->select('r.route_out_id, r.route_out_code, r.route_out_name');
            $this->db->from('route_out r');
            $this->db->where('r.route_out_status', 'active');
            $this->db->where('r.route_out_com_id', $ck_emp_com_id);
            $this->db->where('r.route_out_vendor_id !=', 0);
            $this->db->group_start()
                ->where('r.route_out_plant', $emp_data->emp_plant)
                ->or_where("',' + r.route_out_plant_member + ',' LIKE '%,$emp_data->emp_plant,%'")
                ->group_end();

            // if ($shift_id !== '') {
            //     $this->db->where('r.route_out_shift_id', $shift_id);
            // }
        }

        $query_route = $this->db->get();

        $arr_routes = [];
        foreach ($query_route->result() as $rs1) {
            $arr_routes[] = [
                'route_id' => $reserve_trip == "in" ? $rs1->route_id : $rs1->route_out_id,
                'route_code' => $reserve_trip == "in" ? $rs1->route_code : $rs1->route_out_code,
                'route_name' => $reserve_trip == "in" ? $rs1->route_name : $rs1->route_out_name,
            ];
        }

        // -------------------------------
        // ดึงข้อมูล shift
        // -------------------------------
        $this->db->distinct();
        $this->db->select($reserve_trip == "in" ? 'shift_start as shift' : 'shift_end as shift');
        $this->db->from('shift');
        $this->db->where('shift_com_id', $ck_emp_com_id);
        $this->db->where('shift_status', 'active');
        $this->db->where('shift_start IS NOT NULL');
        $this->db->where('shift_end IS NOT NULL');
        $query_shift = $this->db->get();

        $arr_shift = [];
        foreach ($query_shift->result() as $rs2) {
            $arr_shift[] = ['shift' => $rs2->shift];
        }

        // -------------------------------
        // รวมผลลัพธ์ทั้งหมด
        // -------------------------------
        return [
            'emp_route_id' => $emp_data->emp_route_id,
            'emp_route_out_id' => $emp_data->emp_route_out_id,
            'route' => $arr_routes,
            'shift_data' => $arr_shift
        ];
    }

    public function chk_point($reserve_trip, $reserve_route, $ck_emp_id, $ck_emp_com_id)
    {
        // Get employee data
        $this->db->select('emp_route_id, emp_pick_id, emp_route_out_id, emp_drop_id');
        $this->db->from('emp');
        $this->db->where('emp_id', $ck_emp_id);
        $query_emp = $this->db->get();
        $emp_data = $query_emp->row();

        if (!$emp_data) {
            // Handle case where employee is not found
            return array('error' => 'Employee not found');
        }

        $emp_pick_id = $emp_data->emp_pick_id;
        $emp_drop_id = $emp_data->emp_drop_id;

        // Get points data
        $arr_point = $this->get_points($reserve_trip, $reserve_route);

        // Prepare the result array
        $arr_result = array(
            'emp_pick_id' => $emp_pick_id,
            'emp_drop_id' => $emp_drop_id,
            'arr_point' => $arr_point
        );

        return $arr_result;
    }
    public function check_data_trip_dupplicate($reserve_id, $reserve_date, $reserve_trip, $ck_emp_id)
    {
        $this->db->where('reserve_id != ', $reserve_id);
        $query = $this->db->get_where('reserve', ['reserve_mem_id' => $ck_emp_id, 'reserve_working_date' => $reserve_date, 'reserve_trip' => $reserve_trip]);
        return ($query->num_rows() > 0) ? true : false;
    }
    public function save_reservation_data($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $ck_emp_id, $ck_emp_com_id, $ck_emp_plant)
    {
        //declare variable
        $curr_date = date("Y-m-d");
        $curr_time = date("H:i:s");
        $chk_curr_date = date("l");
        $cur_date_time = date('Y-m-d H:i:s');
        $route_vendor_id = '';

        $time_reserve = $reserve_round;

        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $arr_cut_of_time = get_cut_of_time_data($chk_curr_date, $ck_emp_plant);
        //get shift data
        $shift_data = $reserve_trip == 'in' ? $this->db->get_where('shift', ['shift_start' => $time_reserve])->result() : $this->db->get_where('shift', ['shift_end' => $time_reserve])->result();

        // Get the shift round based on the trip type
        $round_shift = !empty($shift_data) ? $reserve_trip == 'in' ? $shift_data[0]->shift_round_start : $shift_data[0]->shift_round_end : '';

        //check weekdate
        $data_weekday = get_calendar_type($ck_emp_com_id, $curr_date);
        //วันทำงาน ถัดไป
        $data_next_weekday = get_calendar_next_weekday($curr_date);

        $arr_trip_out_night_shift = get_trip_out_night_shift();

        // สร้าง array ใหม่เพื่อเก็บค่าของ shift_end
        $trip_out_night_shift = (!empty($arr_trip_out_night_shift) && is_array($arr_trip_out_night_shift))
            ? array_map(function ($item) {
                return isset($item['shift_end']) ? $item['shift_end'] : null;
            }, $arr_trip_out_night_shift)
            : [];


        //specail case

        //normal case
        if ($reserve_date < $curr_date) {

            return "reserve_less_than";

        } else if ($reserve_date == $curr_date && $time_reserve < $curr_time) {

            return "reserve_less_than";

        } else if ($reserve_date == $tomorrow) {

            if ($reserve_trip == "in" && $this->check_cut_off_time($arr_cut_of_time, $curr_time)) {

                if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                    return "data_dup";
                }

                // Update reservation with "Waiting" status
                return "ng_cut_of_time";

            } else {
                $status_reserve = "Submitted";

                if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                    return "data_dup";
                }

                // Update reservation
                $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, $status_reserve, $round_shift);

                $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $status_reserve, $ck_emp_id, $curr_date);

                return $status_reserve;

            }


        } else if ($reserve_date > $curr_date) {


            //1. // 1. check ว่า วันนี้เป็นวันหยุดหรือไม่ และจองวันทำงานถัดไป
            if (isset($data_weekday["calendar_type"]) && $data_weekday["calendar_type"] == "weekday" && $reserve_date == $data_next_weekday['calendar_date']) {

                if (date('N', strtotime($reserve_date)) == 1 && $reserve_trip == "in") {
                    if ($this->check_cut_off_time($arr_cut_of_time, $curr_time)) {

                        return "ng_cut_of_time";


                    } else {
                        // Check for duplicate reservations outside close time window
                        if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                            return "data_dup";
                        }

                        $gb_status_reserve = "Submitted";

                        // Update reservation with "Submitted" status
                        $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, $gb_status_reserve, $round_shift);

                        $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $gb_status_reserve, $ck_emp_id, $curr_date);

                        return $gb_status_reserve;

                    }// check cut of time
                } else {
                    // Check for duplicate reservations outside close time window
                    if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                        return "data_dup";
                    }

                    $gb_status_reserve = "";

                    // Update reservation with "Submitted" status
                    $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, $gb_status_reserve, $round_shift);

                    $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $gb_status_reserve, $ck_emp_id, $curr_date);

                    return $gb_status_reserve;
                }

            }

            //วันหยุด จองวันจันทร์
            if (isset($data_weekday["calendar_type"]) && $data_weekday["calendar_type"] == "holiday" && $reserve_date == $data_next_weekday['calendar_date']) {

                if (date('N', strtotime($reserve_date)) == 1 && $reserve_trip == "in") {
                    if ($this->check_cut_off_time($arr_cut_of_time, $curr_time)) {

                        return "ng_cut_of_time";


                    } else {
                        // Check for duplicate reservations outside close time window
                        if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                            return "data_dup";
                        }

                        $gb_status_reserve = "Submitted";

                        // Update reservation with "Submitted" status
                        $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, $gb_status_reserve, $round_shift);

                        $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $gb_status_reserve, $ck_emp_id, $curr_date);

                        return $gb_status_reserve;

                    }// check cut of time
                } else {
                    // Check for duplicate reservations outside close time window
                    if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                        return "data_dup";
                    }

                    $gb_status_reserve = "Submitted";

                    // Update reservation with "Submitted" status
                    $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, $gb_status_reserve, $round_shift);

                    $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $gb_status_reserve, $ck_emp_id, $curr_date);

                    return $gb_status_reserve;
                }

            } else {

                //Normal Case

                // Check for duplicate reservations outside close time window
                if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                    return "data_dup";
                }

                $gb_status_reserve = "Submitted";

                // Update reservation with "Submitted" status
                $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, $gb_status_reserve, $round_shift);

                $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $gb_status_reserve, $ck_emp_id, $curr_date);

                return $gb_status_reserve;

            }

            //วันนี้เป็นศุกร์ วันที่แก้คือวันจันทร์


            //วันนี้คือ วันเสาร์ อาทิตย์ แก้ วันจันทร์

            // $day_no = date('N', strtotime($reserve_date));
            // if ($day_no == 6 || $day_no == 7) {
            //     $reserve_date = $this->get_next_monday($reserve_date);
            // }

        } else if ($reserve_date == $curr_date) {

            if (!empty($data_weekday) && isset($data_weekday["calendar_type"]) && $data_weekday["calendar_type"] == "weekday") {

                if ($time_reserve < $curr_time && ($reserve_trip == "in" || $reserve_trip == "in_out")) {

                    return "reserve_less_than";

                } else if ($this->check_cut_off_time($arr_cut_of_time, $curr_time)) {

                    // Update reservation with "Waiting" status
                    return "ng_cut_of_time";

                } else if (($reserve_trip == "in" || $reserve_trip == "in_out") && $reserve_round == "07:30") {

                    return "ng_cut_of_time";

                } else {
                    // Check for duplicate reservations outside close time window
                    if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                        return "data_dup";
                    }

                    // Update reservation with "Submitted" status
                    $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, "Submitted", $round_shift);

                    $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, "Submitted", $ck_emp_id, $curr_date);

                    return "Submitted";
                }

            } else {

                if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                    return "data_dup";
                } else if ($this->check_cut_off_time($arr_cut_of_time, $curr_time)) {

                    if ($this->check_duplicate_reserve($reserve_date, $reserve_trip, $ck_emp_id, $reserve_id)) {
                        return "data_dup";
                    }

                    // Update reservation with "Waiting" status
                    return "ng_cut_of_time";

                } else {

                    // Determine reservation status
                    $status_reserve = $this->get_reserve_status($reserve_date, $reserve_trip, $time_reserve, $data_next_weekday, $trip_out_night_shift);

                    // Update reservation
                    $this->update_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, $status_reserve, $round_shift);

                    $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, "Submitted", $ck_emp_id, $curr_date);

                    return $status_reserve;

                }

            }

        } else {
            return "Submitted";
        }

    }// function

    public function cancel_reservation_data($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, $ck_emp_id, $ck_emp_com_id, $ck_emp_plant)
    {
        //declare variable
        $curr_date = date("Y-m-d");
        $curr_time = date("H:i:s");
        $chk_curr_date = date("l");
        $cur_date_time = date('Y-m-d H:i:s');
        $close_time_start = '';
        $close_time_end = '';
        $time_reserve = $reserve_round;

        //declare close time start
        $close_time_start = '';
        $close_time_end = '';
        $arr_cut_of_time = get_cut_of_time_data($chk_curr_date, $ck_emp_com_id);
        // ตรวจสอบข้อมูล cut-off time
        if (!empty($arr_cut_of_time) && isset($arr_cut_of_time['close_time_start'], $arr_cut_of_time['close_time_end'])) {
            $close_time_start = $arr_cut_of_time['close_time_start'];
            $close_time_end = $arr_cut_of_time['close_time_end'];
        }

        //check weekdate
        $data_weekday = get_calendar_type($ck_emp_com_id, $curr_date);
        //วันทำงาน ถัดไป
        $data_next_weekday = get_calendar_next_weekday($curr_date);

        $arr_trip_out_night_shift = get_trip_out_night_shift();

        // สร้าง array ใหม่เพื่อเก็บค่าของ shift_end
        $trip_out_night_shift = (!empty($arr_trip_out_night_shift) && is_array($arr_trip_out_night_shift))
            ? array_map(function ($item) {
                return isset($item['shift_end']) ? $item['shift_end'] : null;
            }, $arr_trip_out_night_shift)
            : [];

        $arr_cut_of_time = get_cut_of_time_data($chk_curr_date, $ck_emp_com_id);
        // ตรวจสอบข้อมูล cut-off time
        if (!empty($arr_cut_of_time) && isset($arr_cut_of_time['close_time_start'], $arr_cut_of_time['close_time_end'])) {
            $close_time_start = $arr_cut_of_time['close_time_start'];
            $close_time_end = $arr_cut_of_time['close_time_end'];
        }

        if ($curr_time >= $close_time_start && $curr_time <= $close_time_end) {
            //normal case

            $this->cancel_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, "Cancelled");

            // Natthanicha comment cut off time open free 19/06/25
            $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, "Cancelled", $ck_emp_id, $curr_date);

            return "Cancelled";
        } else {
            $this->cancel_reservation($reserve_id, $ck_emp_id, $ck_emp_com_id, $reserve_date, $reserve_trip, $reserve_round, $reserve_route, $reserve_point, $curr_date, $curr_time, "Cancelled");

            // Natthanicha comment cut off time open free 19/06/25
            $this->insert_transaction_reserve($reserve_id, $reserve_date, $reserve_round, $reserve_trip, $reserve_route, $reserve_point, "Cancelled", $ck_emp_id, $curr_date);

            return "Cancelled";
        }



    }

    private function get_points($reserve_trip, $reserve_route)
    {
        // Determine table and status based on reserve_trip
        $table = ($reserve_trip == "in") ? 'pickup' : 'dropoff';
        $status_column = ($reserve_trip == "in") ? 'pickup_status' : 'dropoff_status';
        $id_column = ($reserve_trip == "in") ? 'pickup_id' : 'dropoff_id';
        $code_column = ($reserve_trip == "in") ? 'pickup_code' : 'dropoff_code';
        $name_column = ($reserve_trip == "in") ? 'pickup_name' : 'dropoff_name';

        $this->db->select('*');
        $this->db->from($table);

        if ($reserve_trip == "in") {
            $this->db->where("{$table}_route_id", $reserve_route);
        } else {
            $this->db->where("{$table}_route_id", $reserve_route);
        }

        $this->db->where($status_column, 'active');
        $query_points = $this->db->get();

        $arr_point = array();
        foreach ($query_points->result() as $point) {
            $arr_point[] = array(
                'point_id' => $point->$id_column,
                'point_code' => $point->$code_column,
                'point_name' => $point->$name_column
            );
        }

        return $arr_point;
    }

    // Helper function to check for duplicate reservations
    private function check_duplicate_reserve($reserve_date, $reserve_trip, $reserve_mem_id, $reserve_id)
    {
        $query = $this->db->select('COUNT(reserve_id) AS count_reserve_id')
            ->from('reserve')
            ->where('reserve_working_date', $reserve_date)
            ->where('reserve_follower IS NULL')
            ->where('reserve_trip', $reserve_trip)
            ->where('reserve_mem_id', $reserve_mem_id)
            ->where('reserve_id !=', $reserve_id)
            ->get();

        return $query->row()->count_reserve_id > 0;
    }


    // Helper function to get the Cancel status
    private function get_cancel_status($reserve_date, $reserve_trip, $reserve_round, $data_next_weekday, $trip_out_time)
    {

        //get first time day shift
        $this->db->select('shift_start');
        $this->db->from('shift');
        $this->db->where('shift_type', 'day');
        $this->db->limit(1);
        $this->db->order_by('shift_start', 'ASC');
        $arr_shift = $this->db->get()->row_array();

        if (
            !empty($data_weekday) && isset($data_weekday["calendar_type"]) && ($reserve_date < $data_next_weekday["calendar_date"] ||
                ($reserve_date == $data_next_weekday["calendar_date"] && $reserve_trip == 'in' && $reserve_round == $arr_shift['shift_start']) ||
                ($reserve_date == $data_next_weekday["calendar_date"] && $reserve_trip == 'out' && in_array($reserve_round, $trip_out_time)))
        ) {
            return "Pending_cancel";
        }

        return "Cancelled";
    }

    // Helper function to update reservation data
    private function update_reservation($reserve_id, $reserve_mem_id, $reserve_com_id, $reserve_date, $reserve_trip, $reserve_time, $reserve_route, $reserve_point, $curr_date, $curr_time, $status_reserve, $round_shift)
    {
        // กำหนดค่าเริ่มต้น
        $reserve_route_zone = $reserve_route;
        $reserve_route = '';
        $reserve_zone = '';

        // ตรวจสอบ Trip และดึงข้อมูลจากฐานข้อมูล
        if ($reserve_trip === 'in') {
            // ดึงข้อมูลเส้นทางขาเข้า
            $query = $this->db->select('r.route_id AS reserve_route, rz.route_zone_zone_id AS reserve_zone')
                ->from('route_zone rz')
                ->join('route r', 'r.route_zone_id = rz.route_zone_id', 'left')
                ->where('rz.route_zone_id', $reserve_route_zone)
                ->get();

            $query_point = $this->db->query("SELECT pickup_route_id FROM pickup WHERE pickup_id = '$reserve_point'");

        } else {
            // ดึงข้อมูลเส้นทางขาออก
            $query = $this->db->select('ro.route_out_id AS reserve_route, rzo.route_zone_out_zone_id AS reserve_zone')
                ->from('route_zone_out rzo')
                ->join('route_out ro', 'ro.route_out_zone_id = rzo.route_zone_out_id', 'left')
                ->where('rzo.route_zone_out_id', $reserve_route_zone)
                ->get();

            $query_point = $this->db->query("SELECT dropoff_route_id FROM dropoff WHERE dropoff_id = '$reserve_point'");
        }

        // ตรวจสอบว่ามีผลลัพธ์หรือไม่
        if ($query->num_rows() > 0) {
            $result = $query->row();
            $reserve_route = $result->reserve_route;
            $reserve_zone = $result->reserve_zone;
        }

        if ($query_point->num_rows() > 0) {
            $result = $query_point->row();

            if ($reserve_trip == "in") {
                $reserve_route = $result->pickup_route_id;
            } else {
                $reserve_route = $result->dropoff_route_id;
            }
        }

        // ข้อมูลอัปเดต
        $data_update = [
            'reserve_mem_id' => $reserve_mem_id,
            'reserve_com_id' => $reserve_com_id,
            'reserve_date' => $reserve_date,
            'reserve_working_date' => $reserve_date,
            'reserve_trip' => $reserve_trip,
            'reserve_time' => $reserve_time,
            'reserve_route' => $reserve_route,
            'reserve_zone' => $reserve_zone, // เพิ่ม reserve_zone
            'reserve_route_zone' => $reserve_route_zone,
            'reserve_point' => $reserve_point,
            'reserve_ed_date' => $curr_date,
            'reserve_ed_time' => $curr_time,
            'reserve_ed_mem_id' => $reserve_mem_id,
            'reserve_dev' => "Mobile",
            'reserve_status' => $status_reserve,
            'reserve_round' => $round_shift
        ];

        // อัปเดตข้อมูลในตาราง reserve
        $this->db->where('reserve_id', $reserve_id);
        return $this->db->update('reserve', $data_update);
    }


    private function cancel_reservation($reserve_id, $reserve_mem_id, $reserve_com_id, $reserve_date, $reserve_trip, $reserve_time, $reserve_route, $reserve_point, $curr_date, $curr_time, $status_reserve)
    {
        $data_update = [
            'reserve_ed_date' => $curr_date,
            'reserve_ed_time' => $curr_time,
            'reserve_ed_mem_id' => $reserve_mem_id,
            'reserve_dev' => "Mobile",
            'reserve_status' => $status_reserve,
        ];

        $this->db->where('reserve_id', $reserve_id);
        return $this->db->update('reserve', $data_update);
    }

    // Helper function to insert transaction log
    private function insert_transaction_reserve($reserve_id, $reserve_date, $reserve_time, $reserve_trip, $reserve_route, $reserve_point, $status_reserve, $reserve_mem_id, $transaction_date)
    {
        $data_insert = array(
            'transaction_reserve_id' => $reserve_id,
            'transaction_reserve_date' => $reserve_date,
            'transaction_reserve_time' => $reserve_time,
            'transaction_reserve_trip' => $reserve_trip,
            'transaction_reserve_route' => $reserve_route,
            'transaction_reserve_point' => $reserve_point,
            'transaction_reserve_status' => $status_reserve,
            'transaction_reserve_dev' => "Mobile",
            'transaction_reserve_mem_id' => $reserve_mem_id,
            'transaction_date' => $transaction_date,
            'transaction_type' => "Update"
        );
        return $this->db->insert('transaction_reserve', $data_insert);
    }

    private function get_shift_id_by_time($reserve_time, $reserve_trip)
    {
        $this->db->select('shift_id');

        if ($reserve_trip == 'in') {
            $this->db->where('shift_start', $reserve_time);
        } else if ($reserve_trip == 'out') {
            $this->db->where('shift_end', $reserve_time);
        } else {
            return "";
        }

        $shift = $this->db->get('shift')->row_array();
        return $shift ? $shift['shift_id'] : "";
    }

}
