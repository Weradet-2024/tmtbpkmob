<?php

function set_time_zone()
{
     date_default_timezone_set('Asia/Bangkok');
}

function get_date_today()
{
     return date("Y-m-d");
}

function get_time_now()
{
     return date("H:i");
}

function get_date_mouth()
{
     return date("Y-m");
}

//28 สิงหาคม 2567 
function to_format($old_date)
{
     $month_name = [
          "มกราคม",
          "กุมภาพันธ์",
          "มีนาคม",
          "เมษายน",
          "พฤษภาคม",
          "มิถุนายน",
          "กรกฎาคม",
          "สิงหาคม",
          "กันยายน",
          "ตุลาคม",
          "พฤศจิกายน",
          "ธันวาคม"
     ];
     //$a =  "2021 - 12 - 30";
     $year = substr($old_date, 0, strpos($old_date, '-'));
     $year_thai = intval($year) + 543;
     $month = substr($old_date, strpos($old_date, '-') + 1, 2);
     $day = substr($old_date, strpos($old_date, '-') + 4, 2);
     if (intval($month) - 1 < 0) {
          $format = "-";
     } else {
          $format = $day . " " . $month_name[intval($month) - 1] . " " . $year_thai;
     }
     return $format;
}

// 28 August 2024
function to_format_en($old_date)
{
     $week = date("w");
     $month_name = [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
     ];
     //$a =  "2021 - 12 - 30";
     $year = substr($old_date, 0, strpos($old_date, '-'));
     $year_thai = intval($year) + 543;
     $month = substr($old_date, strpos($old_date, '-') + 1, 2);
     $day = substr($old_date, strpos($old_date, '-') + 4, 2);
     if (intval($month) - 1 < 0) {
          $format = "-";
     } else {
          $format = $day . " " . $month_name[intval($month) - 1] . " " . $year_thai;
     }
     return $format;
}
//พุธ 28 สิงหาคม 2567 
function format_week_date_th_curr_date()
{
     $week = date("w");
     $months = date("m") - 1;
     $day = date("d");
     $years = date("Y") + 543;
     $ThDay = array("อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัส", "ศุกร์", "เสาร์");
     $ThMonth = array("มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
     return $ThDay[$week] . " " . $day . " " . $ThMonth[$months] . " " . $years;
}
//Wednesday 28 August 2024
function format_week_date_en_curr_date()
{
     $week = date("w");
     $months = date("m") - 1;
     $day = date("d");
     $years = date("Y") + 543;
     $EnDay = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
     $EnMonth = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
     return $EnDay[$week] . " " . $day . " " . $EnMonth[$months] . " " . $years;
}
/*
 * to_format_abbreviation
 * set format date โดยเดือนเป็นตัวย่อ
 * @input -
 * @output -
 * @author Suwapat Saowarod 62160340
 * @Create Date 2565-03-09
 * @Update Date -
 */
function to_format_abbreviation($old_date)
{
     $month_name = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.",];
     //$a =  "2021 - 12 - 30";
     $year = substr($old_date, 0, strpos($old_date, '-'));
     $year_thai = intval($year) + 543;
     $month = substr($old_date, strpos($old_date, '-') + 1, 2);
     $day = substr($old_date, strpos($old_date, '-') + 4, 2);
     if (intval($month) - 1 < 0) {
          $format = "-";
     } else {
          $format = $day . " " . $month_name[intval($month) - 1] . " " . $year_thai;
     }
     return $format;
}

function date_cover($date)
{
     $date = explode(" ", $date);
     list($y, $m, $d) = explode("-", $date[0]);
     $date1 = ($y) . "-" . $m . "-" . $d;
     return $date1;
}