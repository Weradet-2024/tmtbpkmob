<?php
/*
 * @author Weradet Nopsomboon
 * @Create Date 2568-02-21
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Fcm_token_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('company_data_helper');
        $this->load->helper('utility_helper');
    }
    public function get_fcm_token($emp_id)
    {
        $query = $this->db->get_where('fcm_token', ['fcm_emp_id' => $emp_id])->result();
        return $query;
    }

    public function get_all_fcm_token(){
        $query = $this->db->get('fcm_token')->result();
        return $query;
    }

    public function insert_fcm_token($data)
    {
        return $this->db->insert('fcm_token', $data);
    }

    public function update_fcm_token($data, $emp_id)
    {
        $this->db->where('fcm_emp_id', $emp_id);
        $this->db->update('fcm_token', $data);
        return true;
    }

    public function delete_fcm_token($emp_id)
    {
        $this->db->where('fcm_emp_id', $emp_id);
        $this->db->delete('fcm_token');
    }

    public function get_fcm_token_by_token($token)
    {
        $query = $this->db->get_where('fcm_token', ['fcm_token' => $token])->result();
        return $query;
    }

    public function get_fcm_token_by_json($json)
    {
        $arr_emp = json_decode($json, true);
        $arr_emp_new = []; // กำหนดค่าเริ่มต้น
    
        // ดึง employee ID ทั้งหมด
        $emp_ids = array_column($arr_emp, 'id');
    
        if (!empty($emp_ids)) {
            // Query หา fcm_token สำหรับ emp_id ทั้งหมดทีเดียว
            $query = $this->db->where_in('fcm_emp_id', $emp_ids)->get('fcm_token')->result_array();
    
            // จัดกลุ่ม fcm_token ตาม emp_id
            $fcm_tokens = [];
            foreach ($query as $row) {
                $fcm_tokens[$row['fcm_emp_id']][] = $row['fcm_token'];
            }
    
            // สร้าง array ใหม่เฉพาะ emp_id ที่มี fcm_token เท่านั้น
            foreach ($arr_emp as $value) {
                $emp_id = $value['id'];
    
                // ตรวจสอบว่ามี fcm_token หรือไม่
                if (isset($fcm_tokens[$emp_id])) {
                    $arr_emp_new[] = [
                        'emp_id' => $emp_id,
                        'date' => $value['date'],
                        'fcm_token' => $fcm_tokens[$emp_id]
                    ];
                }
            }
        }
    
        return $arr_emp_new;
    }
    

    public function get_fcm_token_by_boardcast_id($id){

        $this->db->select('emp.emp_id, fcm_token.fcm_emp_id, broadcast.broadcast_id, broadcast.broadcast_title, broadcast.broadcast_detail, fcm_token.fcm_token');
        $this->db->from('emp');
        $this->db->join('fcm_token', 'fcm_token.fcm_emp_id = emp.emp_id');
        $this->db->join('broadcast', 'broadcast.broadcast_plant_id = emp.emp_plant');
        $this->db->where('broadcast.broadcast_id', $id);
       // $this->db->where('emp_plant = broadcast.broadcast_plant_id' );
        $this->db->where('emp.emp_status', 'active');

        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return [];
        }

    }

    public function update_latest_noti_date($id) {
        $this->db->set('broadcast_lastest_noti', date('Y-m-d H:i:s'));
        $this->db->where('broadcast_id', $id);
        $this->db->update('broadcast');
        return true;
    }

    public function get_all_broadcast(){
        $this->db->select('*');
        $this->db->from('broadcast');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_broadcasts_to_notify()
        {

    $sql = "
        SELECT * FROM broadcast
        WHERE (
            (broadcast_type = 'once' 
                AND broadcast_start_date = ? 
                AND broadcast_end_date >= ?)

            OR (broadcast_type = 'once_per_day' 
                AND broadcast_daily_time = ?)

            OR (broadcast_type = 'weekly' 
                AND broadcast_weekly_day = ? 
                AND broadcast_daily_time = ?)
        )
    ";

    $query = $this->db->query($sql, [
        date('Y-m-d'),
        date('Y-m-d'),
        date('H:i:s'),
        date('l'),
        date('H:i:s')
    ]);

    return $query->result_array();
}


}
