<?php
/*
* @author Weradet Nopsomboon
* @Create Date 2567-08-28
*/

defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_result_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function get_data($data, $ck_emp_id, $ck_emp_com_id)
    {
        $curr_date = date('Y-m-d');
        $curr_time = date("H:i:s");
        $arr_result = array();
        //get emp data
        $this->db->select('*');
        $this->db->from('emp');
        $this->db->where('emp_id', $ck_emp_id);
        $query_emp = $this->db->get();
        foreach($query_emp->result() as $rsemp){
            $emp_id     = $rsemp->emp_id;
            $emp_com_id = $rsemp->emp_com_id;
            $emp_name   = $rsemp->emp_name;
            $emp_lname  = $rsemp->emp_lname; 
        }
        //get post data
        $this->db->select('TOP(1) *');
        $this->db->from('post');
        $this->db->join('veh', 'veh.veh_id = post.post_veh_id', 'left');
        $this->db->where('post.post_date = ', $curr_date);
        $this->db->where('veh_id = ', $data[3]);
        $this->db->order_by('post_id', 'desc');
        $query_post = $this->db->get();
        if ($query_post->num_rows() <= 0) {
            return "bus_service";
        } else {
            foreach ($query_post->result() as $rs1) {
                $post_id = $rs1->post_id;
                $post_combined_route =   $rs1->post_combined_route;
                $post_veh_id         =   $rs1->post_veh_id;
                $post_drv_id         =   $rs1->post_drv_id;
                $post_trip           =   $rs1->post_trip;
                $post_time           =   $rs1->post_time;
                if ($rs1->post_trip == "in") {
                    $trip_veh = "รับเข้า";
                } else  if ($rs1->post_trip == "out") {
                    $trip_veh = "รับออก";
                }
            } // for each
            //get route data
            $route_id       = '';
            $route_code   = '';
            $route_name   = '';
            $route_status = '';

            $this->db->select('*');
            $this->db->from('route');
            $this->db->where('route_id = ', $post_combined_route);
            $this->db->where('route_trip = ', $post_trip);
            $query_route = $this->db->get();
            foreach ($query_route->result() as $rsroute) {
                $route_id        =   $rsroute->route_id;
                $route_code      =   $rsroute->route_code;
                $route_name      =   $rsroute->route_name;
                $route_status    =   $rsroute->route_status;
            } //for each route 
            //get veh data
            $this->db->select('*');
            $this->db->from('veh');
            $this->db->where('veh_id = ', $post_veh_id);
            $this->db->order_by('veh_id', 'DESC');
            $query_veh = $this->db->get();
            foreach ($query_veh->result() as $rsveh) {
                $veh_id      =   $rsveh->veh_id;
                $veh_code    =   $rsveh->veh_code;
                $veh_type    =   $rsveh->veh_type;
                $veh_license =   $rsveh->veh_license;
            } //for each veh
            //get driver data
            $this->db->select('*');
            $this->db->from('driver');
            $this->db->where('driver_veh = ', $post_veh_id);
            $query_drv = $this->db->get();
            $driver_id      =  '';
            $driver_name    =  '';
            $driver_lname   =  '';
            $driver_tel     =  '';
            foreach ($query_drv->result() as $rsdrv) {
                $driver_id      =   $rsdrv->driver_id;
                $driver_name    =   $rsdrv->driver_name;
                $driver_lname   =   $rsdrv->driver_lname;
                $driver_tel     =   $rsdrv->driver_tel;
            } //fort each driver

           $sql = "SELECT * from reserve where reserve_mem_id = '$ck_emp_id' 
           and reserve_date = '$curr_date' and reserve_round = '$post_time'
           and reserve_trip = '$post_trip' and reserve_route IN ($post_combined_route)";
           $query_reserve = $this->db->query($sql);
            
            //check emp was reserve first
            if ($query_reserve->num_rows() > 0) {
                $reserve = "YES";
                $data_reserve = $query_reserve->result();
                $route_id = $data_reserve[0]->reserve_route;
            } else {
                $reserve = "NO";
            }
            $arr_result['status']      = $reserve;
            $arr_result['name']        = $emp_name . " " . $emp_lname;
            $arr_result['date_time']   = $curr_date . " " . $curr_time;
            $arr_result['route']       = $route_code . " " . $route_name;
            $arr_result['trip']        = $trip_veh . " " . $post_time . "น.";
            $arr_result['veh_type']    = $veh_type;
            $arr_result['veh_license'] = $veh_license;
            $arr_result['veh_code']    = $veh_code;
            $arr_result['driver']      = $driver_name . " " . $driver_lname;
            $arr_result['driver_tel']  = $driver_tel;
            //insert attendance here
            $data_insert = array(
                'attendance_com_id'    => $ck_emp_com_id,
                'attendance_mem_id'    => $ck_emp_id,
                'attendance_date'      => $curr_date,
                'attendance_time'      => $curr_time,
                'attendance_drv_id'    => $driver_id,
                'attendance_veh_id'    => $veh_id,
                //'attendance_post_id'    => $post_id,
                'attendance_route_id'  => $route_id,
                'attendance_trip'      => $post_trip,
                'attendance_trip_time' => $post_time,
                'attendance_type'      => "QRCODE",
                'attendance_reserve'   => $reserve
            );
            $this->db->insert('attendance', $data_insert);
            return $arr_result;
        } //check post for driver 

    }
}
