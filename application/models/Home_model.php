<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-08
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Home_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('utility_helper');
    }
    public function get_menu_data()
    {
        $query = $this->db->get_where('module_passenger', ['module_passenger_status' => 'active'])->result_array();
        return $query;
    }
    public function chk_lang($set_lang, $emp_code)
    {
        $sql = "UPDATE emp 
                SET emp_lang = ? 
                WHERE emp_id = ?";
        $query = $this->db->query($sql, array($set_lang, $emp_code));
        if (!$query) {
            throw new Exception('Update failed');
        }
        return true;
    }
    public function get_data($ck_emp_id)
    {
        $this->db->select('
        emp.emp_code, emp.emp_name, emp.emp_lname,
        pickup.pickup_code, pickup.pickup_name, pickup.pickup_timein, pickup.pickup_timeout,
        route.route_code, route.route_name,

        dropoff.dropoff_code, dropoff.dropoff_name,
        route_out.route_out_code, route_out.route_out_name
    ');
        $this->db->from('emp');
        $this->db->join('route', 'route.route_id = emp.emp_route_id', 'left');
        $this->db->join('pickup', 'pickup.pickup_id = emp.emp_pick_id', 'left');
        $this->db->join('route_out', 'route_out.route_out_id = emp.emp_route_out_id', 'left');
        $this->db->join('dropoff', 'dropoff.dropoff_id = emp.emp_drop_id', 'left');
        $this->db->where('emp_id', $ck_emp_id);
        $query = $this->db->get();

        $result = $query->row(); // แค่คนเดียวก็พอ

        return [
            'emp_data' => $result
        ];
    }


    public function get_trip($departure_date, $ck_emp_id)
    {
        $this->db->select('*');
        $this->db->from('reserve');
        $this->db->where('reserve_date', $departure_date);
        $this->db->where('reserve_mem_id', $ck_emp_id);
        $this->db->where('reserve_status != ', 'Cancelled');
        $this->db->where("getdate() < reserve_date+ ' ' + reserve_time");
        $query = $this->db->get();
        return $query->result();
    }
    //update by weradet 
    //update date: 2025-06-08
    //Description: Query new function 
    public function get_tickets_data($departure_date, $ck_emp_id)
    {
        $curr_time = date('H:i:s');

        // เรียก stored procedure ที่อัพเดทแล้ว (รับแค่ emp_id เดียว)
        $sql = "EXEC [GetTicketReservation] ?";
        $params = [
            strVal($ck_emp_id)
        ];

        // ดำเนินการ Query
        $query = $this->db->query($sql, $params)->result_array();

        $results = [];

        foreach ($query as $rs) {
            // Common fields
            $reserve_trip = $rs['reserve_trip'];
            $reserve_date = $rs['reserve_date'];
            $reserve_round = $rs['reserve_round'];
            $reserve_route = $rs['reserve_route'];
            $post_drv_id = $rs['post_drv_id'];
            $post_id = $rs['post_id'];
            $reserve_point = $rs['reserve_point'];
            $post_combined_route = $rs['post_combined_route'];
            $time = '';

            // เลือกจุดขึ้นหรือลงตาม trip
            if ($reserve_trip === 'in') {
                $point_name = $rs['pickup_name'];
                $point_lat = $rs['pickup_lat'];
                $point_long = $rs['pickup_long'];
                $time = $rs['pickup_timein'];

                $route_name = $rs['route_in_name'];
                $route_code = $rs['route_in_code'];

            } else {
                $point_name = $rs['dropoff_name'];
                $point_lat = $rs['dropoff_lat'];
                $point_long = $rs['dropoff_long'];
                $route_name = $rs['route_out_name'];
                $route_code = $rs['route_out_code'];
            }

            // get latest tracking
            $sql_latest = "
        SELECT TOP 1 * FROM tracking 
        WHERE tracking_reg_date = '$reserve_date' 
        AND tracking_time = '$reserve_round' 
        AND tracking_drv_id = '$post_drv_id' 
        AND tracking_trip = '$reserve_trip' 
        AND tracking_post_id = '$post_id'
        AND CHARINDEX(',' + CAST('$reserve_route' AS VARCHAR) + ',', ',' + tracking_combined_route + ',') > 0
        AND tracking_veh_id != '0' 
        AND tracking_reg_time IS NOT NULL 
        ORDER BY tracking_id DESC";
            $query_latest = $this->db->query($sql_latest);
            $data_latest = $query_latest->row();

            // get earliest tracking
            $sql_earliest = "
        SELECT TOP 1 * FROM tracking 
        WHERE tracking_reg_date = '$reserve_date' 
        AND tracking_time = '$reserve_round' 
        AND tracking_drv_id = '$post_drv_id' 
        AND tracking_trip = '$reserve_trip' 
        AND tracking_combined_route = '$post_combined_route'
        AND tracking_post_id = '$post_id'
        AND tracking_veh_id != '0' 
        AND tracking_reg_time IS NOT NULL 
        ORDER BY tracking_id ASC";
            $query_earliest = $this->db->query($sql_earliest);
            $data_earliest = $query_earliest->row();

            $tracking_post_status = '';
            $tracking_id = '';
            $diff_seconds = 0;

            if (!empty($data_latest)) {
                if ($query_latest->num_rows() > 0 && $query_earliest->num_rows() > 0) {
                    $time_latest = strtotime($data_latest->tracking_reg_time);
                    $time_earliest = strtotime($data_earliest->tracking_reg_time);

                    $diff_seconds = abs($time_latest - $time_earliest);
                }

                if ($diff_seconds < 60) {
                    continue;
                }

                // คำนวณความต่างของเวลา
                $time1 = strtotime($curr_time);
                $time2 = strtotime($data_latest->tracking_reg_time);
                $time_diff = ($time1 - $time2) / 60;

                // ตรวจสอบสถานะการโพสต์
                $tracking_post_status = ($time_diff >= 30) ? 'success' : 'go';
                $tracking_id = $data_latest->tracking_id;
            }

            $results[] = [
                'reserve_id' => $rs['reserve_id'],
                'reserve_com_id' => $rs['reserve_com_id'],
                'reserve_trip' => $reserve_trip,
                'reserve_round' => $rs['reserve_round'],
                'reserve_date' => $rs['reserve_date'],
                'reserve_route' => $rs['reserve_route'],
                'reserve_point' => $rs['reserve_point'],
                'reserve_status' => $rs['reserve_status'],
                'reserve_time' => $rs['reserve_time'],

                // Route info
                'route_in_id' => $rs['route_in_id'],
                'route_in_code' => $rs['route_in_code'],
                'route_in_name' => $rs['route_in_name'],
                'route_out_id' => $rs['route_out_id'],
                'route_out_code' => $rs['route_out_code'],
                'route_out_name' => $rs['route_out_name'],

                // Post info
                'post_id' => $rs['post_id'],
                'post_veh_id' => $rs['post_veh_id'],
                'post_drv_id' => $rs['post_drv_id'],
                'post_time' => $rs['post_time'],
                'post_combined' => $rs['post_combined_route'],

                // Vehicle & Driver
                'veh_license' => $rs['veh_license'],
                'driver_id' => $rs['driver_id'],
                'driver_fullname' => $rs['driver_name'] . ' ' . $rs['driver_lname'],

                // Point info (pickup or dropoff based on trip)
                'point_name' => $point_name,
                'point_lat' => $point_lat,
                'point_long' => $point_long,
                'route_name' => $route_name,
                'route_code' => $route_code,
                'time' => $time,
                'tracking_post_status' => $tracking_post_status,
                'tracking_id' => $tracking_id,
                'sql_tracking' => $sql_latest, // SQL tracking if available
                'time_diff' => $diff_seconds
            ];
        }

        return $results;
    }

    public function get_fcm_token_by_emp_id($emp_id)
    {
        $query = $this->db->get_where('fcm_token', ['fcm_emp_id' => $emp_id]);
        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($query->num_rows() > 0) {
            return $query->result(); // คืนค่าข้อมูลถ้ามี
        } else {
            return false; // คืนค่า false ถ้าไม่มีข้อมูล
        }
    }

}
