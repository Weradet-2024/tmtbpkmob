<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['Api/boradcast_reservation']  = 'Push/boradcast_reservation';
$route['Api/boradcast'] = 'Push/boradcast';

$route['default_controller'] = 'Main';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
