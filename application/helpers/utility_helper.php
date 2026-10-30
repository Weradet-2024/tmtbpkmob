<?php

function get_cut_of_time_data($curr_date, $com_id)
{
    // Get the CodeIgniter instance
    $CI =& get_instance();
    // Load the database library if not already loaded
    $CI->load->database();

    // Query the database
    $query = $CI->db->get_where('close_time', array('close_time_date' => $curr_date, 'close_time_plant_id' => $com_id, 'close_time_status' => 'active'))->row_array();

    return !empty($query) ? $query : null;
}

function get_feature_set_item($plant_id){

    // Get the CodeIgniter instance
    $CI =& get_instance();
    // Load the database library if not already loaded
    $CI->load->database();

    $query = $CI->db->get_where('feature_set_item', ['feature_set_item_plant_id' => $plant_id])->row_array();

    return !empty($query) ? $query : null;

}

function  get_cut_of_time_reserve_data($reserve_trip, $reserve_time){
      // Get the CodeIgniter instance
      $CI =& get_instance();
      // Load the database library if not already loaded
      $CI->load->database();
  
      $query = $CI->db->get_where('close_time_reserve', ['close_time_reserve_trip' => $reserve_trip, 'close_time_reserve_time' => 
      $reserve_time, 'close_time_reserve_status' => 'active'])->row_array();
  
      return !empty($query) ? $query : null;
}

function map_array_str($input)
{
    // Convert the string to an array of strings
    $string_array = explode(', ', $input);
    // Convert each string element to an integer
    $int_array = array_map('intval', $string_array);
    return $int_array;
}

function get_calendar_type($com_id, $date)
{
    $CI =& get_instance();
    // Load the database library if not already loaded
    $CI->load->database();

    // Get calendar type
    $CI->db->select('calendar_type');
    $CI->db->from('calendar');
    $CI->db->where('calendar_com_id', $com_id);
    $CI->db->where('calendar_date', $date);

    $result = $CI->db->get()->row_array();

    // Check if result is null
    return !empty($result) ? $result : null;
}

function get_calendar_next_weekday($date)
{
    $CI =& get_instance();
    // Load the database library if not already loaded
    $CI->load->database();

    // Get the next weekday
    $CI->db->limit(1);
    $CI->db->select('*');
    $CI->db->from('calendar');
    $CI->db->where('calendar_date > ', $date);
    $CI->db->where('calendar_type', 'weekday');

    $result = $CI->db->get()->row_array();

    // Check if result is null
    return !empty($result) ? $result : null;
}

function get_trip_out_night_shift()
{
    $CI =& get_instance();
    // Load the database library if not already loaded
    $CI->load->database();

    $CI->db->distinct();
    $CI->db->select('shift_end');
    $CI->db->from('shift');
    $CI->db->where('shift_type', 'night');

    $query = $CI->db->get()->result_array();

    return !empty($query) ? $query : null;

}