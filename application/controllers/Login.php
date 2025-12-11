<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('cookie');
        $this->load->model('Login_model');
        $this->load->library('email');
    }
    public function index()
    {

        if ($this->session->has_userdata("emp_id")) {
            redirect('Home');
        } else {
            $this->load->view('login/login_view');
        }

    }

    public function get_login()
    {
        $emp_email = $this->input->post("emp_email");
        $mem_pname = $this->input->post("emp_pname");
        $remember_me = $this->input->post("remember");
        $result = $this->Login_model->get_login_data($emp_email, $mem_pname, $remember_me);
        if ($result != "ng") {
            // ตรวจสอบเซสชันที่ใช้งานอยู่
            $active_session = $this->Login_model->get_active_session($result->emp_id);

            // สร้างเซสชันใหม่
            $session_id = session_id();
            $this->Login_model->create_session($result->emp_id, $session_id);
            $this->Login_model->update_login_status($result->emp_id, true); // อัปเดตสถานะการล็อกอินในฐานข้อมูล
            $this->session->set_userdata('emp_id', $result->emp_id);
            $this->session->set_userdata('emp_com_id', $result->emp_com_id);
            $this->session->set_userdata('emp_plant', $result->emp_plant);
            $this->session->set_userdata('emp_lang', $result->emp_lang);

            // จัดการ Remember Me
            if ($remember_me) {
                if ($remember_me) {
                    $token = bin2hex(random_bytes(16)); // สร้าง token แบบสุ่ม
                    $this->Login_model->update_remember_token($result->emp_id, $token);
                    set_cookie('remember_me', $token, 60 * 60 * 24 * 30); // คุกกี้หมดอายุใน 30 วัน
                }
            }
            $cookie_expiration = 60 * 60 * 24 * 30;
            $this->input->set_cookie('ck_emp_lang', $result->emp_lang, $cookie_expiration);
            // ตรวจสอบว่ามี token สำหรับ FCM หรือไม่
            // With:
            try {
                if ($this->input->cookie('fcm_token')) {
                    echo "ok_reserve";
                } else {
                    echo "ok";
                }
            } catch (Exception $e) {
                log_message('error', 'Error checking FCM session: ' . $e->getMessage());
                echo "error";
            }

        } else {
            echo "ng";
        }
    }
    public function logout()
    {
        try {
            // Get user ID before destroying session
            $emp_id = $this->session->userdata('emp_id');

            // Delete all cookies
            $cookies = $this->input->cookie();
            if (!empty($cookies)) {
                foreach ($cookies as $cookie_name => $cookie_value) {
                    $this->input->set_cookie([
                        'name' => $cookie_name,
                        'value' => '',
                        'expire' => -3600,
                        'secure' => TRUE
                    ]);
                }
            }

            if ($emp_id) {
                // Update login status and delete session in database
                $this->Login_model->update_login_status($emp_id, false);
                $this->Login_model->delete_session($emp_id);
            }

            // Destroy session
            $this->session->sess_destroy();

            redirect('Login');
        } catch (Exception $e) {
            redirect('Login');
        }
    }

    public function forgotPass()
    {

        $this->load->view('login/forgot_pass_view');

    }

    public function check_email_user()
    {
        $email = $this->input->post('user_email');


        $result = $this->Login_model->check_email($email);


        if ($result) {
            echo 1; //ture

            $this->send_mail_reset($email, $result->emp_id);
        } else {
            echo 2; //false
        }
    }


    //added date 2025-06-17
    // check user employee email
    //author: Weradet Nopsomboon

    public function send_mail_reset($email, $emp_id)
    {

        $token = rand(1000, 9999);

        $this->Login_model->update_pass_token($token, $emp_id);

        // Email Configuration
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_user' => 'tttp.backups@gmail.com',
            'smtp_pass' => 'dwsrzqmxylrhtvyt',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n",
            'smtp_crypto' => 'tls',
            'wordwrap' => TRUE
        );

        // $subjectMail = "รีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ - Togeta";
		 $subjectMail = "TOGETAMOVE : ตั้งค่ารหัสผ่านใหม่ / Reset Password";

        $reset_link = base_url("index.php/Login/reset_password?token=" . urlencode($token)); // สมมติว่ามี $token ที่สร้างไว้

		   $body = '<div class="row">
				<table width="125%" bgcolor="#f5f5f5" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif;">
					<tr>
						<td align="center">
							<table width="600" cellpadding="20" cellspacing="0" bgcolor="#ffffff" style="margin-top: 40px; border-radius: 10px;">
								<tr>
									<td align="center">
										<h2 style="color: #333;">Password Reset Request</h2>
										<p style="color: #555; font-size: 16px;">
											You\'ve requested a password reset. Please click link below to proceed.
										</p>
										<a href="' . $reset_link . '" style="display: inline-block; padding: 12px 24px; margin: 20px 0; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px;">
											Reset Password
										</a>
										<p style="color: #777; font-size: 14px;">
											If you did not make this request, please ignore this email.
										</p>
										<p style="color: #aaa; font-size: 12px; margin-top: 30px;">
											© ' . date("Y") . ' Togeta. All rights reserved.
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				
		
				<table width="125%" bgcolor="#f5f5f5" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif;">
					<tr>
						<td align="center">
						<table width="600" cellpadding="20" cellspacing="0" bgcolor="#ffffff" style="margin-top: 40px; border-radius: 10px;">
							<tr>
							<td align="center">
								<h2 style="color: #333;">คำขอเปลี่ยนรหัสผ่าน</h2>
								<p style="color: #555; font-size: 16px;">
								เราได้รับคำขอให้เปลี่ยนรหัสผ่านของคุณ หากคุณเป็นผู้ดำเนินการ โปรดคลิกที่ปุ่มด้านล่างเพื่อดำเนินการต่อ
								</p>
								<a href="' . $reset_link . '" style="display: inline-block; padding: 12px 24px; margin: 20px 0; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px;">
								รีเซ็ตรหัสผ่าน
								</a>
								<p style="color: #777; font-size: 14px;">
								หากคุณไม่ได้เป็นผู้ร้องขอ กรุณาเพิกเฉยอีเมลฉบับนี้
								</p>
								<p style="color: #aaa; font-size: 12px; margin-top: 30px;">
								© ' . date("Y") . ' Togeta. All rights reserved.
								</p>
							</td>
							</tr>
						</table>
						</td>
					</tr>
			</table></div>';


