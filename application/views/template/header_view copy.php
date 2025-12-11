<?php
defined('BASEPATH') or exit('No direct script access allowed');
$v = date("His");


$lang = get_cookie('ck_emp_lang');
$cookie_expiration = 60 * 60 * 24 * 30; // 30 วัน

// ถ้าไม่มี Cookie หรือหมดอายุ → ตั้งค่าเป็น "th"
if (empty($lang) || empty($_COOKIE["emp_lang"])) {
    $lang = 'th';
    $this->input->set_cookie('ck_emp_lang', $lang, $cookie_expiration);
}


?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Togeta</title>
    <!-- Favicon -->
    <link rel="icon" type="" href="../assets/img/favicon/favicon.png" />
    <link rel="manifest" href="<?php echo base_url('manifest.json'); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <!-- Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-defaults.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/typeahead-js/typeahead.css" />
    <!-- Vendor -->
    <link rel="stylesheet" href="../assets/vendor/libs/@form-validation/umd/styles/index.min.css" />
    <!-- Page -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
    <script src="../assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
    <script src="../assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    <!-- Page JS -->
    <script src="../assets/js/pages-auth.js"></script>
    <link href="<?php echo base_url() . 'assets/vendor/bootstrap-icons/bootstrap-icons.css' ?>" rel="stylesheet">
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css">
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css">
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <script src="https://tttp-ldbox.longdo.com"></script>
    <!-- <script src="https://tttp-ldbox.longdo.com/map3"></script> -->

    <link rel="stylesheet"
        href=" <?php echo base_url() . 'assets/vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.css' ?>"
        type="text/css" />
    <script
        src="<?php echo base_url() . 'assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.js' ?>"></script>

</head>
<body style="padding-bottom:20rem;">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top mb-5">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?php echo base_url() . 'assets/img/img/logo2.png' ?>"/>
           
            </a>
			<input type="text" id="ck_emp_lang" name="ck_emp_lang" value="<?=$_COOKIE["ck_emp_lang"]; ?>" hidden>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-ex-7">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar-ex-7">
                <div class="navbar-nav me-auto">
                    <a class="nav-item nav-link active" href="Home"><?= $this->lang->line('home'); ?></a>
                </div>
                <div class="navbar-nav me-auto">
                    <li class="nav-item dropdown-language dropdown me-2 me-xl-0" style="list-style:none; float: right;">
                        <a class="nav-link dropdown-toggle hide-arrow" style="color:white;" href="javascript:void(0);"
                            data-bs-toggle="dropdown">
                            <?= $this->lang->line('Language'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="chkLang('en')"
                                    data-language="en" data-text-direction="ltr">
                                    <span class="align-middle">
                                        English</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="chkLang('th')"
                                    data-language="fr" data-text-direction="ltr">
                                    <span class="align-middle">
                                        ไทย</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </div>

                <ul class="navbar-nav ms-lg-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Setting"><i class="tf-icons navbar-icon bx bx-cog"></i>
                            <?= $this->lang->line('setting'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Login/logout"><i class="tf-icons navbar-icon bx bx-lock-open-alt"></i>
                            <?= $this->lang->line('logout'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <p id="token"></p>
    <script>
        function chkLang(get_lang) {
            let option_lang = get_lang;
            $.ajax({
                type: "POST",
                url: "Home/chk_lang",
                data: { set_lang: option_lang },
                success: function (response) {

                    if (response == "1") {
                        Swal.fire({
                            title: 'Success',
                            text: 'Set language complete',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });

                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    }
                    else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Set language Error',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        }
    </script>

    <!-- Firebase SDK -->
    <!-- Firebase เวอร์ชัน 8.x -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>


    <script>
        // Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyDbu4QNS04Y0HqLPpKpW7QnYw0RgspPV8I",
            authDomain: "tmaetv-c3db9.firebaseapp.com",
            projectId: "tmaetv-c3db9",
            storageBucket: "tmaetv-c3db9.firebasestorage.app",
            messagingSenderId: "442851494137",
            appId: "1:442851494137:web:51d2e76115d48ad9a33a28",
            measurementId: "G-GLE277ZXJK"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // ลงทะเบียน Service Worker
        //navigator.serviceWorker.register('/firebase-messaging-sw.js', { updateViaCache: 'none' })
        // navigator.serviceWorker.register('/firebase-messaging-sw.js?v=<?= $v ?>')
        navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' })
            .then((registration) => {
                console.log('✅ Service Worker registered.');

                return Notification.requestPermission();
            })
            .then((permission) => {
                if (permission === 'granted') {
                    console.log('✅ Notification permission granted.');

                    // const storedToken = localStorage.getItem('fcmToken');
                    // if (storedToken) {
                    //     console.log('✅ Token มีอยู่แล้ว:', storedToken);
                    //     return null;
                    // }

                    return messaging.getToken({
                        vapidKey: 'BAGG8TmRsF28ipbvUpsK75VVTSND2ltxv1zU3RhocOnyvDNvvwV5kJ2YU7KaKPYj_ln_9kSeEwhIDytGhQ_RCzs'
                    });
                } else {
                    console.log('❌ Notification permission denied.');
                    return null;
                }
            })
            .then((currentToken) => {
                if (currentToken) {
                    console.log('✅ Token ได้รับแล้ว:', currentToken);
                    localStorage.setItem('fcmToken', currentToken);

                    console.log(currentToken);

                    return fetch('<?= base_url('index.php/Push/keepFCMToken') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ token: currentToken })
                    })
                        .then(response => response.json())
                        .then(data => console.log('✅ Token ส่งไป Backend สำเร็จ:', data))
                        .catch(error => console.error('❌ Error ส่ง Token:', error));
                } else {
                    console.log('❌ ไม่มี Token ได้รับ หรือ Token มีอยู่แล้ว');
                }
            })
            .catch((err) => {
                console.error(err);
            });

        // ตรวจจับเมื่อ Token หมดอายุ และรับ Token ใหม่
        messaging.onTokenRefresh(() => {
            messaging.getToken().then((refreshedToken) => {
                console.log('🔄 Token ถูกรีเฟรช:', refreshedToken);
                localStorage.setItem('fcmToken', refreshedToken);

                console.log(refreshedToken);

                return fetch('<?= base_url('index.php/Push/keepFCMToken') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: refreshedToken })
                })
                    .then(response => response.json())
                    .then(data => console.log('✅ Token อัปเดตที่ Backend สำเร็จ:', data))
                    .catch(error => console.error('❌ Error อัปเดต Token:', error));
            }).catch((err) => {
                console.error('❌ ไม่สามารถรีเฟรช Token:', err);
            });
        });

    </script>

    <!-- <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
      window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
        // เริ่มต้น OneSignal
        await OneSignal.init({
            appId: "f45c4e80-d290-4182-88b9-4ed3625102a2",  // ใส่ appId ของคุณ
            notifyButton: {
            enable: true,  // เปิดปุ่มสมัครรับการแจ้งเตือน
            }
        });
    
        });

    </script> -->
