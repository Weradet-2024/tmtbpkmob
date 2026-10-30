<?php
function send_notification($heading, $message, $url) {
    $content = [
        "en" => $message
    ];
    
    $fields = [
        'app_id' => "f45c4e80-d290-4182-88b9-4ed3625102a2", // ใส่ App ID ของคุณ
        'included_segments' => ['All'], // หรือระบุกลุ่มผู้ใช้งาน
        'headings' => ["en" => $heading],
        'contents' => $content,
        'url' => $url, // URL ที่ผู้ใช้จะถูกพาไปเมื่อคลิกแจ้งเตือน
    ];
    
    $fields = json_encode($fields);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.onesignal.com/notifications');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
    'Authorization: Basic os_v2_app_6roe5agssbayfcfzj3jweuicujpap2yq7piewwfv5oy3mkkxpcfxd7nx2q56tntwad3jgpzpg6sbd6xuasu6okc3ygd2aokmd3zb5gi'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}

// ทดสอบการส่งแจ้งเตือน
$response = send_notification("Hello!", "This is ETVVVVVVV.", "https://tabt_etv_mobile.tttpdxservice.com/");
echo "Response: " . $response;


// $notification_id = "5201b6e8-37bc-48e5-a134-460dcbc40d05";

// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, "https://api.onesignal.com/notifications/$notification_id?app_id=f45c4e80-d290-4182-88b9-4ed3625102a2");
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     'Authorization: Basic os_v2_app_6roe5agssbayfcfzj3jweuicujpap2yq7piewwfv5oy3mkkxpcfxd7nx2q56tntwad3jgpzpg6sbd6xuasu6okc3ygd2aokmd3zb5gi',
//     'Content-Type: application/json'
// ]);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch, CURLOPT_HEADER, FALSE);
// curl_setopt($ch, CURLOPT_POST, TRUE);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

// $response = curl_exec($ch);
// curl_close($ch);

// echo "<br>";
// echo "<br>";
// echo $response;