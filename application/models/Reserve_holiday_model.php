<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-28
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Reserve_holiday_model extends CI_Model
{
    private $status_complete_th = "จองสำเร็จ";
    private $status_waiting_th = "รออนุมัติจาก GA";
    private $status_pending_cancel_th = "รออนุมัติยกเลิกจาก GA";
    private $status_cancel_th = "ยกเลิก";
    private $status_complete_en = "Success";
    private $status_waiting_en = "Waiting to GA";
    private $status_pending_cancel_en = "Pending Cancel to GA";
    private $status_cancel_en = "Cancel";

    //save database status attribute
    private $save_status_success = "Submitted";
    private $save_status_waiting = "Waiting";
    public function __construct()
    {
        parent::__construct();
    }

    
    public function chkFeature_ga_approval($ck_emp_plant)
    {
        try {
            // ค้นหาข้อมูลจากฐานข้อมูล
            $this->db->where('feature_set_item_plant_id', $ck_emp_plant);
            $sql = $this->db->get('feature_set_item')->result_array();

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if (!empty($sql) && isset($sql[0]['feature_set_item_ga_approval'])) {
                if ($sql[0]['feature_set_item_ga_approval'] === "active") {
                    $this->save_status_success = "Waiting";
                    $this->save_status_waiting = "Waiting";
                } else {
                    $this->save_status_success = "Submitted";
                    $this->save_status_waiting = "Waiting";
                }
            } else {
                $this->save_status_success = "Submitted";
                $this->save_status_waiting = "Waiting";
            }
	
        if($ck_emp_plant == 2){
            $this->db->select('emp_id, emp_dept');
            $this->db->from('emp');
            $this->db->where('emp_id', $this->session->userdata('emp_id'));

            $query = $this->db->get()->row_array();

            if($query){
                if($query['emp_dept'] == 7){
                    $this->save_status_success = "Submitted";
                    $this->save_status_waiting = "Submitted";

                }
            }

        }


        } catch (Exception $e) {
            // กรณีเกิดข้อผิดพลาด ให้บันทึก Log และกำหนดค่าเริ่มต้น
            log_message('error', 'เกิดข้อผิดพลาดในการ Query: ' . $e->getMessage());
        }

    }

    public function get_emp_name_by_id($ck_emp_id)
    {
        $arr_emp = $this->db->get_where('emp', ['emp_id' => $ck_emp_id])->result_array();
        return !empty($arr_emp) ? $arr_emp[0]["emp_name"] . " " . $arr_emp[0]["emp_lname"] : "";
    }

    private function get_cut_of_time_data($curr_date, $plant_id){
        $query = $this->db->get_where('close_time', array('close_time_date' => $curr_date, 'close_time_plant_id' => $plant_id))->result_array();
        return !empty($query) ? $query : null;
    }

    public function get_time_data_by_trip($trip, $com_id, $emp_plant, $shift)
    {

        $arr_result = [];

        if ($trip == "one_way") {
            // Get distinct shift_start
            $this->db->distinct()->select('shift_start')->where('shift_com_id', $com_id);
            $this->db->where('shift_plant_id', $emp_plant);
            $this->db->where('shift_status', 'active');
            $this->db->where('shift_type', $shift);
            $query_start = $this->db->get('shift')->result();

            // Get distinct shift_end
            $this->db->distinct()->select('shift_end')->where('shift_com_id', $com_id);
            $this->db->where('shift_plant_id', $emp_plant);
            $this->db->where('shift_status', 'active');
            $this->db->where('shift_type', $shift);
            $query_end = $this->db->get('shift')->result();

            // Combine shift_start and shift_end
            foreach ($query_start as $rs) {
                $arr_result[] = [
                    'shift_value' => 'in ' . $rs->shift_start,
                    'shift_text' => 'รับเข้า ' . $rs->shift_start
                ];
            }
            foreach ($query_end as $rs) {
                $arr_result[] = [
                    'shift_value' => 'out ' . $rs->shift_end,
                    'shift_text' => 'รับออก ' . $rs->shift_end
                ];
            }
        } else {
            // For two-way trips
            $this->db->where('shift_type', $shift);
            $query = $this->db->get_where('shift', ['shift_com_id' => $com_id, 'shift_plant_id' => $emp_plant, 'shift_status' => 'active'])->result();
            foreach ($query as $rs) {
                $arr_result[] = [
                    'shift_value' => 'in_out ' . $rs->shift_id,
                    'shift_text' => 'ไป - กลับ ' . $rs->shift_start . ' - ' . $rs->shift_end
                ];
            }
        }

        return $arr_result;
    }

    public function get_route_and_point_by_trip($ck_emp_com_id, $ck_emp_id, $emp_plant)
    {
        // Get employee data
        $emp_data = $this->db->get_where('emp', [
            'emp_id' => $ck_emp_id,
            'emp_com_id' => $ck_emp_com_id
        ])->row();

        if (!$emp_data) {
            return [];
        }

        // Set route and pickup/dropoff queries
        $this->db->select('*');
        $this->db->from('route_zone'); // ระบุฐานข้อมูลและตาราง
        $this->db->where('route_zone_status', 'active');
        $this->db->where('route_zone_com_id', $emp_data->emp_com_id);
        // กำหนดเงื่อนไขแบบ OR ด้วยวงเล็บ
        $this->db->group_start();
        $this->db->where('route_zone_plant_id', $emp_plant);
        $this->db->or_where("',' + route_zone_plant_member + ',' LIKE '%,$emp_plant,%'");
        $this->db->group_end();
        $this->db->order_by('route_zone_plant_id, route_zone_code');
        $query_route = $this->db->get()->result();

        $query_pick = $this->db->get_where('pickup', [
            'pickup_com_id' => $emp_data->emp_com_id,
            'pickup_route_zone_id' => $emp_data->emp_route_zone_id,
            'pickup_status' => 'active'
        ])->result();

        // Set route and pickup/dropoff queries
        $this->db->select('*');
        $this->db->from('route_zone_out'); // ระบุฐานข้อมูลและตาราง
        $this->db->where('route_zone_out_status', 'active');
        $this->db->where('route_zone_out_com_id', $emp_data->emp_com_id);
        // กำหนดเงื่อนไขแบบ OR ด้วยวงเล็บ
        $this->db->group_start();
        $this->db->where('route_zone_out_plant_id', $emp_plant);
        $this->db->or_where("',' + route_zone_out_plant_member + ',' LIKE '%,$emp_plant,%'");
        $this->db->group_end();
        $this->db->order_by('route_zone_out_plant_id, route_zone_out_code');
        $query_route_out = $this->db->get()->result();

        $query_drop = $this->db->get_where('dropoff', [
            'dropoff_com_id' => $emp_data->emp_com_id,
            'dropoff_route_zone_out_id' => $emp_data->emp_route_zone_out_id,
            'dropoff_status' => 'active'
        ])->result();

        // Set data to send to user
        return [
            'emp_route_zone_id' => $emp_data->emp_route_zone_id,
            'emp_pick_id' => $emp_data->emp_pick_id,
            'emp_route_zone_out_id' => $emp_data->emp_route_zone_out_id,
            'emp_drop_id' => $emp_data->emp_drop_id,
            'route_zone' => $query_route,
            'pickup' => $query_pick,
            'route_zone_out' => $query_route_out,
            'dropoff' => $query_drop
        ];
    }

    public function get_point_data_by_id($ck_emp_com_id, $reserve_route)
    {
        $query = $this->db->get_where('pickup', ['pickup_com_id' => $ck_emp_com_id, 'pickup_route_zone_id' => $reserve_route, 'pickup_status' => 'active'])->result();
        return $query;
    }
    public function get_point_out_data_by_id($ck_emp_com_id, $reserve_route_out)
    {
        $query = $this->db->get_where('dropoff', ['dropoff_com_id' => $ck_emp_com_id, 'dropoff_route_zone_out_id' => $reserve_route_out, 'dropoff_status' => 'active'])->result();
        return $query;
    }
    public function get_shift_id_by_time($reserve_time, $reserve_trip)
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
    public function get_data_week($reserve_date, $com_id, $ck_emp_plant)
    {
        $data = $this->db->select('calendar_type')->where('calendar_com_id', $com_id)->where('calendar_date', $reserve_date)->where('calendar_plant_id', $ck_emp_plant)->get('calendar')->result_array();
        if (!empty($data)) {
            return $data[0]["calendar_type"];
        } else {
            return "";
        }
    }
    public function check_data_reserve($emp_id, $reserve_date, $reserve_date_end, $reserve_trip, $reserve_shift)
    {
        $this->db->select('*')->where('shift_id', $reserve_shift);
        $shift = $this->db->get('shift')->row_array();

        $shift_type = $shift["shift_type"];
        $new_departure_date = ($shift_type == "night")
            ? date_cover(date('Y-m-d', strtotime("+1 day", strtotime($reserve_date_end))))
            : $reserve_date_end;

        $this->db->select('*')
            ->from('reserve')
            ->where('reserve_mem_id', $emp_id)
            ->where('reserve_status !=', 'Cancelled')
            ->where('reserve_date >=', $reserve_date);

        if ($reserve_trip == "in") {
            $this->db->where('reserve_trip', 'in')
                ->where('reserve_date <=', $reserve_date_end);
        } elseif ($reserve_trip == "out") {
            $this->db->where('reserve_trip', 'out')
                ->where('reserve_date <=', $new_departure_date);
        } else {
            $this->db->where('reserve_date <=', $new_departure_date);
        }

        $result = $this->db->get()->result_array();

        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    public function save_reservation($reserve_data, $emp_id, $emp_com_id, $ck_emp_plant)
    {
        //current date and time
        $curr_date = date('Y-m-d');
        $curr_time = date('H:i');
        $curr_date_name = date("l");
        //shift data
        $shift_id = '';
        $shift_start = '';
        $shift_end = '';
        $shift_type = '';

        $reserve_date = $reserve_data['reserve_date'];
        $tomorrow = date("Y-m-d", strtotime('tomorrow'));
        $cal_weekday = $this->get_calendar_type($reserve_date, $emp_com_id, $ck_emp_plant);
        if ($cal_weekday == "weekday") {
            return ['data' => "Weekday"];
        }
        //get time of shift data to save database
        //trip in out => time (08:00, 17:00)
        //two way => shift_id

        switch ($reserve_data['reserve_trip']) {
            case "in_out":
                // Get $shift start and end by shift id //reserve_data['reserve_time'] = shift_id
                $arr_shift = $this->get_shift_data($reserve_data['reserve_time']);
                if (!$arr_shift) {
                    return false;
                }
                $shift_id = $arr_shift['shift_id'];
                $shift_start = $arr_shift['shift_start'];
                $shift_end = $arr_shift['shift_end'];
                $shift_type = $arr_shift['shift_type'];
                $shift_round_start = $arr_shift['shift_round_start'];
                $shift_round_end = $arr_shift['shift_round_end'];
                break;
            case "in":
            case "out":
                // Get shift_id
                $shift_id = $this->get_shift_id_by_time($reserve_data['reserve_time'], $reserve_data['reserve_trip']);
                $arr_shift = $this->get_shift_data($shift_id);
                if (!$arr_shift) {
                    return false;
                }
                $shift_id = $arr_shift['shift_id'];
                $shift_start = $arr_shift['shift_start'];
                $shift_end = $arr_shift['shift_end'];
                $shift_type = $arr_shift['shift_type'];
                $shift_round_start = $arr_shift['shift_round_start'];
                $shift_round_end = $arr_shift['shift_round_end'];
                break;
        }

        //get cut of time data by reserve date
        $close_time_end = '';
        $close_time_start = '';
        //get cut of time data by reserve date
        $close_time_end = '';
        $close_time_start = '';
        $arr_cut_of_time = $this->get_cut_of_time_data($curr_date_name, $ck_emp_plant);

        if ($reserve_date < $curr_date) {
            return ['data' => "reserve_less_than"];
        }

        //special case
        //special case
        //reserve in time report

        $feature_set_item = get_feature_set_item($emp_com_id);

        $this->chkFeature_ga_approval($ck_emp_plant);

        if ($feature_set_item == NULL)
            return ['data' => "ng_cut_of_time"];

        if ($feature_set_item['feature_set_item_time_to_close'] == "active") {

            $close_time_reserve_end = '';
            $close_time_reserve_start = '';

            $arr_cut_of_time_reseve = get_cut_of_time_reserve_data($reserve_data['reserve_trip'], $reserve_data['reserve_time']);
            if ($arr_cut_of_time_reseve != NULL) {
                $close_time_reserve_end = $arr_cut_of_time_reseve['close_time_reserve_end'];
                $close_time_reserve_start = $arr_cut_of_time_reseve['close_time_reserve_start'];

                if ($curr_time >= $close_time_reserve_start && $curr_time <= $close_time_reserve_end) {
                    return ['data' => "reserve_less_report"];
                }
            }
        }
        //reserve normal case

        $query_tomorrow_info = $this->db->get_where('calendar', ['calendar_date' => $tomorrow, 'calendar_com_id' => $emp_com_id]);
        $calendar_tomorrow_info = $query_tomorrow_info->result();
        if (empty($calendar_tomorrow_info)) {
            return ['data' => "calendar_tomorrow_info"];
        }

        //get calendar next
        //วันทำงานถัดไป
        $this->db->select('calendar_date, calendar_type');
        $this->db->from('calendar');
        $this->db->where('calendar_date >=', $tomorrow);
        $this->db->where('calendar_type', 'weekday');
        $this->db->where('calendar_com_id', $emp_com_id);
        $this->db->limit(1);
        $query_next = $this->db->get()->result();

        if (empty($query_next)) {
            return ['data' => "ng"];
        }

        if ($reserve_date < $curr_date) {

            return "reserve_less_than";

        } else if ($reserve_date == $curr_date) {

            if ($shift_start < $curr_time && ($reserve_data['reserve_trip'] == "in" || $reserve_data['reserve_trip'] == "in_out")) {

                return ['data' => "reserve_less_than"];


            } else if ( $shift_type == "day" && $shift_end < $curr_time && ($reserve_data['reserve_trip'] == "out")) {

                return ['data' => "reserve_less_than"];
            } 

            return ['data' => $this->do_reservation($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $shift_end, $shift_type, $this->save_status_waiting, $shift_round_start, $shift_round_end)];

        } else if ($reserve_date > $curr_date) {
            if (
                $cal_weekday == "Weekday"
                && $calendar_tomorrow_info[0]->calendar_type == "Holiday"
                && $reserve_date <= $query_next[0]->calendar_date
            ) {

                if ($feature_set_item['feature_set_item_cut_off_time'] == 'active' && $this->check_cut_off_time($arr_cut_of_time, $curr_time)) {

                return ['data' => "ng_cut_of_time"];

                    //return ['data' => $this->do_reservation($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $shift_end, $shift_type, $this->save_status_waiting, $shift_round_start, $shift_round_end)];

                } else {

                    return ['data' => $this->do_reservation($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $shift_end, $shift_type, $this->save_status_success,$shift_round_start, $shift_round_end)];

                }

            } else {

                return ['data' => $this->do_reservation($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $shift_end, $shift_type, $this->save_status_success, $shift_round_start, $shift_round_end)];

            }
        }

        //return ;
    }
    public function get_calendar_type($date, $com_id, $emp_plant)
    {
        $arr_calendar = $this->db->get_where('calendar', ['calendar_com_id' => $com_id, 'calendar_date' => $date, 'calendar_plant_id' => $emp_plant])->row_array();
        if (!empty($arr_calendar)) {
            return $arr_calendar['calendar_type'];
        }
        return [];
    }
    public function get_shift_data($shift)
    {
        // Get shift data
        $this->db->select('*')
            ->from('shift')
            ->where('shift_id', $shift);
        $shift = $this->db->get()->row_array();
        if ($shift) {
            return $shift;
        }
        return [];
    }
    public function do_reservation($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $shift_end, $shift_type, $status, $shift_round_start, $shift_round_end)
    {
        if ($reserve_data['reserve_trip'] == "in") {
            return $this->handle_trip_in($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $status, $shift_round_start);
        } elseif ($reserve_data['reserve_trip'] == "out") {
            return $this->handle_trip_out($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_end, $shift_type, $status, $shift_round_end);
        } else {
            return $this->handle_two_way_trip($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $shift_end, $shift_type, $status, $shift_round_start, $shift_round_end);
        }
    }

    private function handle_trip_in($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $status, $shift_round_start)
    {
        $reservation = $this->get_reservation($emp_id, 'in', $reserve_date);

        if (!$reservation) {
            return $this->save_data_reservation(
                $reserve_date,
                $reserve_date,
                $emp_id,
                $emp_com_id,
                $shift_id,
                $shift_start,
                "in",
                $reserve_data['reserve_route'],
                $reserve_data['reserve_point'],
                date("Y-m-d"),
                date("h:i:s"),
                $emp_id,
                $status,
                "Mobile",
                $shift_round_start
            );
        } elseif ($reservation->reserve_status === 'Cancelled') {
            return $this->update_data_reservation(
                $reserve_date,
                $reserve_date,
                $emp_id,
                $emp_com_id,
                $shift_id,
                $shift_start,
                "in",
                $reserve_data['reserve_route'],
                $reserve_data['reserve_point'],
                date("Y-m-d"),
                date("h:i:s"),
                $emp_id,
                $status,
                "Mobile",
                $shift_round_start
            );
        } else {
            return "data_dup";
        }
    }

    private function handle_trip_out($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_end, $shift_type, $status, $shift_round_end)
    {
        $reservation = $this->get_reservation($emp_id, 'out', $reserve_date);
        $new_reserve_date = ($shift_type == "night") ? $this->get_next_day($reserve_date) : $reserve_date;
        if (!$reservation) {

            return $this->save_data_reservation(
                $new_reserve_date,
                $reserve_date,
                $emp_id,
                $emp_com_id,
                $shift_id,
                $shift_end,
                "out",
                $reserve_data['reserve_route_out'],
                $reserve_data['reserve_point_out'],
                date("Y-m-d"),
                date("HH:i:s"),
                $emp_id,
                $status,
                "Mobile",
                $shift_round_end
            );
        } elseif ($reservation->reserve_status === 'Cancelled') {
            return $this->update_data_reservation(
                $new_reserve_date,
                $reserve_date,
                $emp_id,
                $emp_com_id,
                $shift_id,
                $shift_end,
                "out",
                $reserve_data['reserve_route_out'],
                $reserve_data['reserve_point_out'],
                date("Y-m-d"),
                date("HH:i:s"),
                $emp_id,
                $status,
                "Mobile",
                $shift_round_end
            );
        } else {
            return "data_dup";
        }
    }

    private function handle_two_way_trip($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $shift_end, $shift_type, $status, $shift_round_start, $shift_round_end)
    {
        // Handle the "in" part of the two-way trip
        $result_in = $this->handle_trip_in($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_start, $status, $shift_round_start);
        if ($result_in == "Data Duplicate") {
            return $result_in; // ถ้า trip "in" ซ้ำ ก็ return ได้เลย
        }

        // Handle the "out" part of the two-way trip
        $result_out = $this->handle_trip_out($reserve_date, $reserve_data, $emp_id, $emp_com_id, $shift_id, $shift_end, $shift_type, $status, $shift_round_end);
        if ($result_out == "Data Duplicate") {
            return $result_out; // ถ้า trip "out" ซ้ำ ก็ return ได้เลย
        }

        // Return both results
        return $result_out;
    }


    private function get_next_day($date)
    {
        return date('Y-m-d', strtotime('+1 day', strtotime($date)));
    }

    private function get_reservation($emp_id, $trip, $reserve_date)
    {
        return $this->db->select('reserve_status')
            ->from('reserve')
            ->where('reserve_mem_id', $emp_id)
            ->where('reserve_trip', $trip)
            ->where('reserve_working_date', $reserve_date)
            ->get()->row();
    }

    // public function save_data_reservation($reserve_date, $reserve_working_date, $emp_id, $emp_com_id, $shift_id, $shift_start, $trip, $reserve_route, $reserve_point, $curr_date, $curr_time, $reserve_reg_mem_id, $status, $reserve_dev, $shift_round)
    // {
    //     try {
    //         $sql = "EXEC SaveReservation ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
    //         $params = [
    //             $reserve_date,
    //             $reserve_working_date,
    //             $emp_id,
    //             $emp_com_id,
    //             $shift_id,
    //             $shift_start,
    //             $trip,
    //             $reserve_route,
    //             $reserve_point,
    //             $curr_date,
    //             $curr_time,
    //             $reserve_reg_mem_id,
    //             $status,
    //             $reserve_dev,
    //             $shift_round
    //         ];
    //         $this->db->query($sql, $params);
    //         // Return array on success
    //         return $status;
    //     } catch (Exception $e) {
    //         // Return empty array on error
    //         return [
    //             'reserve_date' => "Error",
    //             'trip' => "Error",
    //             'status' => "Error"
    //         ];
    //     }
    // }
    // public function update_data_reservation($reserve_date, $reserve_working_date, $emp_id, $emp_com_id, $shift_id, $shift_start, $trip, $reserve_route, $reserve_point, $curr_date, $curr_time, $reserve_reg_mem_id, $status, $reserve_dev, $shift_round)
    // {
    //     try {
    //         $sql = "EXEC UpdateReservation ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
    //         $params = [
    //             $reserve_date,
    //             $reserve_working_date,
    //             $emp_id,
    //             $emp_com_id,
    //             $shift_id,
    //             $shift_start,
    //             $trip,
    //             $reserve_route,
    //             $reserve_point,
    //             $curr_date,
    //             $curr_time,
    //             $reserve_reg_mem_id,
    //             $status,
    //             $reserve_dev,
    //             $shift_round
    //         ];
    //         $this->db->query($sql, $params);
    //         // Return array on success
    //         return $status;
    //     } catch (Exception $e) {
    //         // Return empty array on error
    //         return [
    //             'reserve_date' => "Error",
    //             'trip' => "Error",
    //             'status' => "Error"
    //         ];
    //     }
    // }

    public function save_data_reservation($reserve_date, $reserve_working_date, $emp_id, $emp_com_id, $shift_id, $shift_start, $trip, $reserve_route, $reserve_point, $curr_date, $curr_time, $reserve_reg_mem_id, $status, $reserve_dev, $shift_round)
    {
        // เริ่มต้น Transaction
        $this->db->trans_start();

        $ck_emp_plant = $this->session->userdata('emp_plant');

        try {
            // คำสั่ง SQL สำหรับเรียก Stored Procedure
            $sql = "EXEC SaveReservation ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
            $params = [
                $reserve_date,
                $reserve_working_date,
                $emp_id,
                $emp_com_id,
                $shift_id,
                $shift_start,
                $trip,
                intval($ck_emp_plant),
                $reserve_route,
                $reserve_point,
                $curr_date,
                $curr_time,
                $reserve_reg_mem_id,
                $status,
                $reserve_dev,
                $shift_round
            ];

            // ดำเนินการ Query
            $this->db->query($sql, $params);

            // ตรวจสอบว่าการ Query สำเร็จหรือไม่
            if ($this->db->trans_status() === FALSE) {
                // ถ้ามีข้อผิดพลาดเกิดขึ้น ให้ทำการ Rollback
                $this->db->trans_rollback();
                return [
                    'reserve_date' => "Error",
                    'trip' => "Error",
                    'status' => "Error"
                ];
            } else {
                // ถ้าการ Query สำเร็จ ให้ทำการ Commit
                $this->db->trans_commit();

                // คืนค่าข้อมูลเมื่อสำเร็จ
                return $status;
            }
        } catch (Exception $e) {
            // หากเกิดข้อผิดพลาดในขั้นตอนใดๆ ให้ทำการ Rollback
            $this->db->trans_rollback();
            // บันทึก Log ข้อผิดพลาด
            log_message('error', 'Error during save_data_reservation: ' . $e->getMessage());

            return [
                'reserve_date' => "Error",
                'trip' => "Error",
                'status' => "Error"
            ];
        }
    }

    public function update_data_reservation($reserve_date, $reserve_working_date, $emp_id, $emp_com_id, $shift_id, $shift_start, $trip, $reserve_route, $reserve_point, $curr_date, $curr_time, $reserve_reg_mem_id, $status, $reserve_dev, $shift_round)
    {
        $ck_emp_plant = $this->session->userdata('emp_plant');

        try {
            // เริ่ม transaction
            $this->db->trans_start();

            $sql = "EXEC UpdateReservation ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
            $params = [
                $reserve_date,
                $reserve_working_date,
                $emp_id,
                $emp_com_id,
                $shift_id,
                $shift_start,
                $trip,
                intval($ck_emp_plant),
                $reserve_route,
                $reserve_point,
                $curr_date,
                $curr_time,
                $reserve_reg_mem_id,
                $status,
                $reserve_dev,
                $shift_round
            ];
            $this->db->query($sql, $params);

            // ตรวจสอบว่าไม่มีข้อผิดพลาด
            if ($this->db->trans_status() === FALSE) {
                throw new Exception("Transaction Failed");
            }

            // ยืนยันการทำงานของ transaction
            $this->db->trans_complete();

            // Return array on success
            return $status;
        } catch (Exception $e) {
            // Rollback เมื่อเกิดข้อผิดพลาด
            $this->db->trans_rollback();

            return [
                'reserve_date' => "Error",
                'trip' => "Error",
                'status' => "Error",
                'message' => $e->getMessage() // แสดงข้อความ error เพื่อ debug
            ];
        }
    }
    
}
