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
            <h2>Quên mật khẩu Admin</h2>
            <form id="forgotPassForm">
                @csrf
                <div style="position: relative; width: 100%;">
                    <input type="text" class="ggg" name="admin_email" id="admin_email" placeholder="Nhập email" style="padding-right: 100px; width: 100%;">
                    <a href="javascript:void(0)" id="sendCodeBtn" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%);  background-color:none;color:blue;">Gửi mã</a>
                </div>
                <input type="text" class="ggg" name="verify_code" id="verify_code" placeholder="Mã xác nhận" style="display: none;">
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
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            const emailInput = document.getElementById('admin_email');
            const verifyInput = document.getElementById('verify_code');
            const message = document.getElementById('message');

            sendCodeBtn.addEventListener('click', function() {
                const email = emailInput.value.trim();
                if (!email) {
                    showError('Vui lòng nhập email')
                    return;
                }
                fetch('/api/admin-send-otp', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then(data => {
                        verifyInput.style.display = 'block';
                        showAlert('Mã xác nhận đã được gửi đến email.')

                    })
                    .catch(async (error) => {
                        let errorText = 'Gửi mã thất bại.';
                        try {
                            const errData = await error.json();
                            errorText = errData.message || errorText;
                        } catch {}
                        showError(errorText);
                    });
            });
        });

        document.getElementById("forgotPassForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn reload trang
            let confirmButton = document.getElementById('confirmButton');
            let code = document.getElementById('verify_code').value.trim();
            let email = document.getElementById("admin_email").value.trim();

            // Hiển thị lỗi nếu thiếu thông tin
            if (!email) {
                showError("Vui lòng điền đầy đủ thông tin!");
                return;
            }

            console.log(email);
            fetch('/api/admin-verify-otp', {
                    method: 'POST',
                    headers: {
                        "Accept": "application/json",
                        "Content-Type": "application/json",

                    },
                    body: JSON.stringify({
                        email: email,
                        otp: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Xác nhận thành công')
                        sessionStorage.setItem('admin_otp_token', data.otp_admin_token);
                        sessionStorage.setItem('admin_email', email);
                        window.location.href = '/admin-change-pass';

                    } else {
                        showError('Xác nhận thất bại')
                    }

                })
                .catch(async (error) => {
                    const errData = await error.json();
                    showError('Sai mã xác nhận')
                });
        });

        function showError(message) {
            const errorBox = document.getElementById("error-message");
            errorBox.innerText = message;
            errorBox.style.display = "block";
        }

        function showAlert(message) {
            const errorBox = document.getElementById("alert-message");
            errorBox.innerText = message;
            errorBox.style.display = "block";
        }
    </script>



</body>

</html>