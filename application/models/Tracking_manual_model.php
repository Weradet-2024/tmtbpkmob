<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2566-10-09
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Tracking_manual_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_data_route_by_trip($chk_com_id, $route_trip, $plant_id)
    {
        if ($route_trip == "in") {
            // Set route and pickup/dropoff queries
            $this->db->select('*');
            $this->db->from('route'); // ระบุฐานข้อมูลและตาราง
            $this->db->where('route_status', 'active');
            $this->db->where('route_com_id', $chk_com_id);
            $this->db->where('route_trip', $route_trip);
            // กำหนดเงื่อนไขแบบ OR ด้วยวงเล็บ
            $this->db->group_start();
            $this->db->where('route_plant', $plant_id);
            $this->db->or_where("',' + route_plant_member + ',' LIKE '%,$plant_id,%'");
            $this->db->group_end();
            $this->db->order_by('route_plant, route_code');
            return $query_route = $this->db->get()->result();
        } else {
            $this->db->select('*');
            $this->db->from('route_out'); // ระบุฐานข้อมูลและตาราง
            $this->db->where('route_out_status', 'active');
            $this->db->where('route_out_com_id', $chk_com_id);
            $this->db->where('route_out_trip', $route_trip);
            // กำหนดเงื่อนไขแบบ OR ด้วยวงเล็บ
            $this->db->group_start();
            $this->db->where('route_out_plant', $plant_id);
            $this->db->or_where("',' + route_out_plant_member + ',' LIKE '%,$plant_id,%'");
            $this->db->group_end();
            $this->db->order_by('route_out_plant, route_out_code');
            //return $query_route = $this->db->get()->result();
            $query_route = $this->db->get();
            $arr_result = [];
            foreach($query_route->result() as $rs){
                $route_id = $rs->route_out_id;
                $route_code = $rs->route_out_code;
                $route_name = $rs->route_out_name;
                $route_status = $rs->route_out_status;
                $row = [];
                $row['route_id'] = $route_id;
                $row['route_code'] = $route_code;
                $row['route_name'] = $route_name;
                $row['route_status'] = $route_status;
                $arr_result[] = $row;
            }
            return $arr_result;
        }
    }

    public function get_plant_data()
    {
        return $this->db->get_where('plant', ['plant_status' => 'active'])->result();
    }

    public function chk_active_route($ck_emp_com_id, $trip, $route, $curr_date, $curr_time, $plant_id)
    {
        $this->db->select('COUNT(post_id) AS count_post_id');
        $this->db->from('post');
        $this->db->where('post_com_id', $ck_emp_com_id);
        $this->db->where('post_date', $curr_date);
        $this->db->where('post_trip', $trip);
        if ($route != "all") {
            $this->db->where("( ',' + post_combined_route + ',' LIKE '%,$route,%')");
        }

        $query_count_route = $this->db->get()->result();


        $count_post_id = $query_count_route[0]->count_post_id;

        if ($count_post_id <= 0) {

            return "Cannot";

        } else {


            if($trip == "in"){
                $this->db->distinct('post_combined_route');
                $this->db->select('post_veh_id, post_drv_id, post_time, post_combined_route, route_code');
                $this->db->from('post');
                $this->db->join('route', "CHARINDEX(',' + CAST(route.route_id AS VARCHAR) + ',', ',' + post_combined_route + ',') > 0", 'left');
                $this->db->where('post_com_id', $ck_emp_com_id);
                $this->db->where('post_trip', $trip);
                $this->db->where('post_date', $curr_date);
                $this->db->where('post_drv_id !=', 0);
                $this->db->group_start();
                $this->db->where('route_plant', $plant_id);
                $this->db->or_where("',' + route_plant_member + ',' LIKE '%,$plant_id,%'");
                $this->db->group_end();


                if ($route != 'all') {
                    $this->db->where("( ',' + post_combined_route + ',' LIKE '%,$route,%')");
                }

                $this->db->order_by('route_code', 'ASC');
                $query_post = $this->db->get();

                $arr_result = [];

                // Loop data
                foreach ($query_post->result() as $rs1) {

                    $post_id = isset($rs1->post_id) ? $rs1->post_id : "";
                    $post_time = isset($rs1->post_time) ? $rs1->post_time : "";
                    $post_combined_route = isset($rs1->post_combined_route) ? $rs1->post_combined_route : "";
                    $post_veh_id = isset($rs1->post_veh_id) ? $rs1->post_veh_id : "";
                    $post_drv_id = isset($rs1->post_drv_id) ? $rs1->post_drv_id : "";

                    // Get route
                    
                    $this->db->select('route_code, route_name, route_status');
                    $this->db->from('route');
                    $this->db->where('route_com_id = ', $ck_emp_com_id);
                    $this->db->where('route_trip = ', $trip);

                    $post_route_id = explode(', ', $post_combined_route);

                    $this->db->where('route_id', $post_route_id[0]);

                    $query2 = $this->db->get();

                    if ($query2->num_rows() > 0) {
                        $data_query2 = $query2->result();
                        $route_data = $data_query2[0];

                        $route_id = isset($route_data->route_id) ? $route_data->route_id : "";
                        $route_code = isset($route_data->route_code) ? $route_data->route_code : "";
                        $route_name = isset($route_data->route_name) ? $route_data->route_name : "";
                        $route_status = isset($route_data->route_status) ? $route_data->route_status : "";
                    } else {
                        $route_code = "";
                        $route_name = "";
                        $route_status = "";
                    }

                    // Get data veh
                    $this->db->select('veh_license');
                    $this->db->from('veh');
                    $this->db->where('veh_id = ', $post_veh_id);

                    $query_veh = $this->db->get();

                    if ($query_veh->num_rows() > 0) {
                        $data_query_veh = $query_veh->result();
                        $veh_license = isset($data_query_veh[0]->veh_license) ? $data_query_veh[0]->veh_license : "";
                    } else {
                        $veh_license = "";
                    }

                    // Get tracking data to track the vehicle
                    $sql4 = "SELECT TOP 1 * FROM tracking 
                            WHERE tracking_com_id='$ck_emp_com_id' 
                            AND tracking_reg_date='$curr_date' 
                            AND tracking_time='$post_time' 
                            AND tracking_drv_id='$post_drv_id' 
                            AND tracking_trip='$trip' 
                            AND ( ',' + tracking_combined_route + ',' LIKE '%,$post_route_id[0],%')
                            AND tracking_veh_id!='0' 
                            AND tracking_reg_time IS NOT NULL 
                            ORDER BY tracking_id DESC";
                    $query4 = $this->db->query($sql4);
                    $data_query4 = $query4->result();

                    if ($query4->num_rows() > 0) {
                        $tracking_data = $data_query4[0];

                        $tracking_id = isset($tracking_data->tracking_id) ? $tracking_data->tracking_id : "";
                        $tracking_reg_time = isset($tracking_data->tracking_reg_time) ? $tracking_data->tracking_reg_time : "";
                        $tracking_time = isset($tracking_data->tracking_time) ? $tracking_data->tracking_time : "";
                    } else {
                        $tracking_id = "";
                        $tracking_reg_time = "";
                        $tracking_time = "";
                    }

                    // Time difference calculation
                    $time1 = strtotime($curr_time);
                    $time2 = strtotime($tracking_reg_time);
                    $time_diff = ($time1 - $time2) / 60;

                    // Initialize row array
                    $row = [];

                    // Populate row based on conditions
                    if ($tracking_id == "") {
                        $row['post_id'] = $post_id;
                        $row['route_name'] = $route_name;
                        $row['post_time'] = $post_time;
                        $row['veh_license'] = $veh_license;
                        $row['curr_date'] = $curr_date;
                        $row['trip'] = $trip;
                        $row['post_combined_route'] = $post_combined_route;
                        $row['post_veh_id'] = $post_veh_id;
                        $row['post_drv_id'] = $post_drv_id;
                        $row['route_status'] = $route_status;
                        $row['status'] = 'Low_Signal';
                    } else if ($time_diff >= 10) {
                        $row['post_id'] = $post_id;
                        $row['route_name'] = $route_name;
                        $row['post_time'] = $post_time;
                        $row['veh_license'] = $veh_license;
                        $row['curr_date'] = $curr_date;
                        $row['trip'] = $trip;
                        $row['post_combined_route'] = $post_combined_route;
                        $row['post_veh_id'] = $post_veh_id;
                        $row['post_drv_id'] = $post_drv_id;
                        $row['route_status'] = $route_status;
                        $row['status'] = 'Success';
                    } else {
                        $row['post_id'] = $post_id;
                        $row['route_name'] = $route_name;
                        $row['post_time'] = $post_time;
                        $row['veh_license'] = $veh_license;
                        $row['curr_date'] = $curr_date;
                        $row['trip'] = $trip;
                        $row['post_combined_route'] = $post_combined_route;
                        $row['post_veh_id'] = $post_veh_id;
                        $row['post_drv_id'] = $post_drv_id;
                        $row['route_status'] = $route_status;
                        $row['status'] = 'Go';
                    }
                    // Add the row to the result array
                    $arr_result[] = $row;
                } // End of foreach
                return $arr_result;
            }else{
                $this->db->distinct('post_combined_route');
                $this->db->select('post_veh_id, post_drv_id, post_time, post_combined_route, route_out_code');
                $this->db->from('post');
                $this->db->join('route_out', "CHARINDEX(',' + CAST(route_out.route_out_id AS VARCHAR) + ',', ',' + post_combined_route + ',') > 0", 'left');
                $this->db->where('post_com_id', $ck_emp_com_id);
                $this->db->where('post_trip', $trip);
                $this->db->where('post_date', $curr_date);
                $this->db->where('post_drv_id !=', 0);
                $this->db->group_start();
                $this->db->where('route_out_plant', $plant_id);
                $this->db->or_where("',' + route_out_plant_member + ',' LIKE '%,$plant_id,%'");
                $this->db->group_end();


                if ($route != 'all') {
                    $this->db->where("( ',' + post_combined_route + ',' LIKE '%,$route,%')");
                }

                $this->db->order_by('route_out_code', 'ASC');
                $query_post = $this->db->get();

                $arr_result = [];

                // Loop data
                foreach ($query_post->result() as $rs1) {

                    $post_id = isset($rs1->post_id) ? $rs1->post_id : "";
                    $post_time = isset($rs1->post_time) ? $rs1->post_time : "";
                    $post_combined_route = isset($rs1->post_combined_route) ? $rs1->post_combined_route : "";
                    $post_veh_id = isset($rs1->post_veh_id) ? $rs1->post_veh_id : "";
                    $post_drv_id = isset($rs1->post_drv_id) ? $rs1->post_drv_id : "";

                    // Get route
                    $this->db->select('route_out_code, route_out_name, route_out_status');
                    $this->db->from('route_out');
                    $this->db->where('route_out_com_id = ', $ck_emp_com_id);
                    $this->db->where('route_out_trip = ', $trip);

                    $post_route_id = explode(', ', $post_combined_route);

                    $this->db->where('route_out_id', $post_route_id[0]);

                    $query2 = $this->db->get();

                    if ($query2->num_rows() > 0) {
                        $data_query2 = $query2->result();
                        $route_data = $data_query2[0];

                        $route_id       = isset($route_data->route_out_id) ?        $route_data->route_out_id : "";
                        $route_code     = isset($route_data->route_out_code) ?      $route_data->route_out_code : "";
                        $route_name     = isset($route_data->route_out_name) ?      $route_data->route_out_name : "";
                        $route_status   = isset($route_data->route_out_status) ?    $route_data->route_out_status : "";
                    } else {
                        $route_code = "";
                        $route_name = "";
                        $route_status = "";
                    }

                    // Get data veh
                    $this->db->select('veh_license');
                    $this->db->from('veh');
                    $this->db->where('veh_id = ', $post_veh_id);

                    $query_veh = $this->db->get();

                    if ($query_veh->num_rows() > 0) {
                        $data_query_veh = $query_veh->result();
                        $veh_license = isset($data_query_veh[0]->veh_license) ? $data_query_veh[0]->veh_license : "";
                    } else {
                        $veh_license = "";
                    }

                    // Get tracking data to track the vehicle
                    $sql4 = "SELECT TOP 1 * FROM tracking 
                            WHERE tracking_com_id='$ck_emp_com_id' 
                            AND tracking_reg_date='$curr_date' 
                            AND tracking_time='$post_time' 
                            AND tracking_drv_id='$post_drv_id' 
                            AND tracking_trip='$trip' 
                            AND ( ',' + tracking_combined_route + ',' LIKE '%,$post_route_id[0],%')
                            AND tracking_veh_id!='0' 
                            AND tracking_reg_time IS NOT NULL 
                            ORDER BY tracking_id DESC";
                    $query4 = $this->db->query($sql4);
                    $data_query4 = $query4->result();

                    if ($query4->num_rows() > 0) {
                        $tracking_data = $data_query4[0];

                        $tracking_id = isset($tracking_data->tracking_id) ? $tracking_data->tracking_id : "";
                        $tracking_reg_time = isset($tracking_data->tracking_reg_time) ? $tracking_data->tracking_reg_time : "";
                        $tracking_time = isset($tracking_data->tracking_time) ? $tracking_data->tracking_time : "";
                    } else {
                        $tracking_id = "";
                        $tracking_reg_time = "";
                        $tracking_time = "";
                    }

                    // Time difference calculation
                    $time1 = strtotime($curr_time);
                    $time2 = strtotime($tracking_reg_time);
                    $time_diff = ($time1 - $time2) / 60;

                    // Initialize row array
                    $row = [];

                    // Populate row based on conditions
                    if ($tracking_id == "") {
                        $row['post_id'] = $post_id;
                        $row['route_name'] = $route_name;
                        $row['post_time'] = $post_time;
                        $row['veh_license'] = $veh_license;
                        $row['curr_date'] = $curr_date;
                        $row['trip'] = $trip;
                        $row['post_combined_route'] = $post_combined_route;
                        $row['post_veh_id'] = $post_veh_id;
                        $row['post_drv_id'] = $post_drv_id;
                        $row['route_status'] = $route_status;
                        $row['status'] = 'Low_Signal';
                    } else if ($time_diff >= 30) {
                        $row['post_id'] = $post_id;
                        $row['route_name'] = $route_name;
                        $row['post_time'] = $post_time;
                        $row['veh_license'] = $veh_license;
                        $row['curr_date'] = $curr_date;
                        $row['trip'] = $trip;
                        $row['post_combined_route'] = $post_combined_route;
                        $row['post_veh_id'] = $post_veh_id;
                        $row['post_drv_id'] = $post_drv_id;
                        $row['route_status'] = $route_status;
                        $row['status'] = 'Success';
                    } else {
                        $row['post_id'] = $post_id;
                        $row['route_name'] = $route_name;
                        $row['post_time'] = $post_time;
                        $row['veh_license'] = $veh_license;
                        $row['curr_date'] = $curr_date;
                        $row['trip'] = $trip;
                        $row['post_combined_route'] = $post_combined_route;
                        $row['post_veh_id'] = $post_veh_id;
                        $row['post_drv_id'] = $post_drv_id;
                        $row['route_status'] = $route_status;
                        $row['status'] = 'Go';
                    }
                    // Add the row to the result array
                    $arr_result[] = $row;
                } // End of foreach
                return $arr_result;
            }
            
        }
    }
}
