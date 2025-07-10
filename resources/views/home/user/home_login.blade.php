@extends('home.home_layout')
@section('content')

<style>
    .auth-container {
        max-width: 500px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .auth-title {
        text-align: center;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .auth-form input {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 15px;
    }

    .auth-form button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 6px;
        background-color: #007bff;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .auth-form button:hover {
        background-color: #0056b3;
    }

    .auth-extra {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        margin-bottom: 15px;
    }

    .auth-extra a {
        color: #007bff;
        text-decoration: none;
    }

    .divider {
        text-align: center;
        margin: 30px 0 20px;
        position: relative;
    }

    .divider::before,
    .divider::after {
        content: "";
        position: absolute;
        height: 1px;
        width: 45%;
        background: #ddd;
        top: 50%;
    }

    .divider::before {
        left: 0;
    }

    .divider::after {
        right: 0;
    }

    .divider span {
        background: #fff;
        padding: 0 10px;
        color: #666;
    }

    .social-login {
        text-align: center;
        margin-top: 10px;
    }

    .g_id_signin {
        display: inline-block;
        margin-top: 10px;
    }

    #register-message {
        text-align: center;
        color: red;
        margin-top: 10px;
    }
</style>

<section id="form">
    <div class="auth-container">

        <!-- Login Form -->
        <div class="auth-form">
            <div class="auth-title">Đăng nhập vào tài khoản</div>
            <form id="login-form">
                {{ csrf_field() }}
                <input type="text" required title="Vui lòng nhập tài khoản" name="email" placeholder="Tài khoản" />
                <input type="password" required title="Vui lòng nhập mật khẩu" name="password" placeholder="Password" />
                <div class="auth-extra">
                    <label>
                        <input type="checkbox" class="checkbox" id="remember_me"> Nhớ đăng nhập
                    </label>
                    <a href="{{ url('/forgot-password') }}">Quên mật khẩu?</a>
                </div>
                <button type="submit">Đăng nhập</button>
            </form>

            <div class="social-login">
                <div class="divider"><span>Hoặc đăng nhập bằng</span></div>
                <div id="g_id_onload"
                    data-client_id="{{ env('GOOGLE_CLIENT_ID') }}"
                    data-callback="handleGoogleCredentialResponse"
                    data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                    data-type="standard"
                    data-theme="outline"
                    data-size="large"
                    data-logo_alignment="center">
                </div>
            </div>
        </div>

        <!-- Register Form -->
        <div class="auth-form" style="margin-top: 40px;">
            <div class="auth-title">Tạo tài khoản mới</div>
            <form id="register-form">
                {{ csrf_field() }}
                <input type="text" id="customer_name" required placeholder="Họ và tên" />
                <input type="email" id="customer_email" required placeholder="Email" />
                <input type="text" id="customer_phone" required pattern="^\d{10,11}$" placeholder="Phone" />
                <input type="password" id="customer_password" minlength="6" required placeholder="Mật khẩu" />
                <button type="submit">Đăng ký</button>
            </form>
            <p id="register-message"></p>
        </div>

    </div>
</section>


<script>
    document.getElementById("register-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Ngăn chặn reload trang

        let data = {
            name: document.getElementById("customer_name").value,
            email: document.getElementById("customer_email").value,
            password: document.getElementById("customer_password").value,
            phone: document.getElementById("customer_phone").value
        };

        fetch(`{{ url('/api/users') }}`, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                const messageEl = document.getElementById("register-message");

                if (data.success) {
                    messageEl.style.color = "green";
                    messageEl.innerText = "Đăng ký thành công! Vui lòng đăng nhập.";
                } else {
                    messageEl.style.color = "red";

                    if (typeof data.message === 'object') {
                        let errors = [];
                        for (let key in data.message) {
                            if (data.message.hasOwnProperty(key)) {
                                errors.push(...data.message[key]);
                            }
                        }
                        messageEl.innerHTML = errors.map(e => `<div>${e}</div>`).join('');
                    } else {
                        messageEl.innerText = data.message || "Đăng ký thất bại!";
                    }
                }
            })

            .catch(error => {
                document.getElementById("register-message").innerText = "Lỗi hệ thống, vui lòng thử lại sau!";
            });
    });

    document.getElementById("login-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Ngăn chặn reload trang

        let email = document.querySelector("input[name='email']").value;
        let password = document.querySelector("input[name='password']").value;
        let remember = document.getElementById('remember_me').checked;
        fetch("{{ url('/api/login') }}", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember_me: remember
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const storage = remember ? localStorage : sessionStorage;
                    const encodedToken = btoa(data.token); // mã hóa
                    storage.setItem("auth_token", encodedToken);
                    storage.setItem("user_id", data.user.id);
                    storage.setItem("user_email", data.user.email);
                    storage.setItem("user_name", data.user.name);

                    alert("Đăng nhập thành công!");
                    window.location.href = "{{ url('/') }}"; // Chuyển hướng về trang chính
                } else {
                    alert(data.message || "Đăng nhập thất bại!");
                }
            })
            .catch(error => {
                alert("Lỗi hệ thống, vui lòng thử lại sau!");
                console.error("Login error:", error);
            });
    });

    function handleGoogleCredentialResponse(response) {
        const googleToken = response.credential;

        fetch("/api/login/google/callback", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_token: googleToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem("auth_token", btoa(data.token));
                    localStorage.setItem("user_id", data.user.id);
                    localStorage.setItem("user_email", data.user.email);
                    localStorage.setItem("user_name", data.user.name);

                    alert("Đăng nhập Google thành công!");
                    window.location.href = "/";
                } else {
                    alert(data.message || "Đăng nhập thất bại!");
                }
            })
            .catch(error => {
                alert("Lỗi hệ thống, vui lòng thử lại sau!");
                console.error("Google login error:", error);
            });
    }


    window.fbAsyncInit = function() {
        FB.init({
            appId: '{{ env("FACEBOOK_CLIENT_ID") }}',
            cookie: true,
            xfbml: false,
            version: 'v19.0'
        });
    };

    // document.getElementById("facebookLoginBtn").addEventListener("click", function() {
    //     FB.login(function(response) {
    //         if (response.authResponse) {
    //             const accessToken = response.authResponse.accessToken;

    //             fetch("{{ url('/api/login/facebook') }}", {
    //                     method: "POST",
    //                     headers: {
    //                         "Content-Type": "application/json",
    //                         "Accept": "application/json"
    //                     },
    //                     body: JSON.stringify({
    //                         access_token: accessToken
    //                     })
    //                 })
    //                 .then(res => res.json())
    //                 .then(data => {
    //                     if (data.token) {
    //                         localStorage.setItem("auth_token", data.token);
    //                         localStorage.setItem("user_id", data.user.id);
    //                         localStorage.setItem("user_email", data.user.email);
    //                         localStorage.setItem("user_name", data.user.name);
    //                         alert("Đăng nhập Facebook thành công!");
    //                         window.location.href = "/";
    //                     } else {
    //                         alert("Đăng nhập thất bại!");
    //                     }
    //                 })
    //                 .catch(err => {
    //                     console.error("Facebook login error:", err);
    //                     alert("Có lỗi xảy ra khi đăng nhập.");
    //                 });
    //         } else {
    //             alert("Bạn chưa cho phép đăng nhập.");
    //         }
    //     }, {
    //         scope: 'email,public_profile'
    //     });
    // });
</script>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script async defer crossorigin="anonymous"
    src="https://connect.facebook.net/en_US/sdk.js"></script>

@endsection