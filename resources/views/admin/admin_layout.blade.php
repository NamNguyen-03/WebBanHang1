<!DOCTYPE html>

<head>
    <title>ADMIN-FULITEX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="Visitors Responsive web template, Bootstrap Web Templates, Flat Web Templates, Android Compatible web template, 
Smartphone Compatible web template, free webdesigns for Nokia, Samsung, LG, SonyEricsson, Motorola web design" />
    <link rel="icon" type="image/x-icon" href="{{asset('frontend/images/fulitex.png')}}" />
    <script type="application/x-javascript">
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <!-- bootstrap-css -->
    <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
    <!-- //bootstrap-css -->
    <!-- Custom CSS -->
    <link href="{{asset('backend/css/style.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/css/style-responsive.css')}}" rel="stylesheet" />
    <!-- font CSS -->
    <!-- <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel="stylesheet" type="text/css"> -->
    <!-- font-awesome icons -->
    <link rel="stylesheet" href="{{asset('backend/css/font.css')}}" type="text/css" />
    <link href="{{asset('backend/css/font-awesome.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('backend/css/morris.css')}}" type="text/css" />
    <!-- calendar -->
    <link rel="stylesheet" href="{{asset('backend/css/monthly.css')}}">
    <link rel="stylesheet" href="{{asset('backend/css/datatables.css')}}">
    <link rel="stylesheet" href="{{asset('backend/css/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/css/bootstrap-tagsinput.css')}}">
    <!-- //calendar -->
    <!-- //font-awesome icons -->
    <script src="{{asset('backend/js/jquery2.0.3.min.js')}}"></script>
    <script src="{{asset('backend/js/raphael-min.js')}}"></script>
    <script src="{{asset('backend/js/morris.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Tải jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Tải jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">



</head>

