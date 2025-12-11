<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Content-Type: application/json');
require_once APPPATH . "third_party/google-api-php-client/vendor/autoload.php";
require_once APPPATH . "third_party/php_jwt/src/JWT.php";

use \Firebase\JWT\JWT;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

class Push extends CI_Controller
{

    private const FCM_ENDPOINT = 'https://fcm.googleapis.com/v1/projects/tmaetv-c3db9/messages:send';
    private const FCM_SERVER_KEY = 'AIzaSyDbu4QNS04Y0HqLPpKpW7QnYw0RgspPV8I';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('fcm_token_model');
    }

    public function getAccessToken()
    {

        // Path ไปยังไฟล์ Service Account JSON
        $serviceAccountJsonPath = base_url() . 'service_account.json';

        // อ่านไฟล์ JSON และแปลงเป็น array
        $credentials = json_decode(file_get_contents($serviceAccountJsonPath), true);
        if (!$credentials) {
            die(json_encode(["error" => "Failed to decode JSON file. Check the file format."]));
        }

        // ตรวจสอบคีย์ที่จำเป็น
        if (!isset($credentials['client_email']) || !isset($credentials['private_key'])) {
            die(json_encode(["error" => "Invalid service account JSON format. Missing client_email or private_key."]));
        }

        // สร้าง JWT (JSON Web Token) สำหรับการขอ access token
        $iss = $credentials['client_email'];
        $aud = 'https://oauth2.googleapis.com/token';
        $iat = time(); // เวลาปัจจุบัน
        $exp = $iat + 3600; // กำหนดอายุ 1 ชั่วโมง

        // ข้อมูลใน payload ของ JWT
        $payload = [
            'iss' => $iss,
            'aud' => $aud,
            'iat' => $iat,
            'exp' => $exp,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging', // ขอบเขตที่ต้องการ
        ];

        // อ่าน private key
        $privateKey = $credentials['private_key'];

        try {
            // สร้าง JWT Token
            $jwt = new \Firebase\JWT\JWT();
            $accessToken = $jwt->encode($payload, $privateKey, 'RS256');
        } catch (Exception $e) {
            die(json_encode(["error" => "Failed to create JWT. " . $e->getMessage()]));
        }

        // ส่งคำขอไปยัง Google OAuth2 เพื่อขอ Access Token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $aud);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $accessToken
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            die(json_encode(["error" => "Curl error: " . curl_error($ch)]));
        }

        curl_close($ch);

        // รับข้อมูล Access Token
        $responseData = json_decode($response, true);
        if (isset($responseData['error'])) {
            die(json_encode(["error" => "Failed to retrieve access token. " . $responseData['error_description']]));
        }


        return $responseData['access_token'];
    }

    public function keepFCMToken()
    {
        // รับ JSON Input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true); // แปลง JSON เป็น Array

        // ตรวจสอบว่ามี token ส่งมาไหม
        if (!isset($data['token']) || empty($data['token'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Token is required'
            ]);
            return;
        }

        $token = $data['token'];

        $ck_emp_id = $this->session->userdata('emp_id');

        $existingData = $this->fcm_token_model->get_fcm_token($ck_emp_id);
        $response = [];

        if (!empty($existingData)) {
            // Update fcm token
            $data_update = $this->fcm_token_model->update_fcm_token(['fcm_token' => $token], $ck_emp_id);
            if ($data_update) {
                $response = [
                    'status' => 'success',
                    'message' => 'Token updated successfully',
                    'token' => $token
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Error updating token'
                ];
            }
        } else {
            // Insert fcm token
            $data_insert = $this->fcm_token_model->insert_fcm_token([
                'fcm_token' => $token,
                'fcm_datetime' => date('Y-m-d H:i:s'),
                'fcm_emp_id' => $ck_emp_id
            ]);

            if ($data_insert) {
                $response = [
                    'status' => 'success',
                    'message' => 'Token added successfully',
                    'token' => $token
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Error adding token'
                ];
            }
        }

        echo json_encode($response);
    }

    /**
     * Send request to FCM
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function sendFCM(array $data, $accessToken)
    {
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::FCM_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($result === false) {
            throw new Exception('FCM request failed: ' . $error);
        }

        $responseData = json_decode($result, true);

        if (isset($responseData['error'])) {
            log_message('error', 'FCM API Error: ' . $result);
        }

        return $responseData;
    }

    public function noti_arrage()
    {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
        $accessToken = $this->getAccessToken();

        if (!isset($data['tokens']) || !is_array($data['tokens'])) {
        }

        $title = $data['title'] ?? 'แจ้งเตือน';
        $body = $data['body'] ?? '';

        foreach ($data['tokens'] as $token) {

            $message_id = uniqid();

            $data = [
                "message" => [
                    "token" => $token,
                    "data" => [  // ใช้เฉพาะ data (ไม่ใช้ notification)
                        "title" => $title,
                        "body" => $body,
                        "link" => "https://tmtswmob.tttpdx.com/index.php/main/index/fcm",
                        "message_id" => $message_id
                    ]
                ]
            ];

            try {
                $response = $this->sendFCM($data, $accessToken);
                $responses[] = $response;
            } catch (Exception $ex) {
                $responses[] = [
                    'error' => $ex->getMessage(),
                    'token' => $token
                ];
            }

        }

        echo json_encode(['status' => 'sent', 'count' => count($data['tokens'])]);
    }

    public function boradcast_reservation()
    {
        try {
            $json_array = $this->input->post('json_arr_all');
            $accessToken = $this->getAccessToken();

            // ดึง Token ทั้งหมด
            $data_token = $this->fcm_token_model->get_fcm_token_by_json($json_array);

            if (!is_array($data_token)) {
                throw new Exception('Error retrieving FCM tokens from model.');
            }

            // กรองเฉพาะ Token ที่ไม่ว่าง และกำจัด Token ซ้ำ
            $valid_tokens = array_unique(array_merge(...array_column($data_token, 'fcm_token')));

            if (empty($valid_tokens)) {
                throw new Exception('No valid FCM tokens found.');
            }

            $responses = [];
            foreach ($valid_tokens as $token) {
                // สร้าง ID ที่ไม่ซ้ำ

                // เพิ่ม `message_id` ในข้อมูลที่ส่งไป
                // สร้าง message_id โดยใช้ uniqid() หรือวิธีการอื่นที่ต้องการ
                $message_id = uniqid();


                $data = [
                    "message" => [
                        "token" => $token,
                        "data" => [  // ใช้เฉพาะ data (ไม่ใช้ notification)
                            "title" => "การแจ้งเตือนใหม่",
                            "body" => "มีการจองสำเร็จแล้ว ตรวจสอบข้อมูลการจัดรถ",
                            "link" => "https://tmtsrmob.tttpdx.com/index.php/main/index/fcm",
                            "message_id" => $message_id
                        ]
                    ]
                ];




                $response = $this->sendFCM($data, $accessToken);

                $responses[] = $response;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'responses' => $responses
                ]));

        } catch (Exception $e) {
            log_message('error', "Broadcast Reservation Error: " . $e->getMessage());

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]));
        }
    }

    public function boradcast()
    {
        try {
            $accessToken = $this->getAccessToken();

            $id = $this->input->post('id');
            $data_token = $this->fcm_token_model->get_fcm_token_by_boardcast_id($id);

            if (!is_array($data_token) || empty($data_token)) {
                throw new Exception('No tokens found or invalid data');
            }

            // ตรวจสอบข้อมูล boardcast_title, boardcast_body จาก token แรก
            $boardcast_title = isset($data_token[0]['broadcast_title']) ? $data_token[0]['broadcast_title'] : '';
            $boardcast_body = isset($data_token[0]['broadcast_detail']) ? $data_token[0]['broadcast_detail'] : '';

            // เตรียม token ที่ไม่ซ้ำ
            $valid_tokens = array_unique(array_filter(array_column($data_token, 'fcm_token')));

            if (empty($valid_tokens)) {
                throw new Exception('No valid FCM tokens found.');
            }

            // ตัดความยาวข้อความ
            $boardcast_body_plain = strip_tags($boardcast_body);
            $max_length = 10;
            if (strlen($boardcast_body_plain) > $max_length) {
                $boardcast_body_plain = substr($boardcast_body_plain, 0, $max_length) . '...';
            }

            $responses = [];
            $all_data = [];
            foreach ($valid_tokens as $token) {
                $message_id = uniqid();

                $data = [
                    "message" => [
                        "token" => $token,
                        "data" => [  // ใช้เฉพาะ data (ไม่ใช้ notification)
                            "title" => $boardcast_title,
                            "body" => "ตรวจสอบข้อมูล",
                            "link" => "https://tmtsrmob.tttpdx.com/index.php/main/index/fcm",
                            "message_id" => $message_id
                        ]
                    ]
                ];

                $all_data[] = $data;

                try {
                    $response = $this->sendFCM($data, $accessToken);
                    $responses[] = $response;
                    $this->fcm_token_model->update_latest_noti_date($id);
                } catch (Exception $ex) {
                    $responses[] = ['error' => $ex->getMessage(), 'token' => $token];
                }
            }
            
            $output = json_encode([
                'success' => true,
                'all_data' => $all_data,
                'responses' => $responses
            ]);

            if ($output === false) {
                $jsonError = json_last_error_msg();
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'error' => "json_encode error: $jsonError"
                    ]));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output($output);

        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]));
        }
    }

    public function alert_approve()
    {
        try {
            $accessToken = $this->getAccessToken();

            $data_token = $this->fcm_token_model->get_all_fcm_token();

            if (!is_array($data_token) || empty($data_token)) {
                throw new Exception('No tokens found or invalid data');
            }

            // เตรียม token ที่ไม่ซ้ำ
            $valid_tokens = array_unique(array_filter(array_column($data_token, 'fcm_token')));

            if (empty($valid_tokens)) {
                throw new Exception('No valid FCM tokens found.');
            }

            $responses = [];
            $all_data = [];
            foreach ($valid_tokens as $token) {
                $message_id = uniqid();

                $data = [
                    "message" => [
                        "token" => $token,
                        "data" => [  // ใช้เฉพาะ data (ไม่ใช้ notification)
                            "title" => 'Approve Your Reservation',
                            "body" => "ตรวจสอบข้อมูล",
                            "link" => "https://tmtsrmob.tttpdx.com/",
                            "message_id" => $message_id
                        ]
                    ]
                ];

                $all_data[] = $data;

                try {
                    $response = $this->sendFCM($data, $accessToken);
                    $responses[] = $response;
                } catch (Exception $ex) {
                    $responses[] = ['error' => $ex->getMessage(), 'token' => $token];
                }
            }

            $output = json_encode([
                'success' => true,
                'all_data' => $all_data,
                'responses' => $responses
            ]);

            if ($output === false) {
                $jsonError = json_last_error_msg();
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'error' => "json_encode error: $jsonError"
                    ]));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output($output);

        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]));
        }
    }

    public function get_all_broadcast() {
        $data = $this->fcm_token_model->get_all_broadcast();
        echo json_encode($data);
    }

    public function send_broadcast_now()
    {

    try {
        $broadcasts = $this->fcm_token_model->get_broadcasts_to_notify();


        if (empty($broadcasts)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'No broadcast to notify at this time.'
                ]));
        }

        $accessToken = $this->getAccessToken();
        $all_responses = [];

        foreach ($broadcasts as $broadcast) {
            $broadcast_id = $broadcast['broadcast_id'];
            $broadcast_title = $broadcast['broadcast_title'];
            $broadcast_detail = $broadcast['broadcast_detail'];

            // ดึง fcm_token ของพนักงานที่อยู่ใน plant เดียวกัน
            $tokens_data = $this->fcm_token_model->get_fcm_token_by_boardcast_id($broadcast_id);

            if (empty($tokens_data)) {
                continue;
            }

            // ดึง token จริง และลบ token ซ้ำ
            $tokens = array_unique(array_filter(array_column($tokens_data, 'fcm_token')));
            $responses = [];

            foreach ($tokens as $token) {
                $message_id = uniqid();

                $data = [
                    "message" => [
                        "token" => $token,
                        "data" => [
                            "title" => strip_tags($broadcast_title),
                            "body" => "ตรวจสอบข้อมูล",
                            "link" => "https://tmtsrmob.tttpdx.com/index.php/main/index/fcm",
                            "message_id" => $message_id
                        ]
                    ]
                ];

                try {
                    $response = $this->sendFCM($data, $accessToken);
                    $responses[] = $response;
                } catch (Exception $e) {
                    $responses[] = [
                        'error' => $e->getMessage(),
                        'token' => $token
                    ];
                }
            }

            // อัปเดตเวลาส่งล่าสุด
            $this->fcm_token_model->update_latest_noti_date($broadcast_id);

            $all_responses[] = [
                'broadcast_id' => $broadcast_id,
                'tokens_sent' => count($tokens),
                'responses' => $responses
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'result' => $all_responses
            ]));

    } catch (Exception $e) {
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
    }
    }

    public function send_all_broadcast_now()
    {

        try {
        // ดึง broadcast ทั้งหมด
        $broadcasts = $this->fcm_token_model->get_all_broadcast();

        if (empty($broadcasts)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'No broadcasts found.'
                ]));
        }

        $accessToken = $this->getAccessToken();
        $all_responses = [];

        foreach ($broadcasts as $broadcast) {
            $broadcast_id = $broadcast['broadcast_id'];
            $broadcast_title = strip_tags($broadcast['broadcast_title']);
            $broadcast_detail = strip_tags($broadcast['broadcast_detail']);

            // ดึง fcm_token ตาม plant (ตามที่คุณเขียนไว้ใน fcm_token_model)
            $tokens_data = $this->fcm_token_model->get_fcm_token_by_boardcast_id($broadcast_id);
            $tokens = array_unique(array_filter(array_column($tokens_data, 'fcm_token')));

            if (empty($tokens)) {
                $all_responses[] = [
                    'broadcast_id' => $broadcast_id,
                    'message' => 'No tokens found for this broadcast.'
                ];
                continue;
            }

            $responses = [];

            foreach ($tokens as $token) {
                $message_id = uniqid();

                $data = [
                    "message" => [
                        "token" => $token,
                        "data" => [
                            "title" => $broadcast_title,
                            "body" => "ตรวจสอบข้อมูล",
                            "link" => "https://tmtsrmob.tttpdx.com/index.php/main/index/fcm",
                            "message_id" => $message_id
                        ]
                    ]
                ];

                try {
                    $response = $this->sendFCM($data, $accessToken);
                    $responses[] = $response;
                } catch (Exception $ex) {
                    $responses[] = [
                        'error' => $ex->getMessage(),
                        'token' => $token
                    ];
                }
            }

            // อัปเดตเวลาส่งล่าสุด
            $this->fcm_token_model->update_latest_noti_date($broadcast_id);

            $all_responses[] = [
                'broadcast_id' => $broadcast_id,
                'tokens_sent' => count($tokens),
                'responses' => $responses
            ];
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'result' => $all_responses
            ]));

        } catch (Exception $e) {
                return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                'success' => false,
                'error' => $e->getMessage()
                ]));
        }
    }

    private function isTimeWithinMargin($targetTime, $marginInSeconds = 300)
    {
        $now = time();
        $target = strtotime($targetTime);
        return abs($now - $target) <= $marginInSeconds;
    }

    public function notify_cron()
    {
    date_default_timezone_set('Asia/Bangkok');
    try {
        $now = date("H:i");
        $today = date("Y-m-d");
        $dayName = strtolower(date("l"));

        $broadcasts = $this->fcm_token_model->get_all_broadcast();
        $accessToken = $this->getAccessToken();
        $sent = [];

        foreach ($broadcasts as $bc) {
            $shouldSend = false;

            if ($bc['broadcast_type'] === 'once') {
                if (
                    $bc['broadcast_start_date'] === $today &&
                    $this->isTimeWithinMargin('12:00') &&
                    empty($bc['broadcast_lastest_noti']) 
                ) {
                    $shouldSend = true;
                }
            } elseif ($bc['broadcast_type'] === 'once_per_day') {
                if ($this->isTimeWithinMargin($bc['broadcast_daily_time'])) {
                    $shouldSend = true;
                }
            } elseif ($bc['broadcast_type'] === 'weekly') {
                if ($bc['broadcast_weekly_day'] === $dayName && $this->isTimeWithinMargin($bc['broadcast_daily_time'])) {
                    $shouldSend = true;
                }
            }

            if ($shouldSend) {
                $tokenData = $this->fcm_token_model->get_fcm_token_by_boardcast_id($bc['broadcast_id']);
                $tokens = array_unique(array_filter(array_column($tokenData, 'fcm_token')));

                foreach ($tokens as $token) {
                    $message_id = uniqid();
                    $link = $bc['broadcast_is_reminder'] == 1
                        ? "https://tmtsrmob.tttpdx.com/index.php/main/gotoReserve/fcm"
                        : "https://tmtsrmob.tttpdx.com/index.php/main/gotoNotification/fcm";

                    $data = [
                        "message" => [
                            "token" => $token,
                            "data" => [
                                "title" => $bc['broadcast_title'],
                                "body" => strip_tags($bc['broadcast_detail']),
                                "link" => $link,
                                "message_id" => $message_id
                            ]
                        ]
                    ];

                    try {
                        $this->sendFCM($data, $accessToken);
                    } catch (Exception $ex) {
                        log_message('error', 'FCM Error: ' . $ex->getMessage());
                    }
                }

                $this->fcm_token_model->update_latest_noti_date($bc['broadcast_id']);
                $sent[] = $bc['broadcast_id'];
                log_message('debug', 'Sending broadcast ID: ' . $bc['broadcast_id']);
            }
        }

        echo json_encode(['success' => true, 'dayName' => $dayName, 'time' => $now, 'sent' => $sent]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    }

}
