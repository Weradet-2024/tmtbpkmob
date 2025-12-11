<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('sessionlang')) {
    function sessionlang($key)
    {
        $CI = &get_instance();
        $language = $CI->session->userdata('language');
        $CI->lang->load('language_lib', $language);
        return $CI->lang->line($key);
    }
}