<body>
    <section id="container">
        <header class="header fixed-top clearfix">
            <div class="brand">
                <a href="{{URL::to('/admin')}}" class="logo">
                    ADMIN
                </a>
                <div class="sidebar-toggle-box">
                    <div class="fa fa-bars"></div>
                </div>
            </div>

            <div class="top-nav clearfix">
                <ul class="nav pull-right top-menu">

                    <li class="dropdown">
                        <a class="dropdown-toggle" href="#" id="dropdownMenu" style="cursor: pointer;">
                            <img alt="" id="admin-image" src="{{ asset('backend/images/user.jpg') }}" style="display: none;">
                            <span class="username" id="admin-name">Đang tải...</span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout" id="admin-logged-in" style="display: none;">
                            <li><a href="{{url('/admin/profile')}}"><i class="fa fa-suitcase"></i>Thông tin</a></li>
                            <li><a href="javascript:void(0);" onclick="logout();"><i class="fa fa-key"></i>Đăng xuất</a></li>
                        </ul>
                        <ul class="dropdown-menu extended logout" id="admin-logged-out" style="display: none;">
                            <li><a href="{{ url('admin-login') }}"><i class="fa fa-sign-in"></i> Đăng nhập</a></li>
                        </ul>
                    </li>



                </ul>
            </div>


        </header>


        <aside>
            <div id="sidebar" class="nav-collapse">
                <div class="leftside-navigation">
                    <ul class="sidebar-menu" id="nav-accordion">
                        <li>
                            <a class="active" href="{{URL::to('/admin/dashboard')}}">
                                <i class="fa fa-dashboard"></i>
                                <span>Tổng quan</span>
                            </a>
                        </li>
                        <!-- <li>
                            <a class="active" href="{{URL::to('/information')}}">
                                <i class="fa fa-info"></i>
                                <span>Thông tin website</span>
                            </a>
                        </li> -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-money-check"></i>
                                <span>Đơn hàng</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/orders')}}">Quản lý đơn hàng</a></li>

                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-ticket"></i>
                                <span>Mã giảm giá</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-coupon')}}">Thêm mã giảm giá</a></li>
                                <li><a href="{{URL::to('/admin/all-coupon')}}">Liệt kê mã giảm giá</a></li>


                            </ul>
                        </li>

                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-book"></i>
                                <span>Danh mục sản phẩm</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-category')}}">Thêm danh mục sản phẩm</a></li>
                                <li><a href="{{URL::to('/admin/all-category')}}">Liệt kê danh mục sản phẩm</a></li>

                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-book"></i>
                                <span>Thương hiệu</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-brand')}}">Thêm thương hiệu sản phẩm</a></li>
                                <li><a href="{{URL::to('/admin/all-brand')}}">Liệt kê thương hiệu sản phẩm</a></li>

                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-computer"></i>
                                <span>Sản phẩm</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-product')}}">Thêm sản phẩm</a></li>
                                <li><a href="{{URL::to('/admin/all-product')}}">Liệt kê sản phẩm</a></li>
                                <li><a href="{{URL::to('/admin/import-product')}}">Lịch sử nhập sản phẩm</a></li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-regular fa-flag"></i>
                                <span>Banner</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-banner')}}">Thêm banner</a></li>
                                <li><a href="{{URL::to('/admin/all-banner')}}">Danh sách banner</a></li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-newspaper"></i>
                                <span>Danh mục bài viết</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-post-cate')}}">Thêm danh mục bài viết</a></li>
                                <li><a href="{{URL::to('/admin/all-post-cate')}}">Danh sách danh mục bài viết</a></li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-newspaper"></i>
                                <span>Bài viết</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-post')}}">Thêm bài viết</a></li>
                                <li><a href="{{URL::to('/admin/all-post')}}">Danh sách bài viết</a></li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-video"></i>
                                <span>Videos</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/add-video')}}">Thêm video</a></li>
                                <li><a href="{{URL::to('/admin/all-videos')}}">Videos</a></li>

                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-commenting"></i>
                                <span>Comment</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/all-comment')}}">Duyệt comment</a></li>
                            </ul>
                        </li>
                        <!-- <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa fa-phone"></i>
                                <span>Contact</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/all-contact')}}">Lời nhắn từ khách hàng</a></li>
                            </ul>
                        </li> -->
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-percent"></i>
                                <span>Email đăng kí nhận khuyến mãi</span>
                            </a>
                            <ul class="sub">
                                <li><a href="{{URL::to('/admin/all-promotion-content')}}">Nội dung email khuyến mãi</a></li>
                                <li><a href="{{URL::to('/admin/promotion-email')}}">Các email đăng kí nhận khuyến mãi</a></li>
                                <li><a href="{{URL::to('/admin/add-promotion-content')}}">Thêm nội dung email khuyến mãi</a></li>
                            </ul>
                        </li>
                        <li class="sub-menu">
                            <a href="javascript:;">
                                <i class="fa-solid fa-user"></i> <span>Users</span>
                            </a>
                            <ul class="sub">
                                <!-- <li><a href="{{URL::to('/admin/add-users')}}">Thêm user</a></li> -->
                                <li><a href="{{URL::to('/admin/all-users')}}">Liệt kê user</a></li>

                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        <section id="main-content">
            <section class="wrapper">
                @yield('admin_content')
            </section>
            <div class="footer">
                <div class="wthree-copyright">
                    <p>© 2025 Visitors. All rights reserved | Design by <a href="http://w3layouts.com">NamBup</a></p>
                </div>
            </div>
        </section>
    </section>
    <script src="{{asset('backend/js/bootstrap.js')}}"></script>
    <script src="{{asset('backend/js/jquery.dcjqaccordion.2.7.js')}}"></script>
    <script src="{{asset('backend/js/scripts.js')}}"></script>
    <script src="{{asset('backend/js/jquery.slimscroll.js')}}"></script>
    <script src="{{asset('backend/js/jquery.nicescroll.js')}}"></script>
    <script src="{{asset('backend/js/datatables.min.js')}}"></script>
    <script src="{{asset('backend/js/datatables.js')}}"></script>
    <script src="{{asset('backend/js/bootstrap-tagsinput.min.js')}}"></script>
    <script src="{{asset('backend/js/jquery.scrollTo.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script src="{{asset('backend/js/simple.money.format.js')}}"></script>
    <script src="{{asset('backend/js/monthpicker.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- <script src="{{asset('public/backend/js/jquery.form-validator.min.js')}}"></script> -->
    <script>
        // CKEDITOR.replace('product_desc', {
        //     filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        //     filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        //     filebrowserUploadMethod: 'form'
        // });
        // CKEDITOR.replace('product_content', {
        //     filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        //     filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        //     filebrowserUploadMethod: 'form'
        // });
        // CKEDITOR.replace('category_desc', {
        //     filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        //     filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        //     filebrowserUploadMethod: 'form'
        // });
        // CKEDITOR.replace('brand_desc', {
        //     filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        //     filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        //     filebrowserUploadMethod: 'form'
        // });

        // CKEDITOR.replace('info_contact', {
        //     filebrowserImageUploadUrl: "{{url('uploads-ckeditor?_token-'.csrf_token())}}",
        //     filebrowserBrowseUrl: "{{url('file-browser?_token-'.csrf_token())}}",
        //     filebrowserUploadMethod: 'form'
        // });
    </script>




