<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2566-10-09
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Tracking_info_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_data(
        $ck_emp_id,
        $ck_emp_com_id,
        $tracking_date,
        $tracking_time,
        $tracking_trip,
        $tracking_route,
        $tracking_veh_id,
        $tracking_drv_id
    ) {
        // Initialize result array
        $result = [];

        $route_ids = explode(',', $tracking_route);

        if($tracking_trip == "in"){
            $this->db->select('route_code, route_name');
            $this->db->from('route');
            $this->db->where_in('route_id', $route_ids);
            $query_route = $this->db->get();
            $route = $query_route->row();
            $result['route'] = isset($route->route_code) ? $route->route_code . " " . $route->route_name : '';
        }else{
            // Get route data
            $this->db->select('route_out_code, route_out_name');
            $this->db->from('route_out');
            $this->db->where_in('route_out_id', $route_ids);
            $query_route = $this->db->get();
            $route = $query_route->row();
            $result['route'] = isset($route->route_out_code) ? $route->route_out_code . " " . $route->route_out_name : '';
        }
    

        // Get driver data
        $this->db->select('driver_name, driver_lname, driver_tel, veh_id, veh_license');
        $this->db->from('driver');
        $this->db->join('veh', 'veh.veh_id = driver.driver_veh', 'left');
        $this->db->where('driver_id', $tracking_drv_id);
        $query_driver = $this->db->get();
        $driver = $query_driver->row();
        $result['veh_license'] = isset($driver->veh_license) ? $driver->veh_license : '';
        $result['driver'] = isset($driver->driver_name) ? $driver->driver_name . " " . $driver->driver_lname : '';
        $result['driver_tel'] = isset($driver->driver_tel) ? $driver->driver_tel : '';

        // Get reserve count
        $this->db->select('COUNT(reserve_id) as qty');
        $this->db->from('reserve');
        $this->db->where('reserve_trip', $tracking_trip);
        $this->db->where('reserve_date', $tracking_date);
        $this->db->where('reserve_time', $tracking_time);
        $this->db->where('reserve_route', $tracking_route);
        $this->db->where('reserve_status', 'Submitted');
        $query_count = $this->db->get();
        $count = $query_count->row();
        $result['qty'] = isset($count->qty) ? $count->qty : 0;

        return [$result];
    }

    public function get_data_location(
        $ck_emp_id,
        $ck_emp_com_id,
        $ck_emp_plant_id,
        $tracking_date,
        $tracking_time,
        $tracking_trip,
        $tracking_route,
        $tracking_veh_id,
        $tracking_drv_id
    ) {

        // Initialize route array
        $arr_route = [];

        // Retrieve employee data
        $this->db->select('emp_pick_id');
        $this->db->from('emp');
        $this->db->where('emp_com_id', $ck_emp_com_id);
        $this->db->where('emp_id', $ck_emp_id);
        $query_emp = $this->db->get();

        $emp_pick_id = null;
        if ($query_emp->num_rows() > 0) {
            $emp_pick_id = $query_emp->row()->emp_pick_id;
        }

        // Define the condition based on the trip type
        $condition = ($tracking_trip == "in") ?
            ['pickup_route_id' => $tracking_route, 'pickup_status' => 'active'] :
            ['dropoff_route_id' => $tracking_route, 'dropoff_status' => 'active'];

        // Set table and column names based on trip type
        $table = ($tracking_trip == "in") ? 'pickup' : 'dropoff';
        $id_col = ($tracking_trip == "in") ? 'pickup_id' : 'dropoff_id';
        $lat_col = ($tracking_trip == "in") ? 'pickup_lat' : 'dropoff_lat';
        $lon_col = ($tracking_trip == "in") ? 'pickup_long' : 'dropoff_long';
        $name_col = ($tracking_trip == "in") ? 'pickup_name' : 'dropoff_name';
        $com_condition = ($tracking_trip == "in") ? ['pickup_com_id' => $ck_emp_com_id] : ['dropoff_com_id' => $ck_emp_com_id];
        $order_by = ($tracking_trip == "in") ? 'pickup_item' : 'dropoff_item';

        // Retrieve route data
        $this->db->select("$id_col, $lat_col, $lon_col, $name_col");
        $this->db->from($table);
        $this->db->where($condition);
        $this->db->where($com_condition);  // Assuming this is applicable for both in and out
        $this->db->order_by($order_by, 'ASC');  // Order only applies for pickups, adjust if needed
        $query_route = $this->db->get();

        foreach ($query_route->result() as $row) {
            $arr_route[] = [
                "point_id" => $row->$id_col,
                "point_lat" => $row->$lat_col,
                "point_lon" => $row->$lon_col,
                "point_name" => $row->$name_col
            ];
        }

        // Retrieve company data
        $this->db->select('plant_id, plant_lat, plant_long');
        $this->db->from('plant');
        $this->db->where('plant_id', $ck_emp_plant_id);
        $query_com = $this->db->get();

        if ($query_com->num_rows() > 0) {
            $company = $query_com->row();
            $arr_route[] = [
                "point_id" => $company->plant_id,
                "point_lat" => $company->plant_lat,
                "point_lon" => $company->plant_long,
            ];
        }

        // Return the final array of route data
        return $arr_route;
    }

    public function get_plant_data($departure_route, $cookie_lang)
    {
        $arr_route = explode(', ', $departure_route); // เผื่อรวมสายมา
        $this->db->select('plant_id, plant_name, plant_name_th, plant_lat, plant_long, route_name');
        $this->db->from('route');
        $this->db->join('plant', 'route_plant = plant_id', 'left');
        $this->db->where('route_id', $arr_route[0]);
        $data_route = $this->db->get(); // เอามาสัก 1 สายเพื่อ เอา id
        $points = [];
        if ($data_route->num_rows() > 0) {
            $route = $data_route->row();
            if($cookie_lang == 'en'){
                $points[] = [
                    "point_id"   => $route->plant_id,
                    "point_lat"  => $route->plant_lat,
                    "point_lon"  => $route->plant_long,
                    "point_name" => $route->plant_name
                ];
            }else{
                $points[] = [
                    "point_id"   => $route->plant_id,
                    "point_lat"  => $route->plant_lat,
                    "point_lon"  => $route->plant_long,
                    "point_name" => $route->plant_name_th
                ];
            }
            return $points;
        }else{
            $arr_route = explode(', ', $departure_route); // เผื่อรวมสายมา
             $this->db->select('plant_id, plant_name, plant_name_th, plant_lat, plant_long, route_out_name');
             $this->db->from('route_out');
             $this->db->join('plant', 'route_out_plant = plant_id', 'left');
             $this->db->where('route_out_id', $arr_route[0]);
        $data_route = $this->db->get(); // เอามาสัก 1 สายเพื่อ เอา id
        $points = [];
        if ($data_route->num_rows() > 0) {
            $route = $data_route->row();
            if($cookie_lang == 'en'){
                $points[] = [
                    "point_id"   => $route->plant_id,
                    "point_lat"  => $route->plant_lat,
                    "point_lon"  => $route->plant_long,
                    "point_name" => $route->plant_name
                ];
            }else{
                $points[] = [
                    "point_id"   => $route->plant_id,
                    "point_lat"  => $route->plant_lat,
                    "point_lon"  => $route->plant_long,
                    "point_name" => $route->plant_name_th
                ];
            }
            return $points;
        }else{

            return [];

        }

    }
}

    public function get_bus_location(
        $ck_emp_id,
        $ck_emp_com_id,
        $tracking_date,
        $tracking_time,
        $tracking_trip,
        $tracking_route,
        $tracking_veh_id,
        $tracking_drv_id,
        $reserve_point
    ) {
        // Initialize default values
        $row = [
            'location' => '',
            'car_arrage' => false
        ];

        // Fetch the latest tracking data
        $this->db->select('tmp_tracking_id, tmp_tracking_lat, tmp_tracking_lon, tmp_tracking_reg_time');
        $this->db->from('tmp_tracking');
        $this->db->where('tmp_tracking_reg_date', $tracking_date);
        $this->db->where('tmp_tracking_time', $tracking_time);
        $this->db->where("',' + tmp_tracking_combined_route + ',' LIKE '%,$tracking_route,%'");
        $this->db->where('tmp_tracking_veh_id', $tracking_veh_id);
        $this->db->order_by('tmp_tracking_id', 'DESC');
        $this->db->limit(1);

        $query_track = $this->db->get();
        $tracking_data = $query_track->row();

        // If tracking data is found, set the location
        if ($tracking_data) {
            $row['location'] = $tracking_data->tmp_tracking_lat . "," . $tracking_data->tmp_tracking_lon;
            $row['time'] = $tracking_data->tmp_tracking_reg_time;
        }

        // Check if the point is arranged
        if ($reserve_point != "") {
            $this->db->select('id'); 
            $this->db->from('point_arragement');
            $this->db->join('post', 'post.post_id = point_arragement.point_arragement_post_id', 'left');
            $this->db->where([
                'point_arragement.point_arragement_point_id' => $reserve_point,
                'point_arragement.point_arragement_trip' => $tracking_trip,
                'post.post_date' => $tracking_date,
                'point_arragement.point_arragement_veh_id' => $tracking_veh_id
            ]);

            $query_arrage = $this->db->get()->result_array();
            $car_arrage = count($query_arrage) > 0;
            $row['car_arrage'] = $car_arrage;
        }

        return $row;
    }


    public function get_point_reserve(
        $ck_emp_id,
        $tracking_date,
        $tracking_time,
        $tracking_trip,
        $tracking_route,
        $tracking_veh_id
    ) {
        // Fetch the reserve point
        $this->db->select('reserve_point');
        $this->db->from('reserve');
        $this->db->where('reserve_date', $tracking_date);
        $this->db->where('reserve_round', $tracking_time);
        $this->db->where('reserve_trip', $tracking_trip);
        $this->db->where('reserve_route', $tracking_route);
        $this->db->where('reserve_mem_id', $ck_emp_id);
        $this->db->limit(1);

        $query = $this->db->get();
        $reserve_point = $query->row() ? $query->row()->reserve_point : '';

        // Initialize result row
        $row = array('point_lat' => '', 'point_lon' => '');

        // If no reserve point found, return the initialized row
        if (empty($reserve_point)) {
            return $row;
        }

        // Determine table and columns based on trip type
        if ($tracking_trip == "in") {
            $table = 'pickup';
            $id_column = 'pickup_id';
            $lat_column = 'pickup_lat';
            $lon_column = 'pickup_long';
            $name_column = 'pickup_name';
            $status_column = 'pickup_status';
        } else {
            $table = 'dropoff';
            $id_column = 'dropoff_id';
            $lat_column = 'dropoff_lat';
            $lon_column = 'dropoff_long';
            $name_column = 'dropoff_name';
            $status_column = 'dropoff_status';
        }

        // Fetch the point data
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($id_column, $reserve_point);
        $this->db->where($status_column, 'active');

        $query_points = $this->db->get();
        $point = $query_points->row();

        // If point is found, populate the row with its data
        if ($point) {
            $row = array(
                'point_id' => $point->$id_column,
                'point_lat' => $point->$lat_column,
                'point_lon' => $point->$lon_column,
                'point_name' => $point->$name_column
            );
        }
        return $row;
    }
}