//         $body = '
//         <table width="100%" bgcolor="#f5f5f5" cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif;">
//         <tr>
//             <td align="center">
//             <table width="600" cellpadding="20" cellspacing="0" bgcolor="#ffffff" style="margin-top: 40px; border-radius: 10px;">
//                 <tr>
//                 <td align="center">
//                     <h2 style="color: #333;">คำขอเปลี่ยนรหัสผ่าน</h2>
//                     <p style="color: #555; font-size: 16px;">
//                     เราได้รับคำขอให้เปลี่ยนรหัสผ่านของคุณ หากคุณเป็นผู้ดำเนินการ โปรดคลิกที่ปุ่มด้านล่างเพื่อดำเนินการต่อ
//                     </p>
//                     <a href="' . $reset_link . '" style="display: inline-block; padding: 12px 24px; margin: 20px 0; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 16px;">
//                     รีเซ็ตรหัสผ่าน
//                     </a>
//                     <p style="color: #777; font-size: 14px;">
//                     หากคุณไม่ได้เป็นผู้ร้องขอ กรุณาเพิกเฉยอีเมลฉบับนี้
//                     </p>
//                     <p style="color: #aaa; font-size: 12px; margin-top: 30px;">
//                     © ' . date("Y") . ' Togeta. All rights reserved.
//                     </p>
//                 </td>
//                 </tr>
//             </table>
//             </td>
//         </tr>
// </table>';


        $this->email->initialize($config);

        $this->email->from('tttp.backups@gmail.com', 'Togeta Administrator');
        $this->email->to($email);
        $this->email->subject($subjectMail);
        $this->email->message($body);


        if ($this->email->send()) {
            return true;
        } else {
            return $this->email->print_debugger();
        }

    }


    /* reset_password_page
     * check email in database
     * @input -
     * @output -
     * @author Weradet Nopsombun 62160110 
     * @Create Date 2564-08-12
     * @Update -
     */
    public function reset_password()
    {
        $data['token'] = $this->input->get('token');
        $this->load->view('login/reset_pass_view', $data);
    }

    public function reset_password_action()
    {
        $this->load->helper('security');
        $this->load->library('form_validation');

        $password = $this->input->post('password');
        $confirmPassword = $this->input->post('confirmPassword');
        $token = $this->input->post('token');

        // ตรวจสอบค่าว่าง
        if (empty($password) || empty($confirmPassword) || empty($token)) {
            echo json_encode([
                'success' => false,
                'message' => 'ข้อมูลไม่ครบถ้วน'
            ]);
            return;
        }

        // ตรวจสอบความยาวรหัสผ่าน
        if (strlen($password) < 6) {
            echo json_encode([
                'success' => false,
                'message' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'
            ]);
            return;
        }

        // ตรวจสอบความตรงกันของรหัสผ่าน
        if ($password !== $confirmPassword) {
            echo json_encode([
                'success' => false,
                'message' => 'รหัสผ่านไม่ตรงกัน'
            ]);
            return;
        }

        // สมมติว่า token ถูกใช้เพื่อดึง email หรือ user_id ที่ต้องการ reset
        $this->db->where('emp_forgot_token', $token);
        $user = $this->db->get('emp')->row(); // ปรับตามชื่อตารางจริง

        if (!$user) {
            echo json_encode([
                'success' => false,
                'message' => 'Token ไม่ถูกต้องหรือหมดอายุ',
                'token' => $token
            ]);
            return;
        }

        // เข้ารหัสรหัสผ่านใหม่
        $hashed_password = md5($password);

        // อัปเดตรหัสผ่านและลบ token เพื่อป้องกันการใช้ซ้ำ
        $this->db->where('emp_id', $user->emp_id);
        $this->db->update('emp', [
            'emp_pname' => $hashed_password,
            'emp_forgot_token' => null, // ลบทิ้งหลังใช้
        ]);

        echo json_encode([
            'success' => true
        ]);
    }

	public function detectDevice() {
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (preg_match('/mobile|android|iphone|ipad|tablet/', $agent)) {
			return 'mobile';
		}
		return 'pc';
	}


	



}