</body>
<script>
    const adminId = localStorage.getItem("admin_id");
    const adminTokenRaw = localStorage.getItem("admin_token");
    const adminToken = atob(adminTokenRaw);
    document.addEventListener("DOMContentLoaded", function() {



        if (adminId && adminToken) {
            document.getElementById("admin-logged-in").style.display = "block";
            document.getElementById("admin-logged-out").style.display = "none";
            document.getElementById("admin-logged-in").style.display = "none";
            document.getElementById("admin-image").style.display = "inline";
            document.getElementById("admin-name").style.display = "inline";

            fetch("{{ url('/api/admins') }}/" + adminId, {
                    headers: {
                        "Authorization": "Bearer " + adminToken,
                        "Accept": "application/json"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById("admin-name").innerText = data.data.admin_name || "Quản trị viên";

                })
                .catch(err => {
                    console.warn("Không thể lấy tên admin:", err);
                    document.getElementById("admin-name").innerText = "Quản trị viên";
                });

        } else {
            document.getElementById("admin-logged-in").style.display = "none";
            document.getElementById("admin-logged-out").style.display = "block";
            document.getElementById("admin-name").innerText = "";
            document.getElementById("admin-image").style.display = "none";
        }

        const dropdown = document.getElementById("dropdownMenu");
        const dropdownMenu = document.getElementById("admin-logged-in");
        dropdown.addEventListener("click", function(event) {
            event.preventDefault();
            dropdownMenu.style.display = dropdownMenu.style.display === "none" ? "block" : "none";
        });
    });



    function logout() {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        fetch("{{ url('/api/admin-logout') }}", {
                method: "POST",
                headers: {
                    "Authorization": "Bearer " + adminToken,
                    "Accept": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log("Logout:", data.message || "Đã đăng xuất");

                // Xóa localStorage
                localStorage.removeItem("admin_id");
                localStorage.removeItem("admin_token");

                alert("Đã đăng xuất!");
                window.location.href = "{{ url('admin-login') }}";
            })
            .catch(error => {
                console.error("Lỗi khi đăng xuất:", error);



                window.location.href = "{{ url('admin-login') }}";
            });
    }
</script>

@yield('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof CKEDITOR !== 'undefined') {
            for (var instance in CKEDITOR.instances) {
                if (CKEDITOR.instances.hasOwnProperty(instance)) {
                    CKEDITOR.instances[instance].config.versionCheck = false;
                }
            }
        }
    });
</script>
<script type="text/javascript">
    function ChangeToSlug() {
        var slug;

        slug = document.getElementById("slug").value;
        slug = slug.toLowerCase();
        slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
        slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
        slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
        slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
        slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
        slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
        slug = slug.replace(/đ/gi, 'd');
        slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
        slug = slug.replace(/ /gi, "-");
        slug = slug.replace(/\-\-\-\-\-/gi, '-');
        slug = slug.replace(/\-\-\-\-/gi, '-');
        slug = slug.replace(/\-\-\-/gi, '-');
        slug = slug.replace(/\-\-/gi, '-');
        slug = '@' + slug + '@';
        slug = slug.replace(/\@\-|\-\@|\@/gi, '');
        document.getElementById('convert_slug').value = slug;
    }
    $(function() {
        $("#datepicker").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });
    $(function() {
        $("#datepicker2").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });


    $(document).ready(function() {
        $('.product_price_in').simpleMoneyFormat();
        $('.modal-price-in').simpleMoneyFormat();
        $('.product_price').simpleMoneyFormat();
    });
</script>

</html>