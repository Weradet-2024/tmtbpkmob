<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('Asia/Bangkok');

require_once APPPATH . "/third_party/PHPMailer/src/PHPMailer.php";
require_once APPPATH . "/third_party/PHPMailer/src/SMTP.php";
require_once APPPATH . "/third_party/PHPMailer/src/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email_lib
{

    public $CI;
    public $mail;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.office365.com'; // เปลี่ยนเป็น SMTP ของคุณ
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'dxteamservice.ittttp@outlook.com'; // เปลี่ยนเป็นชื่อผู้ใช้ SMTP ของคุณ
        $this->mail->Password = 'Tttp@12345#'; // เปลี่ยนเป็นรหัสผ่าน SMTP ของคุณ
        // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // เปลี่ยนเป็น 'ssl' หรือ 'tls' ตามการตั้งค่าของ SMTP ของคุณ
        $this->mail->Port = 587; // เปลี่ยนเป็นพอร์ต SMTP ของคุณ
        $this->mail->CharSet = "UTF-8";

    }

    public function SendEmailAlertComment($Subject, $Body, $From_mail){

        $outlook_email = "dxteamservice.ittttp@outlook.com";
        $outlook_name = "DX Service ITTTTP";

        $this->mail->isHTML(true);

        $this->mail->setFrom($outlook_email, $outlook_name);
        $this->mail->addAddress($From_mail);

        $this->mail->Subject = $Subject;
        $this->mail->Body = $Body;

        if ($this->mail->send()) {
            return true;
        } else {
            return false;
        }

    }


}
