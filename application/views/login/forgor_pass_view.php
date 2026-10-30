<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Forgot Password - TOGETAMOVE</title>

    <!-- Favicon -->
    <link rel="icon" href="../../assets/img/favicon/favicon.png" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url() . '../assets/vendor/css/rtl/core.css  ' ?>" />
    <link rel="stylesheet" href="<?php echo base_url() . '../assets/vendor/css/rtl/theme-defaults.css' ?>" />
    <link rel="stylesheet" href="<?php echo base_url() . '../assets/css/demo.css' ?>" />
    <link rel="stylesheet" href="<?php echo base_url() . '../assets/vendor/css/pages/page-auth.css' ?>" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">

</head>

<style>
    .pointer {
        cursor: pointer;
    }
</style>

<body style="background-color: #FFFFFF">
    <section class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm w-100" style="max-width: 400px;">
            <div class="card-body p-4">

                <div class="text-center mb-4">
                    <img src="<?php echo base_url() . '../assets/img/favicon/logo_animation.gif' ?>" alt="Logo"
                        style="width: 60%;">
                    <h5 class="mt-3">ลืมรหัสผ่าน</h5>
                    <p class="text-muted small">กรุณากรอกอีเมล เพื่อรับลิงก์รีเซ็ตรหัสผ่าน</p>
                </div>

                <!-- Form -->
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label for="userInput" class="form-label">อีเมล</label>
                        <input type="text" class="form-control" id="userInput" placeholder="กรอกข้อมูลของคุณ" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">ส่งคำขอรีเซ็ตรหัสผ่าน</button>

                    <div class="text-center mt-3">
                        <span onclick="backVal()" class="small text-info pointer">กลับไปหน้าเข้าสู่ระบบ</span>
                    </div>
                </form>

            </div>
        </div>
    </section>

    <!-- JS -->
    <script src="<?php echo base_url() . '../assets/vendor/libs/jquery/jquery.js' ?>"></script>
    <script src="<?php echo base_url() . '../assets/vendor/js/bootstrap.js' ?>"></script>
    <script>
        $('#forgotPasswordForm').on('submit', function (e) {
            e.preventDefault();
            const userInput = $('#userInput').val().trim();

            if (userInput === '') {
                alert('กรุณากรอกข้อมูล');
                return;
            }

            check_email_user(userInput);


        });

        /*
    * check_email_user
    * check e-mail in database using ajax
    * @input user_email
    * @output -
    * @author Weradet Nopsombun 62160110 
    * @Create Date 2568-06-17
    * @Update -
    */
        function check_email_user(user_email) {
            // แสดง loading ขณะรอผล
            Swal.fire({
                title: 'กำลังตรวจสอบ...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: '<?php echo site_url() . '/Login/check_email_user'; ?>',
                data: {
                    user_email: user_email
                },
                success: function (json_res) {
                    if (json_res == 1) {
                        Swal.fire({
                            title: "ระบบได้จัดส่งอีเมลของท่านเรียบร้อยแล้ว",
                            text: "โปรดตรวจสอบอีเมลของท่าน",
                            icon: "success",
                        }).then(() => {
                            window.location = "<?php echo site_url() . '/Login'; ?>";
                        });
                    } else {
                        Swal.fire({
                            title: "ไม่พบอีเมลของท่านในระบบ",
                            text: "กรุณากรอกใหม่อีกครั้ง",
                            icon: "warning"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "เกิดข้อผิดพลาด",
                        text: "ไม่สามารถตรวจสอบอีเมลได้",
                        icon: "error"
                    });
                }
            });
        }


        function backVal() {
            window.location.href = ('../Login');
        }

    </script>
</body>

</html>