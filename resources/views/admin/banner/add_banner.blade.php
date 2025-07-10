@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ url('/admin/dashboard') }}">
                    <img src="{{ asset('backend/images/back.png') }}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{ url('/admin/all-banner') }}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách Banners
                </a>
                Thêm Banner
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="addBannerForm" role="form" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="banner_name">Tên Banner</label>
                            <input type="text" name="banner_name" class="form-control" id="banner_name" placeholder="Tên Banner" required>
                        </div>
                        <div class="form-group">
                            <label for="banner_image">Hình ảnh</label>
                            <input type="file" name="banner_image" class="form-control" id="banner_image" required>
                        </div>
                        <div class="form-group">
                            <label for="banner_desc">Mô tả Banner</label>
                            <textarea style="resize:none" rows="5" class="form-control" name="banner_desc" id="banner_desc" placeholder="Mô tả Banner" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="banner_status">Hiển thị</label>
                            <select name="banner_status" class="form-control input-sm m-bot15" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Thêm Banner</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.getElementById("addBannerForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let formData = new FormData(this);
        const adminTokenRaw = localStorage.getItem("admin_token");

        let adminToken = atob(localStorage.getItem("admin_token"));

        if (!adminTokenRaw) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch("{{ url('/api/banners') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "Authorization": "Bearer " + adminToken
                }
            })
            .then(response => {
                if (response.status === 401) {
                    alert("Chưa đăng nhập, vui lòng đăng nhập!");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert("Thêm banner thành công");
                    window.location.href = "{{ url('/admin/all-banner') }}";
                } else {
                    alert("Có lỗi xảy ra: " + (data.message || "Vui lòng thử lại."));
                }
            })
            .catch(error => {
                console.error("Lỗi khi thêm banner:", error);
                alert("Đã có lỗi xảy ra, vui lòng thử lại!");
            });
    });
</script>

@endsection