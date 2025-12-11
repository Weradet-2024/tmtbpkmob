<?php
/*
 * Main
 * Main Controller  
 * @author Weradet Nopsomboon
 * @Create Date 2567-08-08
 */

defined('BASEPATH') or exit('No direct script access allowed');
include_once dirname(__FILE__) . '../Authenticated_controller.php';
class Main extends Authenticated_controller
{
    protected $auth;

    public function __construct()
    {   

        parent::__construct();

        date_default_timezone_set('Asia/Bangkok');

        $this->load->helper('cookie');

        $this->load->helper('url');
    }

    public function index($token = null)
    {
        $cookie_expiration = 300; // 5 minutes in seconds
        $token = isset($token) ? $token : null;

        if (!empty($token)) {
            set_cookie([
                'name' => 'fcm_token',
                'value' => $token,
                'expire' => $cookie_expiration,
                'secure' => TRUE
            ]);
        }

        try {
            if ($this->session->has_userdata("emp_id")) {
                if (!empty($token)) {
                    redirect("Reserve_history");
                } else {
                    redirect('Home');
                }
            } else {
                redirect('Login');
            }
        } catch (Exception $e) {
            delete_cookie('fcm_token');
            redirect('Login');
        }
    }

    public function gotoNotification($token = null)
    {
        $cookie_expiration = 300; // 5 minutes in seconds
        $token = isset($token) ? $token : null;

        if (!empty($token)) {
            set_cookie([
                'name' => 'fcm_token',
                'value' => $token,
                'expire' => $cookie_expiration,
                'secure' => TRUE
            ]);
        }

        try {
            if ($this->session->has_userdata("emp_id")) {
                if (!empty($token)) {
                    redirect("Notification");
                } else {
                    redirect('Home');
                }
            } else {
                redirect('Login');
            }
        } catch (Exception $e) {
            delete_cookie('fcm_token');
            redirect('Login');
        }
    }
    public function gotoReserve($token = null)
    {
        $cookie_expiration = 300; // 5 minutes in seconds
        $token = isset($token) ? $token : null;

        if (!empty($token)) {
            set_cookie([
                'name' => 'fcm_token',
                'value' => $token,
                'expire' => $cookie_expiration,
                'secure' => TRUE
            ]);
        }

        try {
            if ($this->session->has_userdata("emp_id")) {
                if (!empty($token)) {
                    redirect("Reserve_ad");
                } else {
                    redirect('Home');
                }
            } else {
                redirect('Login');
            }
        } catch (Exception $e) {
            delete_cookie('fcm_token');
            redirect('Login');
        }
    }

}
