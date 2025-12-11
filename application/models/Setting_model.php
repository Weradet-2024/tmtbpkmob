<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-26
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Setting_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
    }
    public function get_data($ck_emp_id)
    {
        $this->db->select('*');
        $this->db->from('emp');
        $this->db->join('company', 'emp.emp_com_id = company.company_id', 'left');
        $this->db->join('plant', 'emp.emp_plant = plant.plant_id', 'left');
        $this->db->where('emp_id = ', $ck_emp_id);
        $query_emp = $this->db->get();
        $arr_result = array();
        foreach ($query_emp->result() as $rs) {
            $emp_id = $rs->emp_id;
            $emp_plant = $rs->emp_plant;
            $emp_code = $rs->emp_code;
            $emp_com_id = $rs->emp_com_id;
            $emp_name = $rs->emp_name;
            $emp_lname = $rs->emp_lname;
            $emp_dept = $rs->emp_dept;
            $emp_section = $rs->emp_section;
            $emp_tel = substr($rs->emp_tel, 0, 6);
            $emp_route_zone_id = $rs->emp_route_zone_id;
            $emp_route_id = $rs->emp_route_id;
            $emp_pick_id = $rs->emp_pick_id;
            $emp_route_out_id = $rs->emp_route_out_id;
            $emp_route_zone_out_id = $rs->emp_route_zone_out_id;
            $emp_route_out_id = $rs->emp_route_out_id;
            $emp_drop_id = $rs->emp_drop_id;
            $company_name = $rs->company_name;
            $plant_name_th = $rs->plant_name_th;
            $plant_name_en = $rs->plant_name;

            $row = array();
            //get dept data
            $query_dept = $this->db->get_where('dept', array(
                'dept_com_id' => $emp_com_id,
                'dept_id' => $emp_dept
            ));
            //get route in data
            $this->db->select('route_code, route_name');
            $this->db->from('route');
            $this->db->where('route_com_id', $emp_com_id);
            $this->db->where('route_id', $emp_route_id);
            $this->db->where('route_vendor_id !=', 0);
            $this->db->group_start();
            $this->db->where('route_plant', $emp_plant);
            $this->db->or_where("',' + route_plant_member + ',' LIKE '%,$emp_plant,%'");
            $this->db->group_end();
            $this->db->order_by('route_code', 'ASC');
            $query_route = $this->db->get();
			
            //get pick data
            $this->db->select('*');
            $this->db->from('pickup');
            $this->db->where('pickup_com_id = ', $emp_com_id);
            // $this->db->where('pickup_route_zone_id = ', $emp_route_zone_id);
            $this->db->where('pickup_id = ', $emp_pick_id);
            $this->db->order_by('pickup_item', 'ASC');
            $query_pick = $this->db->get();

            //get route out data
            $this->db->select('route_out_code as route_code, route_out_name as route_name');
            $this->db->from('route_out');
            // $this->db->where('route_out_vendor_id !=', 0);
            $this->db->where('route_out_com_id', $emp_com_id);
            $this->db->where('route_out_id', $emp_route_out_id);
            $this->db->group_start();
            $this->db->where('route_out_plant', $emp_plant);
            // ไม่เอา route_out OT
            $this->db->where('route_out_name NOT LIKE', '%OT%');
            $this->db->or_where("',' + route_out_plant_member + ',' LIKE '%,$emp_plant,%'");
            $this->db->group_end();
            $this->db->order_by('route_out_code', 'ASC');

            $query_route_out = $this->db->get();


            //get drop off data
            $this->db->select('*');
            $this->db->from('dropoff');
            $this->db->where('dropoff_com_id = ', $emp_com_id);
            // $this->db->where('dropoff_route_zone_out_id = ', $emp_route_zone_out_id);
            $this->db->where('dropoff_id = ', $emp_drop_id);
            $this->db->order_by('dropoff_item', 'ASC');
            $query_drop = $this->db->get();
            //query modal route in data
            $this->db->select('*');
            $this->db->from('route');
            $this->db->where('route_com_id = ', $emp_com_id);
            $this->db->where('route_trip   = ', "in");
            $this->db->where('route_status = ', "active");
            $this->db->where('route_vendor_id !=', 0);
            $this->db->group_start();
            $this->db->where('route_plant', $emp_plant);
            $this->db->or_where("',' + route_plant_member + ',' LIKE '%,$emp_plant,%'");
            $this->db->group_end();
            $this->db->order_by('route_code', 'ASC');
            $query_modal_route = $this->db->get();

            //get route out data
            $this->db->select('*');
            $this->db->from('pickup');
            $this->db->where('pickup_com_id = ', $emp_com_id);
            $this->db->where('pickup_route_id   = ', $emp_route_id);
            $this->db->order_by('pickup_item', 'ASC');
            $query_modal_pick = $this->db->get();
            //query modal route in data

            $this->db->select('*');
            $this->db->from('route_out');
            $this->db->where('route_out_vendor_id !=', 0);
            $this->db->where('route_out_com_id = ', $emp_com_id);
            $this->db->where('route_out_trip   = ', "out");
            $this->db->where('route_out_status = ', "active");
            $this->db->group_start();
            $this->db->where('route_out_plant', $emp_plant);
			
            $this->db->or_where("',' + route_out_plant_member + ',' LIKE '%,$emp_plant,%'");
            $this->db->group_end();
            $this->db->order_by('route_out_code', 'ASC');
            $query_modal_route_out = $this->db->get();
            //get route out data
            $this->db->select('*');
            $this->db->from('dropoff');
            $this->db->where('dropoff_com_id = ', $emp_com_id);
            $this->db->where('dropoff_route_id   = ', $emp_route_out_id);
            $this->db->order_by('dropoff_item', 'ASC');
            $query_modal_drop = $this->db->get();
            $row = array();
            $row['company_name'] = $company_name;
            $row['plant_name_th'] = $plant_name_th;
            $row['plant_name_en'] = $plant_name_en;
            $row['emp_id'] = $emp_id;
            $row['emp_code'] = $emp_code;
            $row['emp_name'] = $emp_name . " " . $emp_lname;
            $row['emp_tel'] = $emp_tel;
            $row['emp_route_zone_id'] = $emp_route_zone_id;
            $row['emp_route_id'] = $emp_route_id;
            $row['emp_pick_id'] = $emp_pick_id;
            $row['emp_route_zone_out_id'] = $emp_route_zone_out_id;
            $row['emp_route_out_id'] = $emp_route_out_id;
            $row['emp_drop_id'] = $emp_drop_id;
            $row['arr_dept'] = $query_dept->result();
            $row['arr_route_in'] = $query_route->result();
            $row['arr_pickup'] = $query_pick->result();
            $row['arr_route_out'] = $query_route_out->result();
            $row['arr_drop'] = $query_drop->result();
            $row['arr_modal_route'] = $query_modal_route->result();
            $row['arr_modal_pick'] = $query_modal_pick->result();
            $row['arr_modal_route_out'] = $query_modal_route_out->result();
            $row['arr_modal_drop'] = $query_modal_drop->result();
            $arr_result[] = $row;
        } //foreeach
        return $arr_result;
    }

    function get_route_in_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
        $ck_emp_plant_id = $arr_emp_com_id->emp_plant;
        $this->db->select('*');
        $this->db->from('route');
        $this->db->where('route_com_id = ', $ck_emp_com_id);
        $this->db->where('route_status = ', "active");
        $this->db->group_start();
        //$this->db->where('route_plant', $ck_emp_plant_id);
        $this->db->or_where("',' + route_plant_member + ',' LIKE '%,$ck_emp_plant_id,%'");
        $this->db->group_end();
        $this->db->order_by('route_code', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    function get_route_out_data()
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $arr_emp_com_id = get_comopany_by_userid($ck_emp_id);
        $ck_emp_com_id = $arr_emp_com_id->emp_com_id;
        $ck_emp_plant_id = $arr_emp_com_id->emp_plant;
        $this->db->select('*');
        $this->db->from('route_out');
        $this->db->where('route_out_com_id = ', $ck_emp_com_id);
        $this->db->where('route_out_status = ', "active");

		//ไม่เอา route_zone_out, route_out (OT)
		$this->db->where('route_out_name NOT LIKE', '%OT%');

        $this->db->group_start();
        //$this->db->where('route_out_plant', $ck_emp_plant_id);
        // $this->db->or_where("',' + route_zone_out_plant_member + ',' LIKE '%,$ck_emp_plant_id,%'");
		$this->db->or_where("',' + route_out_plant_member + ',' LIKE '%,$ck_emp_plant_id,%'");
        $this->db->group_end();
        $this->db->order_by('route_out_code', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_ed_name($ck_emp_id)
    {
        //get emp-data
        $this->db->select('*');
        $this->db->from('emp');
        $this->db->join('company', 'emp.emp_com_id = company.company_id', 'left');
        $this->db->where('emp_id = ', $ck_emp_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function ud_name($modal_ed_emp_name, $modal_ed_emp_lname, $ck_emp_com_id, $modal_ed_emp_id)
    {
        $data = array(
            'emp_name' => $modal_ed_emp_name,
            'emp_lname' => $modal_ed_emp_lname,
        );
        $this->db->where('emp_com_id', $ck_emp_com_id);
        $this->db->where('emp_id', $modal_ed_emp_id);
        $status = $this->db->update('emp', $data);
        return $status;
    }

    public function get_ed_tel($ck_emp_id)
    {
        //get emp_tel data
        $this->db->select('emp_id, emp_tel');
        $this->db->from('emp');
        $this->db->join('company', 'emp.emp_com_id = company.company_id', 'left');
        $this->db->where('emp_id = ', $ck_emp_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function update_tel($emp_id, $emp_tel)
    {
        $data = array(
            'emp_tel' => $emp_tel
        );
        $this->db->where('emp_id', $emp_id);
        $status = $this->db->update('emp', $data);
        return $status;
    }

    public function ed_pw($ck_emp_id)
    {
        //get emp_tel data
        $this->db->select('emp_id');
        $this->db->from('emp');
        $this->db->join('company', 'emp.emp_com_id = company.company_id', 'left');
        $this->db->where('emp_id = ', $ck_emp_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function update_pw($emp_id, $emp_pname)
    {
        $data = array(
            'emp_pname' => $emp_pname
        );
        $this->db->where('emp_id', $emp_id);
        $status = $this->db->update('emp', $data);
        return $status;
    }

    public function get_close_time($ck_emp_com_id, $curr_date_name, $ck_emp_plant_id)
    {
        $this->db->select('*');
        $this->db->from('close_time');
        $this->db->where('close_time_com_id', $ck_emp_com_id);
        $this->db->where('close_time_plant_id', $ck_emp_plant_id);
        $this->db->where('close_time_date', $curr_date_name);
        $this->db->where('close_time_status', 'active');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_calendar($curr_date, $com_id, $plant_id)
    {

        $this->db->select('calendar_type');
        $this->db->from('calendar');
        $this->db->where('calendar_com_id =', $com_id);
        $this->db->where('calendar_plant_id =', $plant_id);
        $this->db->where('calendar_date', $curr_date);
        $query = $this->db->get();
        return $query->result();
    }

    public function modal_route_chk_route($ck_emp_com_id, $route_id)
    {
        $this->db->select('*');
        $this->db->from('pickup');
        $this->db->where('pickup_com_id =', $ck_emp_com_id);
        $this->db->where('pickup_route_id = ', $route_id);
        $this->db->order_by('pickup_code', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function modal_route_out_chk($ck_emp_com_id, $route_out_id)
    {
        $this->db->select('*');
        $this->db->from('dropoff');
        $this->db->where('dropoff_com_id', $ck_emp_com_id);
        $this->db->where('dropoff_route_id', $route_out_id);
        $this->db->order_by('dropoff_item', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function update_route($ck_emp_id, $ck_emp_com_id, $modal_route_emp_id, $route_zone_id, $pickup_id)
    {

        $curr_date = date("Y-m-d");
        $curr_time = date("H:i:s");
        //get emp-data
        $this->db->select('*');
        $this->db->from('emp');
        $this->db->join('route', 'route.route_id = emp.emp_route_id', 'left');
        $this->db->join('pickup', 'pickup.pickup_id = emp.emp_pick_id', 'left');
        $this->db->where('emp_id = ', $ck_emp_id);
        $query_emp = $this->db->get();
        foreach ($query_emp->result() as $rsEmp) {
            $emp_code = $rsEmp->emp_code;
            $emp_name = $rsEmp->emp_name;
            $emp_lname = $rsEmp->emp_lname;
            $emp_dept = $rsEmp->emp_dept;
            $route_code = $rsEmp->route_code;
            $route_name = $rsEmp->route_name;
            $point_id = $rsEmp->emp_pick_id;
            $point_code = $rsEmp->pickup_code;
            $point_name = $rsEmp->pickup_name;

            $emp_route_zone_id = $rsEmp->emp_route_zone_id;
        } //foreach

        if ($emp_route_zone_id != $route_zone_id || $point_id != $pickup_id) {

            //get route_id from pickup
            $arr_route_id = $this->db->get_where('pickup', ['pickup_id' => $pickup_id])->row();

            if (!$arr_route_id || !isset($arr_route_id->pickup_route_id)) {
                return "no_update";
            }

            $route_id = $arr_route_id->pickup_route_id;

            $arr_route_zone_id = $this->db->get_where('route_zone', ['route_zone_id' => $route_zone_id])->row();

            if (!$arr_route_zone_id || !isset($arr_route_zone_id->route_zone_zone_id)) {
                return "no_update";
            }

            $route_zone_zone_id = $arr_route_zone_id->route_zone_zone_id;

            $data = array(
                'emp_zone_id' => $route_zone_zone_id,
                'emp_route_id' => $route_id,
                'emp_route_zone_id' => $route_zone_id,
                'emp_pick_id' => $pickup_id
            );
            $this->db->where('emp_id', $modal_route_emp_id);
            $status = $this->db->update('emp', $data);
            if ($status == 1) {
                $data = array(
                    'from_route_id' => $route_id,
                    'to_route_id' => $route_zone_id,
                    'from_point_id' => $point_id,
                    'to_point_id' => $pickup_id,
                    'trip' => 'in',
                    'emp_id' => $ck_emp_id,
                    'ud_reg_date' => $curr_date,
                    'ud_reg_time' => $curr_time
                );
                $status = $this->db->insert('up_route', $data);
                //update data reserve
                // $data_reserve = array(
                //     'reserve_route'   =>  $route_id,
                //     'reserve_point'   =>  $pickup_id,
                //     'reserve_ed_date' =>  $curr_date,
                //     'reserve_ed_time' =>  $curr_time,
                // );
                // $this->db->where('reserve_mem_id = ', $ck_emp_id);
                // $this->db->where('reserve_trip = ', 'in');
                // $this->db->where('reserve_date > ', $curr_date);
                // $status = $this->db->update('reserve', $data_reserve);
                return $status;
            }
        } else {
            return "no update";
        }
    }

    public function update_route_out($ck_emp_id, $ck_emp_com_id, $modal_route_out_emp_id, $route_zone_out_id, $dropoff_id)
    {
        $curr_date = date("Y-m-d");
        $curr_time = date("H:i:s");
        //get emp-data
        $this->db->select('*');
        $this->db->from('emp');
        $this->db->join('route', 'route.route_id = emp.emp_route_out_id', 'left');
        $this->db->join('dropoff', 'dropoff.dropoff_id = emp.emp_drop_id', 'left');
        $this->db->where('emp_id', $ck_emp_id);
        $query_emp = $this->db->get();
        foreach ($query_emp->result() as $rsEmp) {
            $emp_code = $rsEmp->emp_code;
            $emp_name = $rsEmp->emp_name;
            $emp_lname = $rsEmp->emp_lname;
            $emp_dept = $rsEmp->emp_dept;
            $route_code = $rsEmp->route_code;
            $route_name = $rsEmp->route_name;
            $point_id = $rsEmp->emp_drop_id;
            $point_code = $rsEmp->dropoff_code;
            $point_name = $rsEmp->dropoff_name;
            $emp_route_out_id = $rsEmp->emp_route_out_id;
            $emp_route_zone_out_id = $rsEmp->emp_route_zone_out_id;
        }
        if ($emp_route_zone_out_id != $route_zone_out_id || $point_id != $dropoff_id) {

            $arr_route_zone_id = $this->db->get_where('route_zone_out', ['route_zone_out_id' => $route_zone_out_id])->row();

            if (!$arr_route_zone_id || !isset($arr_route_zone_id->route_zone_out_zone_id)) {
                return "no_update";
            }

            $route_zone_out_zone_id = $arr_route_zone_id->route_zone_out_zone_id;

            //get route_id from pickup
            $arr_route_id = $this->db->get_where('dropoff', ['dropoff_id' => $dropoff_id])->row();

            if (!$arr_route_id || !isset($arr_route_id->dropoff_route_id)) {
                return "no_update";
            }

            $route_id = $arr_route_id->dropoff_route_id;

            $data = array(
                'emp_zone_id' => $route_zone_out_zone_id,
                'emp_route_out_id' => $route_id,
                'emp_route_zone_out_id' => $route_zone_out_id,
                'emp_drop_id' => $dropoff_id
            );


            $this->db->where('emp_id', $modal_route_out_emp_id);
            $status = $this->db->update('emp', $data);
            $data = array(
                'from_route_id' => $emp_route_out_id,
                'to_route_id' => $emp_route_out_id,
                'from_point_id' => $point_id,
                'to_point_id' => $dropoff_id,
                'trip' => 'out',
                'emp_id' => $ck_emp_id,
                'ud_reg_date' => $curr_date,
                'ud_reg_time' => $curr_time
            );
            $this->db->insert('up_route', $data);
            if ($status == 1) {
                // //update data reserve
                // $data_reserve = array(
                //     'reserve_route'   =>  $route_out_id,
                //     'reserve_point'   =>  $dropoff_id,
                //     'reserve_ed_date' =>  $curr_date,
                //     'reserve_ed_time' =>  $curr_time,
                // );
                // $this->db->where('reserve_mem_id = ', $ck_emp_id);
                // $this->db->where('reserve_trip = ', 'in');
                // $this->db->where('reserve_date > ', $curr_date);
                // $status = $this->db->update('reserve', $data_reserve);
                return $status;
            }
        } else {
            return "no update";
        }

    }

    /////////// Natthanicha P.  08022025
    public function chk_feature_item_change_password($com_id, $plant_id)
    {
        $this->db->select('feature_set_item_change_password');
        $this->db->where('feature_set_item_change_password', 'active');
        $this->db->where('feature_set_item_com_id', $com_id);
        $this->db->where('feature_set_item_plant_id', $plant_id);
        $query = $this->db->get('feature_set_item');
        return json_encode($query->result(), JSON_UNESCAPED_UNICODE);
    }

    public function chk_feature_item_allow_passenger_change_route($com_id, $plant_id)
    {
        $this->db->select('feature_set_item_allow_passenger_change_route');
        $this->db->where('feature_set_item_allow_passenger_change_route', 'active');
        $this->db->where('feature_set_item_com_id', $com_id);
        $this->db->where('feature_set_item_plant_id', $plant_id);
        $query = $this->db->get('feature_set_item');
        return json_encode($query->result(), JSON_UNESCAPED_UNICODE);
    }

}
