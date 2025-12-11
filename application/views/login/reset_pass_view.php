<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="th" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>ตั้งค่ารหัสผ่านใหม่ - TOGETAMOVE</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo base_url('assets/img/favicon/favicon.png') ?>" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans&display=swap" rel="stylesheet" />

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #66b2ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card-reset {
            max-width: 440px;
            width: 100%;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 40px 35px;
            text-align: center;
            animation: fadeInUp 0.8s ease forwards;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-img {
            width: 65%;
            margin-bottom: 18px;
            animation: bounce 1.5s infinite alternate;
        }

        @keyframes bounce {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-12px);
            }
        }

        h4 {
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
        }

        .form-label {
            font-weight: 600;
            color: #555;
        }

        .btn-reset {
            background: #0d6efd;
            border: none;
            font-weight: 700;
            padding: 12px;
            transition: background 0.3s ease;
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.6);
        }

        .btn-reset:hover {
            background: #0b5ed7;
            box-shadow: 0 8px 20px rgba(11, 94, 215, 0.8);
        }

        .back-link {
            margin-top: 24px;
            font-weight: 600;
            color: #0d6efd;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            user-select: none;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #0b5ed7;
        }
    </style>
</head>

<body>
    <div class="card-reset shadow-sm">
        <img src="<?php echo base_url('assets/img/favicon/logo_animation.gif') ?>" alt="Logo" class="logo-img" />

        <h4>Set a New Password</h4>
        <p class="text-muted mb-4">Please enter and confirm your new password</p>

        <form id="resetPasswordForm" novalidate>
            <div class="mb-3 text-start">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control form-control-lg" id="password" placeholder="New Password"
                    required minlength="6" />
                <div class="invalid-feedback">
                    Please enter a password of at least 6 characters.
                </div>
            </div>

            <div class="mb-3 text-start">
                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control form-control-lg" id="confirmPassword"
                    placeholder="Confirm New Password" required />
                <div class="invalid-feedback" id="confirmError">
                    Passwords do not match
                </div>
            </div>

            <button type="submit" class="btn btn-reset w-100 text-white">Set New Password</button>
        </form>

        <div class="back-link mt-3" onclick="goLogin()">
            <i class='bx bx-arrow-back'></i> Back to Login Page
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>

    <script>
        (function () {
            'use strict'
            var form = document.getElementById('resetPasswordForm');

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();

                $('#confirmError').hide();
                form.classList.remove('was-validated');

                const password = $('#password').val().trim();
                const confirmPassword = $('#confirmPassword').val().trim();

                if (password.length < 6) {
                    form.classList.add('was-validated');
                    return;
                }

                if (password !== confirmPassword) {
                    $('#confirmError').show();
                    $('#confirmPassword').addClass('is-invalid');
                    return;
                } else {
                    $('#confirmPassword').removeClass('is-invalid');
                }

                Swal.fire({
                    title: 'กำลังบันทึก...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: '<?php echo site_url('/Login/reset_password_action'); ?>',
                    method: 'POST',
                    data: {
                        password: password,
                        confirmPassword: confirmPassword,
                        token: '<?php echo isset($token) ? $token : ''; ?>'
                    },
                    success: function (response) {
                        Swal.close();

                        let res;
                        try {
                            res = JSON.parse(response);
                        } catch (e) {
                            console.error("Response ไม่ใช่ JSON ที่ถูกต้อง:", response);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'รูปแบบข้อมูลจากเซิร์ฟเวอร์ไม่ถูกต้อง',
                            });
                            return;
                        }

						/*
						@author Natthanicha Pothiwat
						@Modify Date : 2025-06-28
						@Update : Alert
						*/
						if (res.success) {
								Swal.fire({
									icon: 'success',
									html: 'เปลี่ยนรหัสผ่านเรียบร้อย<br>(Password changed successfully.)<br><br>คุณสามารถเข้าสู่ระบบด้วยรหัสผ่านใหม่ได้ทันที<br>(You can now log in with your new password.)',
								}).then(() => {
									window.location.href = '<?php echo site_url('/Login'); ?>';
								});
							}
							else {
                            console.log(res);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: res.message || 'ไม่สามารถเปลี่ยนรหัสผ่านได้ กรุณาลองใหม่อีกครั้ง',
                            });
                        }
                    },
                    error: function () {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อกับระบบได้',
                        });
                    }
                });
            });
        })();


        function goLogin() {
            window.location.href = '<?php echo site_url('/Login'); ?>';
        }
    </script>
</body>

</html>
