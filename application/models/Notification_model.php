<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2568-05-31
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Notification_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_notification_data($emp_id)
    {
    // ดึง emp_plant_id ของพนักงาน
    $this->db->select('emp.emp_plant');
    $this->db->from('emp');
    $this->db->where('emp.emp_id', $emp_id);
    $emp_data = $this->db->get()->row();

    if (!$emp_data) {
        return [];
    }

    $emp_plant_id = $emp_data->emp_plant;

    $this->db->select('n.broadcast_id, n.broadcast_title, n.broadcast_detail, n.broadcast_pic_path, n.broadcast_file_path, n.broadcast_start_date, n.broadcast_end_date, n.broadcast_plant_id, n.broadcast_is_reminder, n.broadcast_lastest_noti');
    $this->db->from('broadcast n');
    $this->db->where('n.broadcast_plant_id', $emp_plant_id);
    $this->db->where('n.broadcast_lastest_noti IS NOT NULL', null, false);

    // เงื่อนไข: ไม่แสดง broadcast_type = 'once' ที่หมดอายุ
    $this->db->group_start();
    $this->db->where('n.broadcast_type !=', 'once');
    $this->db->or_group_start();
    $this->db->where('n.broadcast_type', 'once');
    $this->db->where('n.broadcast_end_date >=', date('Y-m-d'));
    $this->db->group_end();
    $this->db->group_end();

    $this->db->order_by('n.broadcast_is_reminder', 'DESC');
    $this->db->order_by('n.broadcast_lastest_noti', 'DESC');

    $query = $this->db->get();
    return $query->result_array();
    }

    public function get_notification($id)
    {
        $query = $this->db->get_where('broadcast', ['broadcast_id' => $id]);
        return $query->row_array(); // ส่งกลับเป็น array
    }


}

