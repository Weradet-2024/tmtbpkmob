<?php
defined('BASEPATH') or exit('No direct script access allowed');
$v = date("His");


$lang = get_cookie('ck_emp_lang');
$cookie_expiration = 60 * 60 * 24 * 30; // 30 วัน

// ถ้าไม่มี Cookie หรือหมดอายุ → ตั้งค่าเป็น "th"
if (empty($_COOKIE["ck_emp_lang"])) {
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

    <title>(SW) TOGETAMOVE</title>
    <!-- Favicon -->
    <link rel="icon" type="" href="../assets/img/favicon/logo_togetamove.png" />
    <link rel="manifest" href="<?php echo base_url('manifest.json'); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Suwintawong">
    <link rel="icon" type="" href="../assets/img/favicon/logo_togetamove.png" />
    <link rel="apple-touch-icon" href="../assets/img/favicon/logo_togetamove.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"> -->
    <!-- วางไว้ใน <head> ของหน้า HTML -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">

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

    <!-- <script src="https://tttp-ldbox.longdo.com/map3"></script> -->
    <!-- <script src="https://tttp-ldbox.longdo.com/map3"></script> -->
	 
	  <script src="https://tttp-ldbox.longdo.com/map2"></script>

    <link rel="stylesheet"
        href=" <?php echo base_url() . 'assets/vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.css' ?>"
        type="text/css" />
    <script
        src="<?php echo base_url() . 'assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.js' ?>"></script>

</head>

<style>
    body {
        font-family: 'Sarabun', sans-serif;
        font-weight: 400;
        /* ปกติ */
    }


    /* ไล่สีพื้นหลัง navbar */
    .custom-navbar {
        background: linear-gradient(90deg, #2196f3, #00bcd4);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .bell-icon {
        font-size: 1.4rem;
        color: white;
        position: relative;
        transition: transform 0.4s ease;
    }

    .bell-icon:hover {
        transform: rotate(20deg);
        color: #ffeb3b;
    }

    .bell-badge {
        position: absolute;
        top: 0;
        right: -5px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 0.7rem;
    }

    .navbar-nav .nav-link {
        color: white !important;
        margin-left: 10px;
        font-weight: 500;
    }

    .navbar-nav .nav-link:hover {
        color: #ffd600 !important;
    }

    .dropdown-menu {
        min-width: 120px;
    }

    /* ตัว container ของ progress bar */
    #progress-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.05);
        z-index: 9999;
    }

    /* ตัว progress bar */
    #progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #ff9a9e, #fad0c4, #fad0c4, #fbc2eb, #a18cd1);
        background-size: 400% 100%;
        animation: shimmer 2s linear infinite;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
        border-radius: 0 6px 6px 0;
    }

    @keyframes shimmer {
        0% {
            background-position: 0% 50%;
        }

        100% {
            background-position: 100% 50%;
        }
    }

    /* Loading Overlay */
    #loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 10000;
        background: rgba(255, 255, 255, 0.7);
        justify-content: center;
        align-items: center;
    }

    .spinner {
        border: 8px solid #f3f3f3;
        border-top: 8px solid #3498db;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<body style="padding-bottom:20rem;">

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar bg-primary">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- โลโก้ -->
            <a class="navbar-brand" href="#">
                <img src="<?= base_url('assets/img/img/logo2.png'); ?>" alt="Logo" />
            </a>

            <!-- กระดิ่งแจ้งเตือน (โชว์เฉพาะหน้า Home) -->

            <div class="d-flex align-items-center">
                <!-- ปุ่มกระดิ่งแจ้งเตือน (หน้า Home เท่านั้น) -->
                <button class="btn text-white me-2" id="NotificationLink" style="font-size: 20px;">
                    <i class="bi bi-bell-fill"></i>
                </button>

                <!-- ปุ่ม burger -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-ex-7">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <!-- เมนู collapsible -->
            <div class="collapse navbar-collapse" id="navbar-ex-7">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="Home"><?= $this->lang->line('home'); ?></a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <!-- เลือกภาษา -->
                    <li class="nav-item dropdown">
                        <button class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <?= $this->lang->line('Language'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="javascript:void(0);" onclick="chkLang('en')">English</a>
                            </li>
                            <li><a class="dropdown-item" href="javascript:void(0);" onclick="chkLang('th')">ไทย</a></li>
                        </ul>
                    </li>

                    <!-- การตั้งค่า -->
                    <li class="nav-item">
                        <a class="nav-link" href="Setting">
                            <i class="bi bi-gear-fill"></i> <?= $this->lang->line('setting'); ?>
                        </a>
                    </li>

                    <!-- ปุ่มรับการแจ้งเตือน -->
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="javascript:void(0);" id="subscribeLink">
                            <i class="bi bi-bell-fill"></i> <?= $this->lang->line('enable_notification'); ?>
                        </a>
                    </li>

                    <!-- ออกจากระบบ -->
                    <li class="nav-item">
                        <a class="nav-link" href="Login/logout">
                            <i class="bi bi-box-arrow-right"></i> <?= $this->lang->line('logout'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    
    <p id="token"></p>
    <p id="installPromptBtn"></p>
    <div id="progress-container">
        <div id="progress-bar"></div>
    </div>
    <script>
        function chkLang(get_lang) {
            let option_lang = get_lang;
            $.ajax({
                type: "POST",
                url: "Home/chk_lang",
                data: {
                    set_lang: option_lang
                },
                success: function(response) {
                    obj = JSON.parse(response);
                    if (obj.status == "ok") {
                        Swal.fire({
                            title: 'Success',
                            text: 'Set language complete',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });

                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else {
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
    <!-- โหลด Firebase SDK จาก CDN -->
    <script src="/firebase/firebase-app.js"></script>
    <script src="/firebase/firebase-messaging.js"></script>


    <script>
        // 🔹 Firebase Configuration
        const firebaseConfig = {
            apiKey: "AIzaSyDbu4QNS04Y0HqLPpKpW7QnYw0RgspPV8I",
            authDomain: "tmaetv-c3db9.firebaseapp.com",
            projectId: "tmaetv-c3db9",
            storageBucket: "tmaetv-c3db9.firebasestorage.app",
            messagingSenderId: "442851494137",
            appId: "1:442851494137:web:51d2e76115d48ad9a33a28",
            measurementId: "G-GLE277ZXJK"
        };

        function getCookie(name) {
            var value = "; " + document.cookie;
            var parts = value.split("; " + name + "=");
            if (parts.length == 2) return parts.pop().split(";").shift();
        }

        $(document).ready(async function() {

            function showLoader() {
                var overlay = document.getElementById('loading-overlay');
                if (overlay) overlay.style.display = 'flex';
            }

            $('#NotificationLink').click(function(e) {
                e.preventDefault(); // ป้องกันการกระพริบหรือ reload หน้า (ถ้า <a>)
                showLoader();
                window.location.href = 'Notification';
            });


            const subscribeBtn = document.getElementById("subscribeLink");
            if (!subscribeBtn) {
                console.error("❌ ปุ่ม Subscribe ไม่พบใน DOM!");
                return;
            }

            // ✅ ตรวจสอบว่า Browser รองรับ Service Worker และ PushManager หรือไม่
            if (!("serviceWorker" in navigator)) {
                console.error("❌ Service Worker ไม่รองรับใน Browser นี้!");
                //alert("Service Worker ไม่รองรับใน Browser นี้!");
                return;
            }

            if (!("PushManager" in window)) {
                console.error("❌ PushManager ไม่รองรับใน Browser นี้!");
                // alert("PushManager ไม่รองรับใน Browser นี้!");
                return;
            }

            // ✅ ตรวจสอบ iOS และเวอร์ชัน
            const isIOS = /iPhone|iPad|iPod/.test(navigator.userAgent);
            const iOSVersion = navigator.userAgent.match(/OS (\d+)_/);
            const isPWA = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone;

            if (isIOS) {
                if (!iOSVersion || parseInt(iOSVersion[1]) < 16.4) {
                    // alert("Web Push ไม่รองรับบน iOS เวอร์ชันต่ำกว่า 16.4");
                    return;
                }
                if (!isPWA) {
                    //alert("กรุณาติดตั้งแอปเป็น PWA ก่อนสมัครรับการแจ้งเตือน!");
                    return;
                }
            }

            try {
                subscribeBtn.addEventListener("click", async () => {
                    try {
                        //alert("ขอสิทธิ์แจ้งเตือน...");

                        // ✅ เริ่มต้น Firebase
                        if (!firebase.apps.length) {
                            firebase.initializeApp(firebaseConfig);
                        }
                        const messaging = firebase.messaging();


                        // ✅ ลงทะเบียน Service Worker
                        const registration = await navigator.serviceWorker.register("/firebase-messaging-sw.js", {
                            scope: "/"
                        });
                        console.log("✅ ลงทะเบียน Service Worker สำเร็จ:", registration);

                        await navigator.serviceWorker.ready;
                        console.log("✅ Service Worker พร้อมใช้งาน!");

                        // ✅ ขออนุญาตการแจ้งเตือน
                        const permission = await Notification.requestPermission();
                        if (permission !== "granted") {
                            console.log("❌ Notifications ถูกปฏิเสธ!");
                            alert("การแจ้งเตือนถูกปฏิเสธ!");
                            return;
                        }

                        console.log("✅ Notification permission granted.");

                        // ✅ ขอ FCM Token
                        const currentToken = await messaging.getToken({
                            vapidKey: "BAGG8TmRsF28ipbvUpsK75VVTSND2ltxv1zU3RhocOnyvDNvvwV5kJ2YU7KaKPYj_ln_9kSeEwhIDytGhQ_RCzs",
                            serviceWorkerRegistration: registration
                        });

                        if (currentToken) {
                            console.log("✅ Token ได้รับแล้ว:", currentToken);
                            localStorage.setItem("fcmToken", currentToken);

                            try {
                                const response = await fetch("<?= base_url('index.php/Push/keepFCMToken') ?>", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        token: currentToken
                                    })
                                });

                                const result = await response.json(); // หรือใช้ .text() ถ้า response ไม่ใช่ JSON
                                console.log("Server response:", result);
                            } catch (error) {
                                console.error("Fetch error:", error);
                                alert("error");
                            }

                            console.log("✅ Token ถูกส่งไป Backend สำเร็จ!");
                            var confirm_notification = getCookie('ck_emp_lang') == "th" ? "สมัครรับการแจ้งเตือนสำเร็จ!" : "Notification enabled";
                            alert("✅ " + confirm_notification);
                        } else {
                            console.error("❌ ไม่สามารถรับ Token ได้!");
                            alert("ไม่สามารถรับ Token ได้!");
                        }
                    } catch (error) {
                        console.error("❌ Error:", error);
                        alert("Error: " + error.message);
                    }
                });

            } catch (error) {
                console.error("❌ Error:", error);
            }
        });
    </script>
    <script>
        const bar = document.getElementById("progress-bar");
        // ค่อย ๆ เติม progress bar
        let progress = 0;
        let interval = setInterval(() => {
            if (progress < 90) {
                progress += Math.random() * 5;
                bar.style.width = progress + "%";
            }
        }, 100);

        // เมื่อหน้าโหลดเสร็จ
        window.addEventListener("load", () => {
            clearInterval(interval);
            bar.style.width = "100%";

            setTimeout(() => {
                document.getElementById("progress-container").style.display = "none";
            }, 400);
        });
    </script>

    <!-- Loading Overlay -->
    <div id="loading-overlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:10000;background:rgba(255,255,255,0.7);justify-content:center;align-items:center;">
        <div class="spinner"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            let target = e.target;
            while (target && target.tagName !== 'A') {
                target = target.parentElement;
            }
              // ตรวจสอบว่าอยู่ใน pagination หรือไม่
            const isPagination = 
            target && 
            typeof target.closest === 'function' &&
            (target.closest('.paginate_button') || target.hasAttribute('data-dt-idx'));

            if (
                target && target.tagName === 'A' &&
                target.getAttribute('href') &&
                !target.getAttribute('target') &&
                !target.getAttribute('href').startsWith('javascript:')&&
                !target.getAttribute('href') !== '#' &&
                !isPagination &&
                !target.classList.contains('no-loading-overlay')
            ) {
                document.getElementById('loading-overlay').style.display = 'flex';
            }
        }, true);
    });
    </script>
