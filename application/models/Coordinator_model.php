<?php
/*
* @author Weradet Nopsomboon
* @Create Date 2567-08-24
*/

defined('BASEPATH') or exit('No direct script access allowed');

class Coordinator_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
    }
    public function get_coordinator_data($emp_plant)
    {
        $ck_emp_id = $this->session->userdata('emp_id');
        $emp_plant = '';
        $row_emp = get_emp_data_by_id($ck_emp_id);
        if(count($row_emp) <= 0){
            return "emp_ng";
        }
        foreach($row_emp as $rs_emp){
            $emp_plant = $rs_emp->emp_plant;
        }
        //get data coordinar
        // $query  =$this->db->get_where('coordinator', ['coordinator_plant_id'=> $emp_plant, 'coordinator_status' => 'active'])->result();
		$query  =$this->db->get_where('coordinator', ['coordinator_status' => 'active'])->result();
        return $query;
    }
}
