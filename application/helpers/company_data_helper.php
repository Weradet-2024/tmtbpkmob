<?php
function get_comopany_by_userid($user_id)
{
    // Get the CodeIgniter instance
    $CI =& get_instance();
    // Load the database library if not already loaded
    $CI->load->database();
    // Query the database
    $query = $CI->db->get_where('emp', array('emp_id' => $user_id));
    return $query->row();
}

function get_emp_data_by_id($ck_emp_id)
{
    $CI =& get_instance();
    // Load the database library if not already loaded
    $CI->load->database();
    // Query the database
    $query = $CI->db->get_where('emp', array('emp_id' => $ck_emp_id));
    return $query->result();
}
