<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Quên mật khẩu Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{asset('frontend/images/techstormlogo.png')}}" />

    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <link rel="stylesheet" href="{{asset('backend/css/bootstrap.min.css')}}">
    <link href="{{asset('backend/css/style.css')}}" rel='stylesheet' type='text/css' />
    <link href="{{asset('backend/css/style-responsive.css')}}" rel="stylesheet" />
    <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{{asset('backend/css/font.css')}}" type="text/css" />
    <link href="{{asset('backend/css/font-awesome.css')}}" rel="stylesheet">
    <script src="{{asset('backend/js/jquery2.0.3.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
</head>

<body>
    <div class="log-w3">
        <div class="w3layouts-main">
            <h2>Đổi mật khẩu</h2>
            <form id="forgotPassForm">
                @csrf

                <div class="input-wrapper" style="position:relative; width:100%; margin-bottom: 20px;">
                    <input type="password" class="ggg" name="new_password" id="new_password" placeholder="Nhập mật khẩu mới" style="padding-right: 40px; width: 100%;">
                    <span class="icon" id="new_pass" style="display:none;"><i class="fa fa-check"></i></span>
                    <span class="toggle-password"><i class="fa fa-eye" toggle="#new_password"></i></span>
                    <span class="input-error" id="new_password_error" style="display:none;color:red"></span>
                </div>

                <div class="input-wrapper" style="position:relative; width:100%; margin-bottom: 20px;">
                    <input type="password" class="ggg" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu mới" style="padding-right: 40px; width: 100%;">
                    <span class="icon" id="confirm_pass" style="display:none;"><i class="fa fa-check"></i></span>
                    <span class="toggle-password"><i class="fa fa-eye" toggle="#confirm_password"></i></span>
                    <span class="input-error" id="confirm_password_error" style="display:none;color:red"></span>
                </div>




                <div class="clearfix"></div>
                <input type="submit" id="confirmButton" value="Xác nhận" name="confirm">
            </form>

            <p id="alert-message" style="color: green; display: none;"></p>
            <p id="error-message" style="color: red; display: none;"></p>
            <a href="{{url('/admin-login')}}">Đăng nhập Admin</a>

        </div>
    </div>

    <script src="{{asset('backend/js/bootstrap.js')}}"></script>
    <script src="{{asset('backend/js/jquery.dcjqaccordion.2.7.js')}}"></script>
    <script src="{{asset('backend/js/scripts.js')}}"></script>
    <script src="{{asset('backend/js/jquery.slimscroll.js')}}"></script>
    <script src="{{asset('backend/js/jquery.nicescroll.js')}}"></script>
    <script src="{{asset('backend/js/jquery.scrollTo.js')}}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPassInput = document.getElementById('new_password');
            const confirmPassInput = document.getElementById('confirm_password');
            const newPassError = document.getElementById('new_password_error');
            const confirmPassError = document.getElementById('confirm_password_error');
            const newPassCheck = document.getElementById('new_pass');
            const confirmPassCheck = document.getElementById('confirm_pass');
            const form = document.getElementById('forgotPassForm');

            // Toggle eye
            document.querySelectorAll('.toggle-password').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const input = document.querySelector(icon.getAttribute('toggle'));
                    input.type = input.type === 'password' ? 'text' : 'password';
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            });

            // Hàm validate mật khẩu mới
            function validateNewPass() {
                const value = newPassInput.value.trim();
                if (value.length < 6) {
                    newPassError.textContent = 'Mật khẩu phải có ít nhất 6 ký tự';
                    newPassError.style.display = 'block';
                    newPassCheck.style.display = 'none';
                    return false;
                } else {
                    newPassError.style.display = 'none';
                    newPassCheck.style.display = 'inline';
                    return true;
                }
            }

            // Hàm validate xác nhận mật khẩu
            function validateConfirmPass() {
                const value = confirmPassInput.value.trim();
                if (value === '') {
                    confirmPassError.textContent = 'Vui lòng nhập lại mật khẩu';
                    confirmPassError.style.display = 'block';
                    confirmPassCheck.style.display = 'none';
                    return false;
                } else if (value !== newPassInput.value.trim()) {
                    confirmPassError.textContent = 'Mật khẩu không khớp';
                    confirmPassError.style.display = 'block';
                    confirmPassCheck.style.display = 'none';
                    return false;
                } else {
                    confirmPassError.style.display = 'none';
                    confirmPassCheck.style.display = 'inline';
                    return true;
                }
            }

            // Lắng nghe sự kiện nhập
            newPassInput.addEventListener('input', function() {
                validateNewPass();
                validateConfirmPass(); // nếu new_password đổi, confirm_password cũng cần kiểm tra lại
            });

            confirmPassInput.addEventListener('input', validateConfirmPass);

            // Submit form nếu hợp lệ
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateNewPass() && validateConfirmPass()) {

                    let admin_email = sessionStorage.getItem('admin_email');
                    let admin_otp_token = sessionStorage.getItem('admin_otp_token');
                    let new_pass = document.getElementById('new_password').value;
                    let new_pass_confirm = document.getElementById('confirm_password').value;
                    alert(new_pass + " " + new_pass_confirm);
                    fetch(`/api/admin-change-password`, {
                            method: "POST",
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Otp-Token': admin_otp_token, // Lấy OTP token từ sessionStorage
                            },
                            body: JSON.stringify({
                                admin_email: admin_email, // Lấy email người dùng từ sessionStorage
                                new_password: new_pass,
                                new_password_confirmation: new_pass_confirm
                            })
                        })
                        .then(res => res.json()) // Chuyển đổi phản hồi từ server thành JSON
                        .then(data => {
                            if (data.success) {
                                alert("Mật khẩu đã được đổi thành công!");
                                window.location.href = '/login'
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Có lỗi xảy ra:', error);
                            alert('Đã xảy ra lỗi khi thay đổi mật khẩu. Vui lòng thử lại!');
                        });

                }
            });
        });
    </script>




</body>
<style>
    input[type="password"]::-webkit-credentials-auto-fill-button,
    input[type="password"]::-webkit-clear-button {
        display: none !important;
    }

    input::-ms-reveal,
    input::-ms-clear {
        display: none;
    }

    .input-wrapper {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    /* Căn icon bên phải và nằm cùng hàng */
    .toggle-password {
        position: absolute;
        bottom: 20%;
        right: 20px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
        z-index: 2;
    }

    /* Icon fa-check nằm bên trái icon mắt */
    .input-wrapper .icon {
        position: absolute;
        bottom: 20%;
        right: 40px;
        /* Dịch sang trái một chút so với icon eye */
        transform: translateY(-50%);
        color: green;
        z-index: 2;
    }

    .toggle-password:hover {
        color: #333;
    }
</style>

</html>